<?php

namespace OneToMany\DataUriBundle\Tests\Translation;

use OneToMany\DataUri\Contract\Enum\FileType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function realpath;

#[Group('UnitTests')]
#[Group('TranslationTests')]
final class FileTypeNameMapperTest extends TestCase
{
    #[DataProvider('providerMessagesFilePath')]
    public function testFileTypeNameIsMappedOntoItself(string $filePath): void
    {
        $messages = require_once realpath($filePath);

        $this->assertIsArray($messages);
        $this->assertNotCount(0, $messages);

        foreach (FileType::cases() as $fileType) {
            $this->assertArrayHasKey($fileType->getName(), $messages);
            $this->assertEquals($fileType->getName(), $messages[$fileType->getName()]);
        }
    }

    /**
     * @return non-empty-list<non-empty-list<non-empty-string>>
     */
    public static function providerMessagesFilePath(): array
    {
        $provider = [
            [__DIR__.'/../../translations/messages.en.php'],
        ];

        return $provider;
    }
}
