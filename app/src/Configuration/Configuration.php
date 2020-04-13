<?php declare(strict_types=1);

namespace ImageViewer\Configuration;

use RuntimeException;

class Configuration
{
    private string $path;
    private string $imagePath;
    private string $cache;
    private string $migrations;
    private OptionsConfig $options;
    private DatabaseConfig $database;

    public function __construct(string $configFile)
    {
        $this->path = $this->getBasePath();
        $this->ensureFileExists($configFile);

        $this->database = new DatabaseConfig(
            $this->getSections($configFile, 'database')
        );

        $this->options = new OptionsConfig(
            $this->getSections($configFile, 'options')
        );

        $locations = $this->getSections($configFile, 'locations');
        $this->imagePath = $this->processImagePath((string)$locations['images']);
        $this->migrations = (string)$locations['migrations'];
        $this->cache = (string)$locations['cache'];
    }

    public function getBasePath(): string
    {
        $basePath = realpath(__dir__ . '/../../');
        $basePath = rtrim($basePath, '/') . '/';

        return $basePath;
    }

    public function getDatabase(): DatabaseConfig
    {
        return $this->database;
    }

    public function getOptions(): OptionsConfig
    {
        return $this->options;
    }

    public function getImagePath(): string
    {
        return $this->imagePath;
    }

    public function getCachePath(): string
    {
        return $this->cache;
    }

    public function getMigrationsPath(): string
    {
        return $this->migrations;
    }

    private function ensureFileExists(string $configFile): void
    {
        if (!file_exists($configFile)) {
            throw new RuntimeException("Config file not found: '{$configFile}'");
        }
    }

    private function getSections(string $configFile, string $section): array
    {
        $processSections = true;
        $iniFile = parse_ini_file($configFile, $processSections);
        if (!isset($iniFile[$section])) {
            throw new RuntimeException("Could not get section '$section'");
        }

        return $iniFile[$section];
    }

    private function processImagePath(string $imagePath): string
    {
        if ($imagePath[0] == '/') {
            $absolutePath = $imagePath;
        } else {
            $absolutePath = realpath(__DIR__ . '/../../../') . '/' . $imagePath;
        }

        if (is_dir($absolutePath)) {
            return $absolutePath;
        }

        throw new RuntimeException("Could not find the image absolute path: '$absolutePath', config: '$imagePath'");
    }
}
