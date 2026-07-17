<?php

use OneToMany\DataUri\Contract\Enum\Type;

/**
 * Map the file type onto itself to avoid having
 * to maintain this list for each new file type.
 *
 * @var non-empty-list<non-empty-string> $names
 */
$names = \array_map(static function (Type $type): string {
    return $type->getName();
}, Type::cases());

return \array_combine($names, $names);
