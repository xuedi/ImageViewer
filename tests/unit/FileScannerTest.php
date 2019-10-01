<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Configuration\Configuration;
use ImageViewer\Configuration\DatabaseConfig;
use PHPUnit\Framework\TestCase;

final class FileScannerTest extends TestCase
{
    public function testCanBuildFactory(): void
    {
        $database = $this->createMock(Database::class);
        $basePath = 'tests/resources/images/';

        $subject = new FileScanner($database, $basePath);
        $this->assertInstanceOf(FileScanner::class, $subject);
    }
}
