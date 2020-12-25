<?php declare(strict_types=1);

namespace ImageViewer;

use Exception;
use ImageViewer\DataTransferObjects\MissingThumbnail;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \ImageViewer\ThumbnailManager
 * @uses   \ImageViewer\DataTransferObjects\MissingThumbnail
 */
final class ThumbnailManagerTest extends TestCase
{
    /** @var MockObject|Database */
    private $database;

    /** @var MockObject|ThumbnailGenerator */
    private $generatorMock;

    private string $imagePath;
    private ThumbnailManager $subject;

    public function setUp(): void
    {
        $this->database = $this->createMock(Database::class);
        $this->generatorMock = $this->createMock(ThumbnailGenerator::class);
        $this->imagePath = realpath(__DIR__ . '/../resources/images/') . '/';

        $this->subject = new ThumbnailManager($this->database, $this->generatorMock, $this->imagePath, 3);
    }

    public function testCanBuildFactory(): void
    {
        $this->assertInstanceOf(ThumbnailManager::class, $this->subject);
    }

    public function testCanScanForFiles(): void
    {
        $this->database->expects($this->exactly(3))->method('getMissingThumbnails')->willReturn([
            MissingThumbnail::from(
                'someHash_200',
                200,
                1,
                'China/2002-04-00 Day in HongKong/alex-azabache-YM71ka72TNw-unsplash.jpg',
                1
            ),
            MissingThumbnail::from(
                'someHash_200',
                200,
                1,
                'China/2002-04-00 Day in HongKong/chilam-siu-7pSxk2ThDEE-unsplash.jpg',
                2
            ),
            MissingThumbnail::from(
                'someHash_200',
                200,
                1,
                'China/2002-04-00 Day in HongKong/chilam-siu-QZjd3hQGwVQ-unsplash.jpg',
                3
            ),
            MissingThumbnail::from(
                'someHash_200',
                200,
                1,
                'China/2002-04-00 Day in HongKong/frame-harirak-6xxj2JTLWc4-unsplash.jpg',
                4
            ),
            MissingThumbnail::from(
                'someHash_200',
                200,
                1,
                'China/2002-04-00 Day in HongKong/lf-franciz--VxduY2PV-g-unsplash.jpg',
                5
            ),
            MissingThumbnail::from(
                'someHash_200',
                200,
                1,
                'Germany/2019-09-13 Out in the green/SunFlowerCouple.jpg',
                6
            ),
            MissingThumbnail::from(
                'someHash_200',
                200,
                1,
                'Germany/2019-09-13 Out in the green/SunFlowerHouse.jpeg',
                7
            ),
            MissingThumbnail::from(
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
            MissingThumbnail::from(
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

    public function testCanContinueOnFailedGenerator(): void
    {
        $this->generatorMock->method('create')->willThrowException(
            new RuntimeException('someThingWentWrong')
        );

        $this->database->method('getMissingThumbnails')->willReturn([
            MissingThumbnail::from(
                'someHash_200',
                200,
                1,
                'China/2002-04-00 Day in HongKong/alex-azabache-YM71ka72TNw-unsplash.jpg',
                1
            ),
        ]);

        $this->assertEquals(0, $this->subject->run(0));
    }
}
