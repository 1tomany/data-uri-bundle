<?php

use OneToMany\DataUri\Contract\Enum\FileType;

/**
 * @var array<array-key, scalar>
 */
$messages = [];

foreach (FileType::cases() as $fileType) {
    $messages[$fileType->getName()] = $fileType->getName();
}

return $messages;
