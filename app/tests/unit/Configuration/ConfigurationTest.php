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
        $realPath = realpath(__DIR__ .'/../../../../');
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

    public function testCanRetrieveTagGroup(): void
    {
        $actual = $this->subject->getTagGroup();
        $expected = [
            'people' => [
                0 => 'friendA',
                1 => 'friendB',
                2 => 'friendC',
            ],
            'country' => [
                0 => 'germany',
                1 => 'sweden',
                2 => 'denmark',
                3 => 'greece',
                4 => 'china',
            ],
            'city' => [
                0 => 'amsterdam',
                1 => 'london',
                2 => 'berlin',
            ],
            'madeBy' => [
                0 => 'friendA',
                1 => 'friendC',
            ],
            'misc' => [
                0 => 'dinner',
                1 => 'party',
                2 => 'study',
                3 => 'goingOut',
                4 => 'traveling',
                5 => 'food',
                6 => 'cute',
            ],
            'year' => [
                0 => '2000',
                1 => '2001',
                2 => '2002',
                3 => '2003',
            ],
        ];

        $this->assertEquals($expected, $actual);
    }

    public
    function testExceptionOnMissingConfiguration(): void
    {
        $configFile = '/MeepMeep';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Config file not found: '{$configFile}'");

        new Configuration($configFile);
    }

    public
    function testExceptionOnMissingConfigurationSection(): void
    {
        $configFile = __DIR__ . '/../../resources/configMissingSection.ini';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Could not get section 'database'");

        new Configuration($configFile);
    }
}
