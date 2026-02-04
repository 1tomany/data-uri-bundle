<?php

namespace OneToMany\DataUriBundle\Tests\Serializer;

use OneToMany\DataUri\Contract\Record\DataUriInterface;
use OneToMany\DataUri\Exception\InvalidArgumentException;
use OneToMany\DataUriBundle\Serializer\DataUriNormalizer;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use function basename;

use const UPLOAD_ERR_PARTIAL;

#[Group('UnitTests')]
#[Group('SerializerTests')]
final class DataUriNormalizerTest extends TestCase
{
    public function testDenormalizingUploadedFileRequiresItToBeValid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The file "photo.jpeg" was only partially uploaded.');

        new DataUriNormalizer()->denormalize(new UploadedFile('/path/to/photo.jpeg', 'photo.jpeg', 'image/jpeg', UPLOAD_ERR_PARTIAL, true), DataUriInterface::class);
    }

    public function testDenormalizingFileUsesFilename(): void
    {
        $file = new DataUriNormalizer()->denormalize(new File(__FILE__), DataUriInterface::class);

        $this->assertEquals('DataUriNormalizerTest.php', $file->getName());
    }

    public function testDenormalizingUploadedFileUsesClientOriginalName(): void
    {
        $file = new DataUriNormalizer()->denormalize(new UploadedFile(__FILE__, basename(__FILE__), 'text/x-php', test: true), DataUriInterface::class);

        $this->assertEquals('DataUriNormalizerTest.php', $file->getName());
    }

    public function testDenormalizingStringableNonSymfonyFileObject(): void
    {
        $data = new class('Hello, world!') implements \Stringable
        {
            public function __construct(public string $data)
            {
            }

            public function __toString(): string
            {
                return \sprintf('data:text/plain;base64,%s', \base64_encode($this->data));
            }
        };

        $file = new DataUriNormalizer()->denormalize($data, DataUriInterface::class);

        $this->assertTrue($file->getType()->isTxt());
        $this->assertEquals('Hello, world!', $file->read());
    }

    public function testDenormalizingRawText(): void
    {
        $file = new DataUriNormalizer()->denormalize('Hello, world!', DataUriInterface::class);

        $this->assertTrue($file->getType()->isTxt());
        $this->assertEquals('Hello, world!', $file->read());
    }

    public function testDenormalizingDataUri(): void
    {
        $file = new DataUriNormalizer()->denormalize('data:text/plain;base64,SGVsbG8sIHdvcmxkIQ==', DataUriInterface::class);

        $this->assertTrue($file->getType()->isTxt());
        $this->assertEquals('Hello, world!', $file->read());
    }

    public function testDoesNotSupportNormalizationWithNonStringAndNonSymfonyFileDataAndDataUriInterface(): void
    {
        $this->assertFalse(new DataUriNormalizer()->supportsDenormalization(new \stdClass(), DataUriInterface::class));
    }

    public function testSupportsNormalizationWithStringDataAndDataUriInterfaceType(): void
    {
        $this->assertTrue(new DataUriNormalizer()->supportsDenormalization('Hello, world!', DataUriInterface::class));
    }

    public function testSupportsNormalizationWithSymfonyFileDataAndDataUriInterfaceType(): void
    {
        $this->assertTrue(new DataUriNormalizer()->supportsDenormalization(new File('file.pdf', false), DataUriInterface::class));
    }

    public function testDoesNotSupportNormalizationWithEmptyListDataAndDataUriInterfaceType(): void
    {
        $this->assertFalse(new DataUriNormalizer()->supportsDenormalization([], DataUriInterface::class));
    }

    public function testSupportsNormalizationWithNonEmptyListOfStringsDataAndDataUriInterfaceType(): void
    {
        $this->assertTrue(new DataUriNormalizer()->supportsDenormalization(['Hello, world!'], DataUriInterface::class));
    }

    public function testSupportsNormalizationWithNonEmptyListOfSymfonyFileDataAndDataUriInterfaceType(): void
    {
        $this->assertTrue(new DataUriNormalizer()->supportsDenormalization([new File('file.pdf', false)], DataUriInterface::class));
    }

    public function testSupportsNormalizationWithNonEmptyListOfStringAndSymfonyFileDataAndDataUriInterfaceType(): void
    {
        $data = [
            'Hello, world!',
            new File('file.pdf', false),
            'data:text/plain;base64,SGVsbG8sIHdvcmxkIQ==',
        ];

        $this->assertTrue(new DataUriNormalizer()->supportsDenormalization($data, DataUriInterface::class));
    }
}
