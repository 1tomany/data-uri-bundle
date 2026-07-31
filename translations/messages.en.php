<?php

use OneToMany\DataUri\Contract\Enum\FileType;

$fileTypeNames = array_map(static fn ($ft) => $ft->getName(), FileType::cases());

return array_combine($fileTypeNames, $fileTypeNames);
