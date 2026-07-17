<?php

use OneToMany\DataUri\Contract\Enum\Type;

// Map the file type onto itself to avoid having to
// maintain this list every time a new type is added
$fileTypeNames = \array_map(function (Type $type): string {
    return $type->getName();
}, Type::cases());

return \array_combine($fileTypeNames, $fileTypeNames);
