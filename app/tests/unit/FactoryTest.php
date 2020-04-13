<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Configuration\Configuration;
use ImageViewer\Configuration\DatabaseConfig;
use ImageViewer\Controller\RegisterController;
use ImageViewer\Updater\Filesystem;
use ImageViewer\Updater\Metadata;
use ImageViewer\Updater\Structure;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ImageViewer\Factory
 * @uses   \ImageViewer\Database
 * @uses   \ImageViewer\ThumbnailGenerator
 * @uses   \ImageViewer\Controller\RegisterController
 * @uses   \ImageViewer\Controller\AbstractController
 * @uses   \ImageViewer\Updater\Filesystem
 * @uses   \ImageViewer\Updater\Structure
 * @uses   \ImageViewer\Updater\Metadata
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

    public function testGetRegisterController(): void
    {
        $this->assertInstanceOf(RegisterController::class, $this->subject->getRegisterController());
    }

    public function testGetUpdaterFilesystem(): void
    {
        $this->assertInstanceOf(Filesystem::class, $this->subject->getUpdaterFilesystem());
    }

    public function testGetUpdaterStructure(): void
    {
        $this->assertInstanceOf(Structure::class, $this->subject->getUpdaterStructure());
    }

    public function testGetUpdaterMetadata(): void
    {
        $this->assertInstanceOf(Metadata::class, $this->subject->getUpdaterMetadata());
    }
}
