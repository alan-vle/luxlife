<?php

namespace App\Utils;

use Symfony\Component\CssSelector\Exception\InternalErrorException;

class EnumUtils
{
    /**
     * Normalize enum name : first letter in uppercase, the rest in lowercase and replace underscores by space.
     * And check if enum is a valid object and property name exists.
     */
    public static function nameNormalizer(mixed $enum): string
    {
        if (!is_object($enum)) {
            throw new InternalErrorException();
        }

        $enum = (object) $enum;

        if (!property_exists($enum, 'name')) {
            throw new InternalErrorException();
        }

        return ucfirst(strtolower(str_replace('_', ' ', $enum->name)));
    }
}
