<?php

namespace App\Service;

enum EnumUtils
{
    /**
     * Enum name normalized. First letter in uppercase, the rest in lowercase and replace underscores by space.
     */
    public static function normalizedEnumName(mixed $enum): string
    {
        if (!is_object($enum)) {
            HttpUtils::throw400HTTPError();
        }

        $enum = (object) $enum;

        if (!property_exists($enum, 'name')) {
            HttpUtils::throw400HTTPError();
        }

        return ucfirst(strtolower(str_replace('_', ' ', $enum->name)));
    }
}
