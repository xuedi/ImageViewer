<?php declare(strict_types=1);

namespace ImageViewer\Updater;

use ImageViewer\Database;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @covers \ImageViewer\Updater\Filesystem
 */
final class FileBuilderTest extends TestCase
{
    /** @var MockObject|Database */
    private $databaseMock;

    /** @var MockObject|ConsoleOutput */
    private $outputMock;

    /** @var MockObject|ProgressBar */
    private $progressBar;

    private string $basePath;
    private Filesystem $subject;

    public function setUp(): void
    {
        $this->basePath = realpath(__DIR__ . '/../../resources/images/') . '/';
        $this->databaseMock = $this->createMock(Database::class);
        $this->outputMock = $this->createMock(ConsoleOutput::class);
        $this->progressBar = $this->createMock(ProgressBar::class);

        $this->subject = new Filesystem(
            $this->databaseMock,
            $this->outputMock,
            $this->progressBar,
            $this->basePath
        );
    }

    public function testCanBuildFactory(): void
    {
        $this->assertInstanceOf(Filesystem::class, $this->subject);
    }

    public function testCanUpdate(): void
    {
        $imageCount = 8;

        $this->databaseMock->expects($this->exactly(2))->method('getAllImagesNames')->willReturn([
            0 => 'China/2002-04-00 Day in HongKong/alex-azabache-YM71ka72TNw-unsplash.jpg',
            1 => 'China/2002-04-00 Day in HongKong/chilam-siu-7pSxk2ThDEE-unsplash.jpg',
            99 => 'China/orphanedImage.jpg',
        ]);
        $this->databaseMock->expects($this->exactly(5))->method('insert');

        $this->databaseMock->expects($this->once())->method('getImagesHashes')->willReturn([
            1 => 'aebd27921aa1dfef7cc1fa5820dedd4fdc1c9ce0', // does exist, triggers a rename
        ]);

        $this->databaseMock->expects($this->exactly(2))->method('update');

        $this->progressBar->expects($this->once())->method('setMaxSteps')->with($imageCount);
        $this->progressBar->expects($this->once())->method('start');
        $this->progressBar->expects($this->once())->method('finish');
        $this->progressBar->expects($this->exactly($imageCount + 1))->method('advance');
        $this->progressBar->expects($this->once())->method('setFormat')->with(
            'Syncing filesystem and database: [%bar%] %memory:6s%'
        );

        $files = [
            0 => $this->basePath . 'China/2002-04-00 Day in HongKong/alex-azabache-YM71ka72TNw-unsplash.jpg',
            1 => $this->basePath . 'China/2002-04-00 Day in HongKong/chilam-siu-7pSxk2ThDEE-unsplash.jpg',
            2 => $this->basePath . 'China/2002-04-00 Day in HongKong/chilam-siu-QZjd3hQGwVQ-unsplash.jpg',
            3 => $this->basePath . 'China/2002-04-00 Day in HongKong/frame-harirak-6xxj2JTLWc4-unsplash.jpg',
            4 => $this->basePath . 'China/2002-04-00 Day in HongKong/lf-franciz--VxduY2PV-g-unsplash.jpg',
            5 => $this->basePath . 'Germany/2019-09-13 Out in the green/SunFlowerCouple.jpg',
            6 => $this->basePath . 'Germany/2019-09-13 Out in the green/SunFlowerHouse.jpeg',
            7 => $this->basePath . 'Germany/2019-09-13 Out in the green/SunflowersFromWikipedia.jpg', // exif issue
        ];
        $events = [];
        $tags = [];

        $this->subject->update();
    }
}
