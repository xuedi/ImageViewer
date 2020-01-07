<?php declare(strict_types=1);

namespace ImageViewer\Extractors;

use Error;
use Exception;
use ImageViewer\Database;
use ImageViewer\EventDate;
use RuntimeException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class EventExtractor
{
    private string $path;
    private Database $database;
    private ProgressBar $progressBar;
    private OutputInterface $output;

    public function __construct(Database $database, OutputInterface $output, ProgressBar $progressBar, string $path)
    {
        $this->progressBar = $progressBar;
        $this->database = $database;
        $this->output = $output;
        $this->path = $path;
    }

    public function parse(array $fileNames, array $locationIds): array
    {
        $this->progressBar->setMaxSteps(count($fileNames));
        $this->progressBar->setFormat('Events:    [%bar%] %memory:6s%');
        $this->progressBar->start();

        $events = $this->database->getLocations();
        foreach ($fileNames as $fileName) {
            $this->progressBar->advance();
            if (substr($fileName, 0, strlen($this->path)) == $this->path) {
                $fileName = substr($fileName, strlen($this->path));
            }
            $location = strtolower(explode('/', $fileName)[0]);
            $event = strtolower(explode('/', $fileName)[1]);
            try {
                $eventDate = EventDate::fromString(substr($event, 0, 10));
                $eventName = trim(substr($event, 10));
                if (!in_array($event, $events)) {
                    $events[] = $event;
                    $this->database->insert('events', [
                        'locationId' => $locationIds[$location] ?? 1,
                        'name' => $eventName,
                        'date' => $eventDate->asString(),
                    ]);
                }
            } catch (Exception $e) {
                continue;
            }
        }
        $this->progressBar->advance();
        $this->progressBar->finish();

        $this->output->write(PHP_EOL);

        return $this->database->getEvents(true);
    }
}
