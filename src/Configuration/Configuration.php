<?php declare(strict_types=1);

namespace ImageViewer\Configuration;

use RuntimeException;

class Configuration
{
    /** @var string */
    private $path;

    /** @var DatabaseConfig */
    private $database;

    public function __construct(string $configFile)
    {
        $this->path = $this->getBasePath();
        $this->ensureFileExists($configFile);

        list($database, $locations) = $this->getSections($configFile);
        $this->database = new DatabaseConfig($database);
    }

    public function getDatabase(): DatabaseConfig
    {
        return $this->database;
    }

    public function getImagePath(): string
    {
        return $this->path;
    }

    private function ensureFileExists(string $configFile): void
    {
        if (!file_exists($configFile)) {
            throw new RuntimeException("Config file not found: '{$configFile}'");
        }
    }

    private function getSections(string $configFile): array
    {
        $processSections = true;
        $iniFile = parse_ini_file($configFile, $processSections);
        if (!isset($iniFile['database'])) {
            throw new RuntimeException("Could not get section 'database'");
        }
        if (!isset($iniFile['locations'])) {
            throw new RuntimeException("Could not get section 'location'");
        }

        return [
            $iniFile['database'],
            $iniFile['locations']
        ];
    }

    private function getBasePath(): string
    {
        $basePath = __dir__ . '/../../';
        if (!file_exists($basePath . 'ImageViewer')) {
            throw new RuntimeException("Could not set basePath: '{$basePath}'");
        }

        return $basePath;
    }
}
