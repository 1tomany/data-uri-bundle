<?php

namespace OneToMany\DataUriBundle\Serializer;

use OneToMany\DataUri\Contract\Record\DataUriInterface;
use OneToMany\DataUri\DataDecoder;
use OneToMany\DataUri\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

use function count;
use function filter_var;
use function is_a;
use function is_array;
use function is_string;
use function str_starts_with;
use function stripos;

use const FILTER_VALIDATE_URL;

final readonly class DataUriNormalizer implements DenormalizerInterface
{
    /**
     * @see Symfony\Component\Serializer\Normalizer\DenormalizerInterface
     *
     * @param string|File $data
     * @param array<string, mixed> $context
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): DataUriInterface
    {
        if ($data instanceof UploadedFile) {
            if (!$data->isValid()) {
                throw new InvalidArgumentException($data->getErrorMessage());
            }

            $name = $data->getClientOriginalName();
        }

        if (is_string($data)) {
            // @see https://github.com/1tomany/rich-bundle/issues/66
            $isHttpUrl = false !== filter_var($data, FILTER_VALIDATE_URL) && 0 === stripos($data, 'http');

            // The data is not an HTTP URL or a "data:" URI, so
            // the best we can do is assume it's a block of text
            if (!$isHttpUrl && !str_starts_with($data, 'data:')) {
                return new DataDecoder()->decodeText($data, null);
            }
        }

        return new DataDecoder()->decode($data, $name ?? null);
    }

    /**
     * @see Symfony\Component\Serializer\Normalizer\DenormalizerInterface
     *
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        $isSupported = false;

        if (is_a($type, DataUriInterface::class, true)) {
            if (is_string($data)) {
                $isSupported = true;
            }

            if ($data instanceof File) {
                $isSupported = true;
            }

            // @see https://github.com/1tomany/data-uri-bundle/issues/1
            if (is_array($data) && ($dataCount = count($data)) > 0) {
                $supportedRecords = 0;

                foreach ($data as $dv) {
                    if (is_string($dv)) {
                        ++$supportedRecords;
                    }

                    if ($dv instanceof File) {
                        ++$supportedRecords;
                    }
                }

                $isSupported = $dataCount === $supportedRecords;
            }
        }

        return $isSupported;
    }

    /**
     * @see Symfony\Component\Serializer\Normalizer\DenormalizerInterface
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            DataUriInterface::class => true,
        ];
    }
}
