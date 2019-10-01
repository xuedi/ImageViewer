<?php declare(strict_types=1);

namespace ImageViewer;

use Error;
use Exception;
use RuntimeException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

// TODO: To be put into a nice service

class EventExtractor
{
    /** @var Database */
    private $database;

    /** @var string */
    private $path;

    public function __construct(Database $database, string $path)
    {
        $this->database = $database;
        $this->path = $path;
    }

    public function getBy(OutputInterface $output, array $fileNames, array $locationIds): array
    {
        $progressBar = new ProgressBar($output, count($fileNames));
        $progressBar->setFormat('Events:    [%bar%] %memory:6s%');
        $progressBar->start();

        $events = $this->database->getLocations();
        foreach ($fileNames as $fileName) {
            $progressBar->advance();
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
        $progressBar->advance();
        $progressBar->finish();

        $output->write(PHP_EOL);

        return $this->database->getEvents();
    }
}
