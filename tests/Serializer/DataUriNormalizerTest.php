<?php

namespace OneToMany\DataUriBundle\Tests\Serializer;

use OneToMany\DataUri\Contract\Record\DataUriInterface;
use OneToMany\DataUri\Exception\InvalidArgumentException;
use OneToMany\DataUriBundle\Serializer\DataUriNormalizer;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
}
