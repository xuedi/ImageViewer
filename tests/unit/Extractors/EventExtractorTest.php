<?php declare(strict_types=1);

namespace ImageViewer\Extractors;

use ImageViewer\Database;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

final class EventExtractorTest extends TestCase
{
    /** @var string */
    private string $basePath;

    /** @var MockObject|Database */
    private $database;

    /** @var MockObject|ProgressBar */
    private $progressBar;

    /** @var MockObject|ConsoleOutput */
    private $consoleOutput;

    /** @var EventExtractor */
    private $subject;

    protected function setUp(): void
    {
        $this->basePath = realpath(__DIR__ . '/../../resources/images/') . '/';
        $this->progressBar = $this->createMock(ProgressBar::class);
        $this->consoleOutput = $this->createMock(ConsoleOutput::class);

        $this->database = $this->createMock(Database::class);
        $this->database->method('getLocations')->willReturn([]);

        $this->subject = new EventExtractor(
            $this->database,
            $this->consoleOutput,
            $this->progressBar,
            $this->basePath
        );
    }

    public function testCanBuild(): void
    {
        $this->assertInstanceOf(EventExtractor::class, $this->subject);
    }

    public function testCanParseNoLocations(): void
    {
        $this->database
            ->expects($this->exactly(3))
            ->method('insert')
            ->withConsecutive(
                ['events', ['locationId' => 1, 'name' => 'eventa', 'date' => '0000-00-00']],
                ['events', ['locationId' => 1, 'name' => 'eventb', 'date' => '2019-10-22']],
                ['events', ['locationId' => 1, 'name' => 'eventc', 'date' => '2020-01-00']],
                );

        $locations = [];
        $files = [
            $this->basePath . 'China/0000-00-00 EventA',
            $this->basePath . 'China/2019-10-22 EventB',
            $this->basePath . 'Germany/2020-01-00 EventC'
        ];

        $this->subject->parse($files, $locations);
    }

    public function testCanParseWithKnownLocations(): void
    {
        $this->database
            ->expects($this->exactly(3))
            ->method('insert')
            ->withConsecutive(
                ['events', ['locationId' => 10, 'name' => 'eventa', 'date' => '0000-00-00']],
                ['events', ['locationId' => 20, 'name' => 'eventb', 'date' => '2019-10-22']],
                ['events', ['locationId' => 1, 'name' => 'eventc', 'date' => '2020-01-00']],
                );

        $locations = [
            'italy' => 10,
            'china' => 20,
        ];
        $files = [
            $this->basePath . 'Italy/0000-00-00 EventA',
            $this->basePath . 'China/2019-10-22 EventB',
            $this->basePath . 'Germany/2020-01-00 EventC'
        ];

        $this->subject->parse($files, $locations);
    }

    public function testCanParseWithInvalidDate(): void
    {
        $this->database->expects($this->exactly(2))->method('insert')->withConsecutive(
            ['events', ['locationId' => 1, 'name' => 'eventb', 'date' => '2019-10-22']],
            ['events', ['locationId' => 1, 'name' => 'eventc', 'date' => '2020-01-00']],
            );

        $locations = [];
        $files = [
            $this->basePath . 'Italy/DEAD-MEAT EventA', // will be ignored due to invalid date
            $this->basePath . 'China/2019-10-22 EventB',
            $this->basePath . 'Germany/2020-01-00 EventC'
        ];

        $this->subject->parse($files, $locations);
    }
}
