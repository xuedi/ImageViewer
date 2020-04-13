<?php declare(strict_types=1);

namespace ImageViewer;

use RuntimeException;

trait Ensure
{
    public static function ensureParameter(array $parameter, array $expectedFields): void
    {
        foreach ($expectedFields as $fieldName) {
            if (!isset($parameter[(string)$fieldName])) {
                throw new RuntimeException("Missing argument '$fieldName'");
            }
        }
    }

    // use cant be casted, instead of is not type (typechecking will be done my the constructors)
    public static function ensureInteger(array $parameter, string $field): void
    {
        if (!is_int($parameter[$field])) {
            throw new RuntimeException("Field '$field' is not of type integer");
        }
    }

    // use cant be casted, instead of is not type (typechecking will be done my the constructors)
    public static function ensureString(array $parameter, string $field): void
    {
        if (!is_string($parameter[$field])) {
            throw new RuntimeException("Field '$field' is not of type string");
        }
    }
}
