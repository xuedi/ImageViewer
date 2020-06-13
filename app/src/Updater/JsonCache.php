<?php declare(strict_types=1);

namespace ImageViewer\Updater;

use Exception;
use ImageViewer\Database;
use ImageViewer\EventDate;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class JsonCache
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
        $this->output->write(' -> wrote jsonCache: ' . $this->path . PHP_EOL);


        // country cache (menu)
        $locationCache = [];
        foreach ($this->database->getLocations() as $id => $name) {
            $locationCache[$id] = [
                "name" => $name,
                "events" => [],
            ];
        }
        foreach ($this->database->getEventNames() as $eventObj) {
            $locationCache[$eventObj['locationId']]['events'][] = [
                'link' => $eventObj['id'],
                'name' => $eventObj['name'],
            ];
        }
        file_put_contents($this->path . '/locations.json', json_encode($locationCache, JSON_PRETTY_PRINT));

    }
}
