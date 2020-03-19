<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Configuration\Configuration;
use ImageViewer\Configuration\DatabaseConfig;
use ImageViewer\Controller\RegisterController;
use ImageViewer\Extractors\LocationExtractor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ImageViewer\Factory
 * @uses   \ImageViewer\Database
 * @uses   \ImageViewer\ExtractorService
 * @uses   \ImageViewer\Extractors\EventExtractor
 * @uses   \ImageViewer\Extractors\LocationExtractor
 * @uses   \ImageViewer\Extractors\MetaExtractor
 * @uses   \ImageViewer\FileBuilder
 * @uses   \ImageViewer\FileScanner
 * @uses   \ImageViewer\ThumbnailGenerator
 * @uses   \ImageViewer\Controller\RegisterController
 */
final class FactoryTest extends TestCase
{
    /** @var MockObject|Configuration */
    private MockObject $config;

    private Factory $subject;

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

    public function testCanGetThumbnailGenerator(): void
    {
        $this->assertInstanceOf(ThumbnailGenerator::class, $this->subject->getThumbnailGenerator());
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

    public function testGetRegisterController(): void
    {
        $this->assertInstanceOf(RegisterController::class, $this->subject->getRegisterController());
    }
}
