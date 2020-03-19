<?php declare(strict_types=1);

namespace ImageViewer\Extractors;

use ImageViewer\Database;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @covers \ImageViewer\Extractors\LocationExtractor
 */
final class LocationExtractorTest extends TestCase
{
    /** @var MockObject|Database */
    private $database;

    /** @var MockObject|ProgressBar */
    private $progressBar;

    /** @var MockObject|ConsoleOutput */
    private $consoleOutput;

    private string $basePath;
    private LocationExtractor $subject;

    protected function setUp(): void
    {
        $this->basePath = realpath(__DIR__ . '/../../resources/images/') . '/';
        $this->progressBar = $this->createMock(ProgressBar::class);
        $this->consoleOutput = $this->createMock(ConsoleOutput::class);

        $this->database = $this->createMock(Database::class);
        $this->database->method('getLocations')->willReturn([]);

        $this->subject = new LocationExtractor(
            $this->database,
            $this->consoleOutput,
            $this->progressBar,
            $this->basePath
        );
    }

    public function testCanBuild(): void
    {
        $this->assertInstanceOf(LocationExtractor::class, $this->subject);
    }

    public function testCanParseNoLocations(): void
    {
        $this->database->expects($this->exactly(2))->method('insert')->withConsecutive(
            ['locations', ['name' => 'china']],
            ['locations', ['name' => 'germany']],
            );

        $files = [
            $this->basePath . 'China/0000-00-00 EventA',
            $this->basePath . 'China/2019-10-22 EventB',
            $this->basePath . 'Germany/2020-01-00 EventC'
        ];

        $this->subject->parse($files);
    }
}
