<?php declare(strict_types=1);

namespace ImageViewer;

use Exception;
use ImageViewer\DataTransferObjects\MissingThumbnailDto;
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
            MissingThumbnailDto::from(
                'someHash_200',
                200,
                1,
                'China/2002-04-00 Day in HongKong/alex-azabache-YM71ka72TNw-unsplash.jpg',
                1
            ),
            MissingThumbnailDto::from(
                'someHash_200',
                200,
                1,
                'China/2002-04-00 Day in HongKong/chilam-siu-7pSxk2ThDEE-unsplash.jpg',
                2
            ),
            MissingThumbnailDto::from(
                'someHash_200',
                200,
                1,
                'China/2002-04-00 Day in HongKong/chilam-siu-QZjd3hQGwVQ-unsplash.jpg',
                3
            ),
            MissingThumbnailDto::from(
                'someHash_200',
                200,
                1,
                'China/2002-04-00 Day in HongKong/frame-harirak-6xxj2JTLWc4-unsplash.jpg',
                4
            ),
            MissingThumbnailDto::from(
                'someHash_200',
                200,
                1,
                'China/2002-04-00 Day in HongKong/lf-franciz--VxduY2PV-g-unsplash.jpg',
                5
            ),
            MissingThumbnailDto::from(
                'someHash_200',
                200,
                1,
                'Germany/2019-09-13 Out in the green/SunFlowerCouple.jpg',
                6
            ),
            MissingThumbnailDto::from(
                'someHash_200',
                200,
                1,
                'Germany/2019-09-13 Out in the green/SunFlowerHouse.jpeg',
                7
            ),
            MissingThumbnailDto::from(
                'someHash_200',
                200,
                1,
                'Germany/2019-09-13 Out in the green/SunflowersFromWikipedia.jpg',
                8
            ),
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
            MissingThumbnailDto::from(
                'someHash_200',
                200,
                1,
                'China/2002-04-00 Day in HongKong/alex-azabache-YM71ka72TNw-unsplash.jpg',
                1
            ),
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
            MissingThumbnailDto::from(
                'someHash_200',
                200,
                1,
                $file,
                1
            ),
        ]);

        $this->subject->run(0);
    }

    public function testExceptionToBeShownOnAlreadyExistingThumbnail(): void
    {
        $file = '.gitkeep';
        $expectedFile = realpath(__DIR__ . '/../../../') . '/public/thumbs/' . $file;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Thumbnail already exist '$expectedFile'");

        $this->database->method('getMissingThumbnails')->willReturn([
            MissingThumbnailDto::from(
                '.gitkeep',
                200,
                1,
                'China/2002-04-00 Day in HongKong/alex-azabache-YM71ka72TNw-unsplash.jpg',
                1
            ),
        ]);

        $this->subject->run(0);
    }
}
