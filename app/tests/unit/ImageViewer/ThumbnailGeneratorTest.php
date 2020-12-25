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
    private string $resourcePath;
    private ThumbnailGenerator $subject;

    public function setUp(): void
    {
        $this->resourcePath = (string)realpath(__DIR__ . '/../../resources/');
        $this->testFile = $this->resourcePath . '/tmp/testThumb.jpg';
        $this->subject = new ThumbnailGenerator();

        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    public function testCreateThumbnail(): void
    {
        $path = $this->resourcePath . '/images/China/2002-04-00 Day in HongKong/';
        $file = $path . 'frame-harirak-6xxj2JTLWc4-unsplash.jpg';

        $this->subject->create($file, 200, $this->testFile);

        $this->assertFileExists($this->testFile);
    }

    public function testCanCatchNonExistingFile(): void
    {
        $file = $this->resourcePath . '/nonExisting.file';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Could not find file '$file'");

        $this->subject->create($file, 100, '');
    }

    public function testCanCatchNonAlreadyExistingThumbnail(): void
    {
        $path = $this->resourcePath . '/images/China/2002-04-00 Day in HongKong/';
        $file = $path . 'frame-harirak-6xxj2JTLWc4-unsplash.jpg';
        $thumbnail = $file; // same file,already exist

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Thumbnail already exist '$thumbnail'");

        $this->subject->create($file, 100, $thumbnail);
    }
}
