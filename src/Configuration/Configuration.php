<?php declare(strict_types=1);

namespace ImageViewer\Configuration;

use RuntimeException;

class Configuration
{
    /** @var string */
    private $path;

    /** @var TagGroupConfig */
    private $tagGroup;

    /** @var DatabaseConfig */
    private $database;

    /** @var string */
    private $imagePath;

    /** @var string */
    private $cache;

    /** @var string */
    private $migrations;

    public function __construct(string $configFile)
    {
        $this->path = $this->getBasePath();
        $this->ensureFileExists($configFile);

        $this->database = new DatabaseConfig(
            $this->getSections($configFile, 'database')
        );

        $this->tagGroup = new TagGroupConfig(
            $this->getSections($configFile, 'tagGroups')
        );

        $locations = $this->getSections($configFile, 'locations');
        $this->imagePath = $locations['images'];
        $this->cache = $locations['cache'];
        $this->migrations = $locations['migrations'];
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

    public function getImagePath(): string
    {
        return $this->imagePath;
    }

    public function getTagGroup(): array
    {
        return $this->tagGroup->getGroup();
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
}
