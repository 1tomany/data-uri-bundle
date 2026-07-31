<?php

use OneToMany\DataUri\Contract\Enum\FileType;

$mapper = static fn (FileType $ft): string => $ft->getName();

/**
 * @var non-empty-list<non-empty-string> $fileTypeNames
 */
$fileTypeNames = array_map($mapper, FileType::cases());

return array_combine($fileTypeNames, $fileTypeNames);
