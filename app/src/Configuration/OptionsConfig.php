<?php declare(strict_types=1);

namespace ImageViewer\Configuration;

use RuntimeException;

class OptionsConfig
{
    private int $threads;

    public function __construct(array $configValues)
    {
        $this->threads = $this->ensureParameterInteger($configValues, 'threads');
    }

    public function getThreads(): int
    {
        return $this->threads;
    }

    private function ensureParameter(array $parameters, string $field): string
    {
        if (!isset($parameters[$field])) {
            throw new RuntimeException("Config 'options' is missing: '{$field}'");
        }

        return (string)$parameters[$field];
    }

    private function ensureParameterInteger(array $parameters, string $field): int
    {
        $value = $this->ensureParameter($parameters, $field);

        return (int)$value;
    }
}
