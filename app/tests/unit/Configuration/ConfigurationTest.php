<?php declare(strict_types=1);

namespace ImageViewer\Configuration;

use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \ImageViewer\Configuration\Configuration
 * @uses   \ImageViewer\Configuration\DatabaseConfig
 * @uses   \ImageViewer\Configuration\OptionsConfig
 */
final class ConfigurationTest extends TestCase
{
    private Configuration $subject;

    protected function setUp(): void
    {
        $file = realpath(__DIR__ . '/../../resources/') . '/config.ini';
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

    public function testCanRetrieveRelativeBasePath(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Could not find the image absolute path: '/tmp/nonExistingPath', config: '/tmp/nonExistingPath'");

        $file = __DIR__ . '/../../resources/configMissingAbsoluteImagePath.ini';
        new Configuration($file);
    }

    public function testCanRetrieveAbsoluteBasePath(): void
    {
        $realPath = realpath(__DIR__ . '/../../../../');
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Could not find the image absolute path: '$realPath/nonExistingPath', config: 'nonExistingPath'");

        $file = __DIR__ . '/../../resources/configMissingRelativeImagePath.ini';
        new Configuration($file);
    }

    public function testCanRetrieveImagePath(): void
    {
        $expected = realpath(__DIR__ . '/../../resources/images/') . '/';
        $actual = $this->subject->getImagePath();

        $this->assertEquals($expected, $actual);
    }

    public function testCanRetrieveCachePath(): void
    {
        $expected = 'app/tests/resources/tmp/';
        $actual = $this->subject->getCachePath();

        $this->assertEquals($expected, $actual);
    }

    public function testCanRetrieveMigrationsPath(): void
    {
        $expected = 'app/database/migrations/';
        $actual = $this->subject->getMigrationsPath();

        $this->assertEquals($expected, $actual);
    }

    public function testCanRetrieveDatabase(): void
    {
        $actual = $this->subject->getDatabase();
        $expected = new DatabaseConfig([
            'host' => '127.0.0.1',
            'port' => '3306',
            'user' => 'imageViewer',
            'pass' => 'imageViewer',
            'name' => 'imageViewer',
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testCanRetrieveOptions(): void
    {
        $actual = $this->subject->getOptions();
        $expected = OptionsConfig::fromParameters([
            'threads' => 2,
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testExceptionOnMissingConfiguration(): void
    {
        $configFile = '/MeepMeep';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Config file not found: '{$configFile}'");

        new Configuration($configFile);
    }

    public function testExceptionOnMissingConfigurationSection(): void
    {
        $configFile = __DIR__ . '/../../resources/configMissingSection.ini';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Could not get section 'database'");

        new Configuration($configFile);
    }
}
