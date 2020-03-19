<?php declare(strict_types=1);

namespace ImageViewer\Extractors;

use ImageViewer\Database;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @covers \ImageViewer\Extractors\MetaExtractor
 */
final class MetaExtractorTest extends TestCase
{
    /** @var MockObject|Database */
    private $database;

    /** @var MockObject|ProgressBar */
    private $progressBar;

    /** @var MockObject|ConsoleOutput */
    private $consoleOutput;

    private string $basePath;
    private MetaExtractor $subject;

    protected function setUp(): void
    {
        $this->basePath = realpath(__DIR__ . '/../../resources/images/') . '/';
        $this->progressBar = $this->createMock(ProgressBar::class);
        $this->consoleOutput = $this->createMock(ConsoleOutput::class);

        $this->database = $this->createMock(Database::class);
        $this->database->method('getLocations')->willReturn([]);

        $this->subject = new MetaExtractor(
            $this->database,
            $this->consoleOutput,
            $this->progressBar,
            $this->basePath,
            $this->getTagGroup()
        );
    }

    public function testCanBuild(): void
    {
        $this->assertInstanceOf(MetaExtractor::class, $this->subject);
    }

    public function testCanParseNoLocations(): void
    {
        $imageCount = 1;

        $this->progressBar->expects($this->once())->method('setMaxSteps')->with($imageCount);
        $this->progressBar->expects($this->once())->method('start');
        $this->progressBar->expects($this->once())->method('finish');
        $this->progressBar->expects($this->exactly($imageCount + 1))->method('advance');
        $this->progressBar->expects($this->once())->method('setFormat')->with('Tags:      [%bar%] %memory:6s%');

        $this->database->expects($this->exactly(3))->method('insert')->withConsecutive(
            ['tags', ['name' => 'hong kong', 'tag_group_id' => 1]],
            ['tags', ['name' => 'skyscraper', 'tag_group_id' => 1]],
            ['tags', ['name' => 'brutalism', 'tag_group_id' => 1]],
            );

        $files = [
            0 => $this->basePath . 'China/2002-04-00 Day in HongKong/alex-azabache-YM71ka72TNw-unsplash.jpg',
        ];

        $this->subject->parse($files);
    }

    // TODO Will be replaced by DB management via file & autoUpdate
    private function getTagGroup()
    {
        return [
            "people" => [
                0 => "friendA",
                1 => "friendB",
                2 => "friendC"
            ],
            "country" => [
                0 => "germany",
                1 => "sweden",
                2 => "denmark",
                3 => "greece",
                4 => "china"
            ],
            "city" => [
                0 => "amsterdam",
                1 => "london",
                2 => "berlin"
            ],
            "madeBy" => [
                0 => "friendA",
                1 => "friendC"
            ],
            "misc" => [
                0 => "dinner",
                1 => "party",
                2 => "study",
                3 => "goingOut",
                4 => "traveling",
                5 => "food",
                6 => "cute"
            ],
            "year" => [
                0 => "2000",
                1 => "2001",
                2 => "2002",
                3 => "2003"
            ]
        ];
    }
}
