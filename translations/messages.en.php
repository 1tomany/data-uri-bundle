<?php

use OneToMany\DataUri\Contract\Enum\Type;

// Map the file type onto itself to avoid having to manually maintain this list
$fileTypeNames = \array_map(fn (Type $t): string => $t->getName(), Type::cases());

return \array_combine($fileTypeNames, $fileTypeNames);
