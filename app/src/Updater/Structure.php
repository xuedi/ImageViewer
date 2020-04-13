<?php declare(strict_types=1);

namespace ImageViewer\Updater;

use Exception;
use ImageViewer\Database;
use ImageViewer\EventDate;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class Structure
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

    public function update(): void
    {
        $databaseImageNames = $this->database->getAllImagesNames();
        $previousLocations = $this->database->deleteLocations() + 1;
        $previousEvents = $this->database->deleteEvents() + 1;

        $this->output->write(PHP_EOL);

        $this->progressBar->setMaxSteps(count($databaseImageNames) * 2);
        $this->progressBar->setFormat('Restructuring: [%bar%] %memory:6s%');
        $this->progressBar->start();

        $locations = [1 => 'unknown'];
        $events = [1 => 'unknown'];

        foreach ($databaseImageNames as $fileName) {
            $locations = $this->rebuildLocations($locations, $fileName);
            $this->progressBar->advance();
        }

        foreach ($databaseImageNames as $fileName) {
            $events = $this->rebuildEvents($events, $locations, $fileName);
            $this->progressBar->advance();
        }

        $this->progressBar->advance();
        $this->progressBar->finish();

        $this->output->write(PHP_EOL);
        $this->output->write(' -> previousLocations: ' . $previousLocations . PHP_EOL);
        $this->output->write(' -> previousEvents: ' . $previousEvents . PHP_EOL);
        $this->output->write(' -> currentLocations: ' . count($locations) . PHP_EOL);
        $this->output->write(' -> currentEvents: ' . count($events) . PHP_EOL);
    }

    private function rebuildLocations(array $locations, string $fileName): array
    {
        $locationName = strtolower(explode('/', $fileName)[0]);
        if (!in_array($locationName, $locations)) {
            $locationId = $this->database->insert('locations', ['name' => $locationName]);
            $locations[$locationId] = $locationName;
        }

        return $locations;
    }

    private function rebuildEvents(array $events, array $locations, string $fileName): array
    {
        $location = strtolower(explode('/', $fileName)[0]);
        $event = strtolower(explode('/', $fileName)[1]);
        try {
            $eventDate = EventDate::fromString(substr($event, 0, 10));
            $eventName = trim(substr($event, 10));
            if (!in_array($event, $events)) {
                $eventId = $this->database->insert('events', [
                    'locationId' => array_search($location, $locations) ?? 1,
                    'name' => $eventName,
                    'date' => $eventDate->asString(),
                ]);
                $events[$eventId] = $event;
            }
        } catch (Exception $e) {
            echo "Could not process: '$fileName': " . $e->getMessage() . PHP_EOL;
        }
        return $events;
    }
}
