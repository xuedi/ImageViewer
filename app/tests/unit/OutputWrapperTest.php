<?php declare(strict_types=1);

namespace ImageViewer;

use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \ImageViewer\OutputWrapper
 */
final class OutputWrapperTest extends TestCase
{
    public function testCanEcho(): void
    {
        $expectedOutput = 'testOutput';

        $this->expectOutputString($expectedOutput);

        $subject = new OutputWrapper();
        $subject->echo($expectedOutput);
    }
}
