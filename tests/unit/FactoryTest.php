<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Configuration\Configuration;
use ImageViewer\Configuration\DatabaseConfig;
use PHPUnit\Framework\TestCase;

final class FactoryTest extends TestCase
{
    public function testCanBuildFactory(): void
    {
        $config = $this->createMock(Configuration::class);
        $subject = new Factory($config);
        $this->assertInstanceOf(Factory::class, $subject);
    }

    public function testCanGetDatabase(): void
    {
        $databaseConfig = $this->createMock(DatabaseConfig::class);
        $databaseConfig->method('getDsn')->willReturn('sqlite::memory:');

        $config = $this->createMock(Configuration::class);
        $config->method('getDatabase')->willReturn($databaseConfig);

        $subject = new Factory($config);
        $this->assertInstanceOf(Database::class, $subject->getDatabase());
    }

    public function testCanGetFileScanner(): void
    {
        $databaseConfig = $this->createMock(DatabaseConfig::class);
        $databaseConfig->method('getDsn')->willReturn('sqlite::memory:');

        $config = $this->createMock(Configuration::class);
        $config->method('getDatabase')->willReturn($databaseConfig);
        $config->method('getImagePath')->willReturn('../../tests/resources/images/');

        $subject = new Factory($config);
        $this->assertInstanceOf(FileScanner::class, $subject->getFileScanner());
    }
}
