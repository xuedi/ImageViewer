<?php declare(strict_types=1);

trait DtoTypes
{
    public static function ensureParameter(array $parameter, array $expectedFields): void
    {
        foreach ($expectedFields as $fieldName) {
            if (!isset($parameter[$fieldName])) {
                throw new RuntimeException("Missing argument '$fieldName'");
            }
        }
    }

    public static function ensureInteger(array $parameter, string $field): void
    {
        if (!is_int($parameter[$field])) {
            throw new RuntimeException("Field '$field' is not of type integer");
        }
    }

    public static function ensureString(array $parameter, string $field): void
    {
        if (!is_string($parameter[$field])) {
            throw new RuntimeException("Field '$field' is not of type string");
        }
    }
}
