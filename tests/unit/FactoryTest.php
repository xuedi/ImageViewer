<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Configuration\Configuration;
use ImageViewer\Configuration\DatabaseConfig;
use ImageViewer\Extractors\LocationExtractor;
use ImageViewer\Extractors\MetaExtractor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class FactoryTest extends TestCase
{
    private Factory $subject;

    /** @var MockObject|Configuration */
    private MockObject $config;

    public function setUp(): void
    {
        $databaseConfig = $this->createMock(DatabaseConfig::class);
        $databaseConfig->method('getDsn')->willReturn('sqlite::memory:');

        $this->config = $this->createMock(Configuration::class);
        $this->config->method('getDatabase')->willReturn($databaseConfig);
        $this->config->method('getImagePath')->willReturn('../../tests/resources/images/');

        $this->subject = new Factory($this->config);
    }

    public function testCanBuildFactory(): void
    {
        $this->assertInstanceOf(Factory::class, $this->subject);
    }

    public function testCanGetDatabase(): void
    {
        $this->assertInstanceOf(Database::class, $this->subject->getDatabase());
    }

    public function testCanGetFileScanner(): void
    {
        $this->assertInstanceOf(FileScanner::class, $this->subject->getFileScanner());
    }

    public function testCanGetExtractorService(): void
    {
        $this->assertInstanceOf(ExtractorService::class, $this->subject->getExtractorService());
    }

    public function testCanGetLocationExtractor(): void
    {
        $this->assertInstanceOf(LocationExtractor::class, $this->subject->getLocationExtractor());
    }
}
