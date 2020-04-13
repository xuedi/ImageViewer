<?php declare(strict_types=1);

namespace ImageViewer\Updater;

use ImageViewer\Database;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @covers \ImageViewer\Updater\Structure
 * @uses   \ImageViewer\EventDate
 */
final class StructureTest extends TestCase
{
    /** @var MockObject|Database */
    private $databaseMock;

    /** @var MockObject|ConsoleOutput */
    private $outputMock;

    /** @var MockObject|ProgressBar */
    private $progressBarMock;

    private string $basePath;
    private Structure $subject;

    public function setUp(): void
    {
        $this->basePath = realpath(__DIR__ . '/../../resources/images/') . '/';
        $this->databaseMock = $this->createMock(Database::class);
        $this->outputMock = $this->createMock(ConsoleOutput::class);
        $this->progressBarMock = $this->createMock(ProgressBar::class);

        $this->subject = new Structure(
            $this->databaseMock,
            $this->outputMock,
            $this->progressBarMock,
            $this->basePath
        );
    }

    public function testCanBuildFactory(): void
    {
        $this->assertInstanceOf(Structure::class, $this->subject);
    }

    public function testCanUpdate(): void
    {
        $imageCount = 8;

        $this->databaseMock->expects($this->once())->method('deleteLocations');
        $this->databaseMock->expects($this->once())->method('deleteEvents');
        $this->databaseMock->expects($this->once())->method('getAllImagesNames')->willReturn([
            0 => 'China/2002-04-00 Day in HongKong/alex-azabache-YM71ka72TNw-unsplash.jpg',
            1 => 'China/2002-04-00 Day in HongKong/chilam-siu-7pSxk2ThDEE-unsplash.jpg',
            2 => 'China/2002-04-00 Day in HongKong/chilam-siu-QZjd3hQGwVQ-unsplash.jpg',
            3 => 'China/2002-04-00 Day in HongKong/frame-harirak-6xxj2JTLWc4-unsplash.jpg',
            4 => 'China/2002-04-00 Day in HongKong/lf-franciz--VxduY2PV-g-unsplash.jpg',
            5 => 'Germany/2019-09-13 Out in the green/SunFlowerCouple.jpg',
            6 => 'Germany/2019-09-13 Out in the green/SunFlowerHouse.jpeg',
            7 => 'Germany/2019-09-13 Out in the green/SunflowersFromWikipedia.jpg', // exif issue
        ]);

        $this->progressBarMock->expects($this->once())->method('setMaxSteps')->with($imageCount * 2);
        $this->progressBarMock->expects($this->once())->method('start');
        $this->progressBarMock->expects($this->once())->method('finish');
        $this->progressBarMock->expects($this->exactly(($imageCount * 2) + 1))->method('advance');
        $this->progressBarMock->expects($this->once())->method('setFormat')->with(
            'Restructuring: [%bar%] %memory:6s%'
        );

        $this->subject->update();
    }

    public function testCanSeeHandledException(): void
    {
        $this->databaseMock->expects($this->once())->method('getAllImagesNames')->willReturn([
            0 => 'China/INVALID Event/image.jpg',
        ]);

        $this->subject->update();

        $this->expectOutputString("Could not process: 'China/INVALID Event/image.jpg': Cound not create EventDate from 'invalid ev'\n");
        //
    }
}
