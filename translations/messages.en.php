<?php

use OneToMany\DataUri\Contract\Enum\FileType;

$messages = [];

foreach (FileType::cases() as $fileType) {
    $messages[$fileType->getName()] = $fileType->getName();
}

return $messages;
