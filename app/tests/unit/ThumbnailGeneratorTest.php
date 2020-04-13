<?php declare(strict_types=1);

namespace ImageViewer;

use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \ImageViewer\ThumbnailGenerator
 */
final class ThumbnailGeneratorTest extends TestCase
{
    private string $testFile;
    private ThumbnailGenerator $subject;

    public function setUp(): void
    {
        $this->testFile = realpath(__DIR__ . '/../resources/tmp/') . '/testThumb.jpg';
        $this->subject = new ThumbnailGenerator();

        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    public function testCreateThumbnail(): void
    {
        $path = __DIR__ . '/../resources/images/China/2002-04-00 Day in HongKong/';
        $file = $path . 'frame-harirak-6xxj2JTLWc4-unsplash.jpg';

        $this->subject->create($file, 200, $this->testFile);

        $this->assertFileExists($this->testFile);
    }

    public function testCanCatchNonExistingFile(): void
    {
        $file = __DIR__ . '/nonExisting.file';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Could not find file '$file'");

        $this->subject->create($file, 100, '');
    }

    public function testCanCatchNonAlreadyExistingThumbnail(): void
    {
        $path = __DIR__ . '/../resources/images/China/2002-04-00 Day in HongKong/';
        $file = $path . 'frame-harirak-6xxj2JTLWc4-unsplash.jpg';
        $thumbnail = $file; // same file,already exist

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Thumbnail already exist '$thumbnail'");

        $this->subject->create($file, 100, $thumbnail);
    }
}
