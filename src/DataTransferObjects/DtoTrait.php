<?php

trait DtoTypes
{
    public static function ensureParameter(array $parameter, array $expectedFields)
    {
        foreach ($expectedFields as $fieldName) {
            if (!isset($parameter[$fieldName])) {
                throw new RuntimeException("Missing argument '$fieldName'");
            }
        }
    }

    public static function ensureInteger(array $parameter, string $field)
    {
        if (!is_int($parameter[$field])) {
            throw new RuntimeException("Field '$field' is not of type integer");
        }
    }

    public static function ensureString(array $parameter, string $field)
    {
        if (!is_string($parameter[$field])) {
            throw new RuntimeException("Field '$field' is not of type string");
        }
    }
}
