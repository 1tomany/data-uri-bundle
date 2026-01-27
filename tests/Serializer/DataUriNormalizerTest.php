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

    public function testDenormalizingUploadedFileUsesClientOriginalName(): void
    {
        $file = new DataUriNormalizer()->denormalize(new UploadedFile(__FILE__, basename(__FILE__), 'text/x-php', test: true), DataUriInterface::class);

        $this->assertEquals('DataUriNormalizerTest.php', $file->getName());
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

    public function testSupportsNormalizationWithStringAndDataUriInterface(): void
    {
        $this->assertTrue(new DataUriNormalizer()->supportsDenormalization('Hello, world!', DataUriInterface::class));
    }

    public function testSupportsNormalizationWithFileAndDataUriInterface(): void
    {
        $this->assertTrue(new DataUriNormalizer()->supportsDenormalization(new File('/path/to/file.pdf', false), DataUriInterface::class));
    }
}
