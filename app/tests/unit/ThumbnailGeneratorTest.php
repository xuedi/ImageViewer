<?php declare(strict_types=1);

namespace ImageViewer;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ThumbnailGeneratorTest extends TestCase
{
    private string $imagePath;
    private ThumbnailGenerator $subject;

    /** @var MockObject|Database */
    private $database;

    public function setUp(): void
    {
        $this->database = $this->createMock(Database::class);
        $this->imagePath = realpath(__DIR__ . '/../resources/images/') . '/';

        $this->subject = new ThumbnailGenerator($this->database, $this->imagePath, 3);
    }

    public function testCanBuildFactory(): void
    {
        $this->assertInstanceOf(ThumbnailGenerator::class, $this->subject);
    }

    public function testCanScanForFiles(): void
    {
        $this->database->expects($this->exactly(3))->method('getMissingThumbnails')->willReturn([
            [
                'name' => 'someHash_200',
                'size' => 200,
                'size_id' => 1,
                'file' => 'China/2002-04-00 Day in HongKong/alex-azabache-YM71ka72TNw-unsplash.jpg',
                'file_id' => 1,
            ],
            [
                'name' => 'someHash_200',
                'size' => 200,
                'size_id' => 1,
                'file' => 'China/2002-04-00 Day in HongKong/chilam-siu-7pSxk2ThDEE-unsplash.jpg',
                'file_id' => 2,
            ],
            [
                'name' => 'someHash_200',
                'size' => 200,
                'size_id' => 1,
                'file' => 'China/2002-04-00 Day in HongKong/chilam-siu-QZjd3hQGwVQ-unsplash.jpg',
                'file_id' => 3,
            ],
            [
                'name' => 'someHash_200',
                'size' => 200,
                'size_id' => 1,
                'file' => 'China/2002-04-00 Day in HongKong/frame-harirak-6xxj2JTLWc4-unsplash.jpg',
                'file_id' => 4,
            ],
            [
                'name' => 'someHash_200',
                'size' => 200,
                'size_id' => 1,
                'file' => 'China/2002-04-00 Day in HongKong/lf-franciz--VxduY2PV-g-unsplash.jpg',
                'file_id' => 5,
            ],
            [
                'name' => 'someHash_200',
                'size' => 200,
                'size_id' => 1,
                'file' => 'Germany/2019-09-13 Out in the green/SunFlowerCouple.jpg',
                'file_id' => 6,
            ],
            [
                'name' => 'someHash_200',
                'size' => 200,
                'size_id' => 1,
                'file' => 'Germany/2019-09-13 Out in the green/SunFlowerHouse.jpeg',
                'file_id' => 7,
            ],
            [
                'name' => 'someHash_200',
                'size' => 200,
                'size_id' => 1,
                'file' => 'Germany/2019-09-13 Out in the green/SunflowersFromWikipedia.jpg',
                'file_id' => 8,
            ],
        ]);

        $this->assertEquals(3, $this->subject->run(0));
        $this->assertEquals(3, $this->subject->run(1));
        $this->assertEquals(2, $this->subject->run(2));
    }

    public function testWillSkipOnZeroChunk(): void
    {
        $this->database->expects($this->once())->method('getMissingThumbnails')->willReturn([]);

        $this->assertEquals(0, $this->subject->run(0));
    }

    public function testWillSkipOnBadDivider(): void
    {
        $this->database->expects($this->exactly(3))->method('getMissingThumbnails')->willReturn([
            [
                'name' => 'someHash_200',
                'size' => 200,
                'size_id' => 1,
                'file' => 'China/2002-04-00 Day in HongKong/alex-azabache-YM71ka72TNw-unsplash.jpg',
                'file_id' => 1,
            ],
        ]);

        $this->assertEquals(1, $this->subject->run(0));
        $this->assertEquals(0, $this->subject->run(1));
        $this->assertEquals(0, $this->subject->run(2));
    }

    public function testExceptionToBeShownOnNonExistingFile(): void
    {
        $file = 'meIsNotReallyHere.lost';
        $expectedFile = $this->imagePath . $file;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Could not find file '$expectedFile'");

        $this->database->method('getMissingThumbnails')->willReturn([
            [
                'name' => 'someHash_200',
                'size' => 200,
                'size_id' => 1,
                'file' => $file,
                'file_id' => 1,
            ],
        ]);

        $this->subject->run(0);
    }

    public function testExceptionToBeShownOnAlreadyExistingThumbnail(): void
    {
        $file = '.gitkeep';
        $expectedFile = realpath(__DIR__ . '/../../../') . '/public/thumbs/'. $file;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Thumbnail already exist '$expectedFile'");

        $this->database->method('getMissingThumbnails')->willReturn([
            [
                'name' => '.gitkeep',
                'size' => 200,
                'size_id' => 1,
                'file' => 'China/2002-04-00 Day in HongKong/alex-azabache-YM71ka72TNw-unsplash.jpg',
                'file_id' => 1,
            ],
        ]);

        $this->subject->run(0);
    }
}
