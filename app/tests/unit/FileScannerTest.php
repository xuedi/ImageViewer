<?php declare(strict_types=1);

namespace ImageViewer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @covers \ImageViewer\FileScanner
 */
final class FileScannerTest extends TestCase
{
    /** @var MockObject|OutputInterface */
    private $output;

    /** @var MockObject|ProgressBar */
    private $progressBar;

    /** @var MockObject|Database */
    private $database;

    private string $basePath;
    private FileScanner $subject;

    public function setUp(): void
    {
        $this->database = $this->createMock(Database::class);
        $this->basePath = realpath(__DIR__ . '/../resources/images/') . '/';

        $this->output = $this->createMock(OutputInterface::class);
        $this->progressBar = $this->createMock(ProgressBar::class);
        $this->subject = new FileScanner($this->database, $this->output, $this->progressBar, $this->basePath);
    }

    public function testCanBuildFactory(): void
    {
        $this->assertInstanceOf(FileScanner::class, $this->subject);
    }

    public function testCanScanForFiles(): void
    {
        $imageCount = 8;
        $this->database->expects($this->once())->method('getImages')->willReturn([]);

        $this->output->expects($this->once())->method('write')->with(PHP_EOL);

        $this->progressBar->expects($this->once())->method('setMaxSteps')->with($imageCount);
        $this->progressBar->expects($this->once())->method('start');
        $this->progressBar->expects($this->once())->method('finish');
        $this->progressBar->expects($this->exactly($imageCount + 1))->method('advance');
        $this->progressBar->expects($this->once())->method('setFormat')->with(
            'Search:    [%bar%] %memory:6s%'
        );

        $actual = $this->subject->scan();
        $expected = [
            0 => $this->basePath . 'China/2002-04-00 Day in HongKong/alex-azabache-YM71ka72TNw-unsplash.jpg',
            1 => $this->basePath . 'China/2002-04-00 Day in HongKong/chilam-siu-7pSxk2ThDEE-unsplash.jpg',
            2 => $this->basePath . 'China/2002-04-00 Day in HongKong/chilam-siu-QZjd3hQGwVQ-unsplash.jpg',
            3 => $this->basePath . 'China/2002-04-00 Day in HongKong/frame-harirak-6xxj2JTLWc4-unsplash.jpg',
            4 => $this->basePath . 'China/2002-04-00 Day in HongKong/lf-franciz--VxduY2PV-g-unsplash.jpg',
            5 => $this->basePath . 'Germany/2019-09-13 Out in the green/SunFlowerCouple.jpg',
            6 => $this->basePath . 'Germany/2019-09-13 Out in the green/SunFlowerHouse.jpeg',
            7 => $this->basePath . 'Germany/2019-09-13 Out in the green/SunflowersFromWikipedia.jpg',
        ];

        $this->assertEquals($expected, $actual);
    }
}
