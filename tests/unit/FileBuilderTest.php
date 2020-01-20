<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Extractors\MetaExtractor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @covers \ImageViewer\FileBuilder
 */
final class FileBuilderTest extends TestCase
{
    /** @var MockObject|Database */
    private $databaseMock;

    /** @var MockObject|ConsoleOutput */
    private $outputMock;

    /** @var MockObject|MetaExtractor */
    private $metaExtractorMock;

    /** @var MockObject|ProgressBar */
    private $progressBar;

    private string $basePath;
    private FileBuilder $subject;

    public function setUp(): void
    {
        $this->basePath = realpath(__DIR__ . '/../resources/images/') . '/';
        $this->databaseMock = $this->createMock(Database::class);
        $this->outputMock = $this->createMock(ConsoleOutput::class);
        $this->metaExtractorMock = $this->createMock(MetaExtractor::class);
        $this->progressBar = $this->createMock(ProgressBar::class);

        $this->subject = new FileBuilder(
            $this->databaseMock,
            $this->outputMock,
            $this->metaExtractorMock,
            $this->progressBar,
            'Path'
        );
    }

    public function testCanBuildFactory(): void
    {
        $this->assertInstanceOf(FileBuilder::class, $this->subject);
    }

    public function testCanBuildFileList(): void
    {
        $imageCount = 8;

        $this->progressBar->expects($this->once())->method('setMaxSteps')->with($imageCount);
        $this->progressBar->expects($this->once())->method('start');
        $this->progressBar->expects($this->once())->method('finish');
        $this->progressBar->expects($this->exactly($imageCount + 1))->method('advance');
        $this->progressBar->expects($this->once())->method('setFormat')->with('Files:     [%bar%] %memory:6s%');

        $files = [
            0 => $this->basePath . 'China/2002-04-00 Day in HongKong/alex-azabache-YM71ka72TNw-unsplash.jpg',
            1 => $this->basePath . 'China/2002-04-00 Day in HongKong/chilam-siu-7pSxk2ThDEE-unsplash.jpg',
            2 => $this->basePath . 'China/2002-04-00 Day in HongKong/chilam-siu-QZjd3hQGwVQ-unsplash.jpg',
            3 => $this->basePath . 'China/2002-04-00 Day in HongKong/frame-harirak-6xxj2JTLWc4-unsplash.jpg',
            4 => $this->basePath . 'China/2002-04-00 Day in HongKong/lf-franciz--VxduY2PV-g-unsplash.jpg',
            5 => $this->basePath . 'Germany/2019-09-13 Out in the green/SunFlowerCouple.jpg',
            6 => $this->basePath . 'Germany/2019-09-13 Out in the green/SunFlowerHouse.jpeg',
            7 => $this->basePath . 'Germany/2019-09-13 Out in the green/SunflowersFromWikipedia.jpg',
        ];
        $events = [];
        $tags = [];

        $this->subject->build($files, $events, $tags);
    }
}
