<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Configuration\Configuration;
use ImageViewer\Configuration\DatabaseConfig;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ConfigurationTest extends TestCase
{
    /** @var Configuration */
    private $subject;

    protected function setUp(): void
    {
        $file = __DIR__ . '/../../resources/config.ini';
        $this->subject = new Configuration($file);
    }

    public function testCanBuildFactory(): void
    {
        $this->assertInstanceOf(Configuration::class, $this->subject);
    }

    public function testCanRetrieveBasePath(): void
    {
        $expected = realpath(__dir__ . '/../../../') . '/';
        $actual = $this->subject->getBasePath();
        $this->assertEquals($expected, $actual);
    }

    public function testCanRetrieveImagePath(): void
    {
        $expected = 'tests/resources/images/';
        $actual = $this->subject->getImagePath();
        $this->assertEquals($expected, $actual);
    }

    public function testCanRetrieveCachePath(): void
    {
        $expected = 'tests/resources/tmp/';
        $actual = $this->subject->getCachePath();
        $this->assertEquals($expected, $actual);
    }

    public function testCanRetrieveMigrationsPath(): void
    {
        $expected = 'database/migrations/';
        $actual = $this->subject->getMigrationsPath();
        $this->assertEquals($expected, $actual);
    }

    public function testCanRetrieveDatabase(): void
    {
        $expected = new DatabaseConfig([
            'host' => '127.0.0.1',
            'port' => '3306',
            'user' => 'imageViewer',
            'pass' => 'imageViewer',
            'name' => 'imageViewer',
        ]);
        $actual = $this->subject->getDatabase();
        $this->assertEquals($expected, $actual);
    }

    public function testExceptionOnMissingConfiguration(): void
    {
        $configFile = '/MeepMeep';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Config file not found: '{$configFile}'");

        new Configuration($configFile);
    }
}
