<?php

namespace OneToMany\DataUriBundle\Serializer;

use OneToMany\DataUri\Contract\Record\TemporaryFileInterface;
use OneToMany\DataUri\DataDecoder;
use OneToMany\DataUri\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function count;
use function filter_var;
use function get_debug_type;
use function is_a;
use function is_array;
use function is_scalar;
use function is_string;
use function sprintf;
use function stripos;

use const FILTER_VALIDATE_URL;

final readonly class TemporaryFileNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function __construct(
        private DataDecoder $dataDecoder = new DataDecoder(),
    ) {
    }

    /**
     * @see Symfony\Component\Serializer\Normalizer\DenormalizerInterface
     *
     * @param string|\Stringable|File $data
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): TemporaryFileInterface
    {
        if ($data instanceof File) {
            if ($data instanceof UploadedFile) {
                if (!$data->isValid()) {
                    throw new InvalidArgumentException($data->getErrorMessage());
                }

                $name = $data->getClientOriginalName();
            }

            return $this->dataDecoder->decode($data, name: $name ?? $data->getFilename());
        }

        if ($data instanceof \Stringable) {
            $data = $data->__toString();
        }

        if (!is_string($data)) { // @phpstan-ignore function.alreadyNarrowedType
            throw new InvalidArgumentException(sprintf('Expected data of type "%s", "%s" given.', 'string', get_debug_type($data)));
        }

        // The data is not a URL or an encoded URI,
        // so we assume it is a block of plaintext
        if (true === $this->isPlaintextData($data)) {
            return $this->dataDecoder->decodeText($data);
        }

        return $this->dataDecoder->decode($data);
    }

    /**
     * @see Symfony\Component\Serializer\Normalizer\NormalizerInterface
     *
     * @param TemporaryFileInterface $data
     *
     * @return array{
     *   name: non-empty-string,
     *   size: non-negative-int,
     *   format: non-empty-lowercase-string,
     * }
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        if (!$data instanceof TemporaryFileInterface) { // @phpstan-ignore instanceof.alwaysTrue
            throw new InvalidArgumentException(sprintf('Expected data of type "%s", "%s" given.', TemporaryFileInterface::class, get_debug_type($data)));
        }

        return [
            'name' => $data->getName(),
            'size' => $data->getSize(),
            'format' => $data->getFormat(),
        ];
    }

    /**
     * @see Symfony\Component\Serializer\Normalizer\DenormalizerInterface
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        $isValueSupported = false;

        if (is_a($type, TemporaryFileInterface::class, true)) {
            if ($this->isValueSupported($data)) {
                $isValueSupported = true;
            } else {
                // @see https://github.com/1tomany/data-uri-bundle/issues/1
                if (is_array($data) && ($dataCount = count($data)) > 0) {
                    $supportedRecords = 0;

                    foreach ($data as $dv) {
                        if ($this->isValueSupported($dv)) {
                            ++$supportedRecords;
                        }
                    }

                    $isValueSupported = $dataCount === $supportedRecords;
                }
            }
        }

        return $isValueSupported;
    }

    /**
     * @see Symfony\Component\Serializer\Normalizer\NormalizerInterface
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof TemporaryFileInterface;
    }

    /**
     * @see Symfony\Component\Serializer\Normalizer\DenormalizerInterface
     * @see Symfony\Component\Serializer\Normalizer\NormalizerInterface
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            TemporaryFileInterface::class => true,
        ];
    }

    private function isPlaintextData(string $data): bool
    {
        // @see https://github.com/1tomany/rich-bundle/issues/66
        $isHttpUrl = false !== filter_var($data, FILTER_VALIDATE_URL) && 0 === stripos($data, 'http');

        if ($isHttpUrl) {
            return false;
        }

        return 0 !== stripos($data, 'data:');
    }

    private function isValueSupported(mixed $value): bool
    {
        return is_string($value) || $value instanceof File || $value instanceof \Stringable;
    }
}
