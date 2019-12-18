<?php declare(strict_types=1);

namespace ImageViewer\Configuration;

use RuntimeException;

class DatabaseConfig
{
    private array $data = [
        'host',
        'port',
        'user',
        'pass',
        'name',
    ];

    public function __construct(array $configValues)
    {
        $setData = [];
        foreach ($this->data as $expectedField) {
            if (!isset($configValues[$expectedField])) {
                throw new RuntimeException("Config 'database' is missing: '{$expectedField}'");
            } else {
                $setData[$expectedField] = $configValues[$expectedField];
            }
        }
        $this->data = $setData;
    }

    public function getDsn(): string
    {
        return 'mysql:host=' . $this->getHost() . ';port=' . $this->getPort() . ';dbname=' . $this->getName() . ';charset=utf8mb4';
    }

    public function getHost(): string
    {
        return $this->data['host'];
    }

    public function getUser(): string
    {
        return $this->data['user'];
    }

    public function getPass(): string
    {
        return $this->data['pass'];
    }

    public function getPort(): string
    {
        return $this->data['port'];
    }

    public function getName(): string
    {
        return $this->data['name'];
    }
}
