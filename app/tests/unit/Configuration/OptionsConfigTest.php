<?php declare(strict_types=1);

namespace ImageViewer\Configuration;

use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \ImageViewer\Configuration\OptionsConfig
 */
final class OptionsConfigTest extends TestCase
{
    private OptionsConfig $subject;

    protected function setUp(): void
    {
        $this->subject = OptionsConfig::fromParameters([
            'threads' => 4,
        ]);
    }

    public function testCanBuildFactory(): void
    {
        $this->assertInstanceOf(OptionsConfig::class, $this->subject);
    }

    public function testCanNotBuildBecauseOfMissingParameter(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Missing argument 'threads'");

        OptionsConfig::fromParameters([]);
    }

    public function testCanRetrieveThreads(): void
    {
        $this->assertEquals(4, $this->subject->getThreads());
    }
}
