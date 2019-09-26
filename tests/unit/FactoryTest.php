<?php declare(strict_types=1);

use ImageViewer\Configuration\Configuration;
use ImageViewer\Factory;
use PHPUnit\Framework\TestCase;

final class FactoryTest extends TestCase
{
    public function testCanBuildFactory(): void
    {
        $config = $this->createMock(Configuration::class);
        $subject = new Factory($config);
        $this->assertInstanceOf(Factory::class, $subject);
    }
}
