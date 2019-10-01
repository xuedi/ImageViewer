<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Configuration\Configuration;
use ImageViewer\Configuration\DatabaseConfig;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class DatabaseTest extends TestCase
{
    /** @var DatabaseConfig */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new DatabaseConfig([
            'host' => '127.0.0.1',
            'port' => '3306',
            'user' => 'user',
            'pass' => 'pass',
            'name' => 'name',
        ]);
    }

    public function testCanBuildFactory(): void
    {
        $this->assertInstanceOf(DatabaseConfig::class, $this->subject);
    }

    public function testCanNotBuildBecauseOfMissingParameter(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Config 'database' is missing: 'host'");

        new DatabaseConfig([
            'port' => '3306',
            'user' => 'user',
            'pass' => 'pass',
            'name' => 'name',
        ]);

    }

    public function testCanRetrieveDsn(): void
    {
        $expected = 'mysql:host=127.0.0.1;port=3306;dbname=name;charset=utf8mb4';
        $actual = $this->subject->getDsn();
        $this->assertEquals($expected, $actual);
    }

    public function testCanRetrieveHost(): void
    {
        $expected = '127.0.0.1';
        $actual = $this->subject->getHost();
        $this->assertEquals($expected, $actual);
    }

    public function testCanRetrieveUser(): void
    {
        $expected = 'user';
        $actual = $this->subject->getUser();
        $this->assertEquals($expected, $actual);
    }

    public function testCanRetrievePass(): void
    {
        $expected = 'pass';
        $actual = $this->subject->getPass();
        $this->assertEquals($expected, $actual);
    }

    public function testCanRetrievePort(): void
    {
        $expected = '3306';
        $actual = $this->subject->getPort();
        $this->assertEquals($expected, $actual);
    }

    public function testCanRetrieveName(): void
    {
        $expected = 'name';
        $actual = $this->subject->getName();
        $this->assertEquals($expected, $actual);
    }
}
