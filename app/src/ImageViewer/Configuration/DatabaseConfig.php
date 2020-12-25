<?php declare(strict_types=1);

namespace ImageViewer\Configuration;

use RuntimeException;

class DatabaseConfig
{
    private string $host;
    private string $port;
    private string $user;
    private string $pass;
    private string $name;

    public function __construct(array $configValues)
    {
        $this->host = $this->ensureParameter($configValues, 'host');
        $this->port = $this->ensureParameter($configValues, 'port');
        $this->user = $this->ensureParameter($configValues, 'user');
        $this->pass = $this->ensureParameter($configValues, 'pass');
        $this->name = $this->ensureParameter($configValues, 'name');
    }

    public function getDsn(): string
    {
        return 'mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->name . ';charset=utf8mb4';
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPass(): string
    {
        return $this->pass;
    }

    public function getPort(): string
    {
        return $this->port;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function ensureParameter(array $parameters, string $field): string
    {
        if (!isset($parameters[$field])) {
            throw new RuntimeException("Config 'database' is missing: '{$field}'");
        }

        return (string)$parameters[$field];
    }
}
