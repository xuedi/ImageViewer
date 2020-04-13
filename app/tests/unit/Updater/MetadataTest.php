<?php declare(strict_types=1);

namespace ImageViewer\Updater;

use ImageViewer\Database;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @covers \ImageViewer\Updater\Metadata
 * @uses   \ImageViewer\CameraSettings
 * @uses   \ImageViewer\Camera
 */
final class MetadataTest extends TestCase
{
    /** @var MockObject|Database */
    private $databaseMock;

    /** @var MockObject|ConsoleOutput */
    private $outputMock;

    /** @var MockObject|ProgressBar */
    private $progressBarMock;

    private string $basePath;
    private Metadata $subject;

    public function setUp(): void
    {
        $this->basePath = realpath(__DIR__ . '/../../resources/images/') . '/';
        $this->databaseMock = $this->createMock(Database::class);
        $this->outputMock = $this->createMock(ConsoleOutput::class);
        $this->progressBarMock = $this->createMock(ProgressBar::class);

        $this->subject = new Metadata(
            $this->databaseMock,
            $this->outputMock,
            $this->progressBarMock,
            $this->basePath
        );
    }

    public function testCanBuildFactory(): void
    {
        $this->assertInstanceOf(Metadata::class, $this->subject);
    }

    public function testCanUpdate(): void
    {
        $fileList = [
            0 => 'China/2002-04-00 Day in HongKong/alex-azabache-YM71ka72TNw-unsplash.jpg',
            1 => 'China/2002-04-00 Day in HongKong/chilam-siu-7pSxk2ThDEE-unsplash.jpg',
            2 => 'China/2002-04-00 Day in HongKong/chilam-siu-QZjd3hQGwVQ-unsplash.jpg',
            3 => 'China/2002-04-00 Day in HongKong/frame-harirak-6xxj2JTLWc4-unsplash.jpg',
            4 => 'China/2002-04-00 Day in HongKong/lf-franciz--VxduY2PV-g-unsplash.jpg',
            5 => 'Germany/2019-09-13 Out in the green/SunFlowerCouple.jpg',
            6 => 'Germany/2019-09-13 Out in the green/SunFlowerHouse.jpeg',
            7 => 'Germany/2019-09-13 Out in the green/SunflowersFromWikipedia.jpg',
        ];

        $tagList = [
            1 => "hong kong",
            2 => "skyscraper",
            3 => "brutalism",
            4 => "skyline",
            5 => "bus",
            6 => "grey",
            7 => "neon lights",
            8 => "night",
            9 => "street sign",
            10 => "flower",
            11 => "frienda",
            12 => "friendb",
            13 => "green",
            14 => "sunflower",
            15 => "landscape",
        ];

        $cameraList = [
            1 => "ed90a62d55a1834c9785b8e0f78785f4",
        ];


        $this->databaseMock
            ->expects($this->once())
            ->method('getCameras')
            ->willReturn($cameraList);

        $this->databaseMock
            ->expects($this->once())
            ->method('getImagesNamesWithStatus')
            ->willReturn($fileList);

        $this->databaseMock
            ->expects($this->once())
            ->method('getTags')
            ->willReturn($tagList);

        $this->progressBarMock->expects($this->once())->method('setMaxSteps')->with(count($fileList));
        $this->progressBarMock->expects($this->once())->method('start');
        $this->progressBarMock->expects($this->once())->method('finish');
        $this->progressBarMock->expects($this->exactly(count($fileList) + 1))->method('advance');
        $this->progressBarMock->expects($this->once())->method('setFormat')->with(
            'Updating metadata and tags: [%bar%] %memory:6s%'
        );

        $this->subject->update();
    }

    public function testCanSaveData(): void
    {
        $fileList = [
            1 => 'China/2002-04-00 Day in HongKong/chilam-siu-7pSxk2ThDEE-unsplash.jpg',
        ];

        $tagList = [
            1 => "hong kong",
            2 => "skyscraper",
            3 => "brutalism",
            4 => "skyline",
            5 => "bus",
            6 => "grey",
            7 => "neon lights",
            8 => "night",
            9 => "street sign",
            10 => "flower",
            11 => "frienda",
            12 => "friendb",
            13 => "green",
            14 => "sunflower",
            15 => "landscape",
        ];

        $cameraList = [
            1 => "ed90a62d55a1834c9785b8e0f78785f4",
            2 => "1dc5191c41e7312cba35dedbc380a421",
        ];


        $this->databaseMock
            ->expects($this->once())
            ->method('getCameras')
            ->willReturn($cameraList);

        $this->databaseMock
            ->expects($this->once())
            ->method('getImagesNamesWithStatus')
            ->willReturn($fileList);

        $this->databaseMock
            ->expects($this->once())
            ->method('getTags')
            ->willReturn($tagList);

        $this->databaseMock
            ->expects($this->once())
            ->method('update')
            ->with('files', 1, [
                'event_id' => 1,
                'camera_id' => 1,
                'status_id' => 2,
                'fileSize' => 1467015,
                'fileType' => 'image/jpeg',
                'pixel' => 10328064,
                'iso' => null,
                'exposure' => null,
                'aperture' => null,
                'width' => 2624,
                'height' => 3936,
                'createdAt' => '2019-12-30 14:08:01',
            ]);


        $this->subject->update();
    }

    public function testGetExceptionOnUnknownTag(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expected tag not found!');

        $this->databaseMock
            ->expects($this->once())
            ->method('getImagesNamesWithStatus')
            ->willReturn([
                0 => 'China/2002-04-00 Day in HongKong/alex-azabache-YM71ka72TNw-unsplash.jpg',
            ]);

        $this->databaseMock
            ->expects($this->once())
            ->method('getTags')
            ->willReturn([]); // while processing saved to DB but cant be retrieved short after

        $this->subject->update();
    }
}
