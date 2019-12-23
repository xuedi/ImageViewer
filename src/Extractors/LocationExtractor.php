<?php declare(strict_types=1);

namespace ImageViewer\Extractors;

use ImageViewer\Database;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class LocationExtractor
{
    private string $path;
    private Database $database;
    private OutputInterface $output;

    public function __construct(Database $database, OutputInterface $output, string $path)
    {
        $this->database = $database;
        $this->output = $output;
        $this->path = $path;
    }

    public function parse(array $fileNames): array
    {
        $progressBar = new ProgressBar($this->output, count($fileNames));
        $progressBar->setFormat('Locations: [%bar%] %memory:6s%');
        $progressBar->start();

        $locations = $this->database->getLocations();
        foreach ($fileNames as $fileName) {
            $progressBar->advance();
            if(substr($fileName,0, strlen($this->path)) == $this->path) {
                $fileName = substr($fileName,strlen($this->path));
            }
            $location = strtolower(explode('/', $fileName)[0]);
            if(!in_array($location, $locations)) {
                $locations[] = $location;
                $this->database->insert('locations', ['name' => $location]);
            }
        }
        $progressBar->advance();
        $progressBar->finish();

        $this->output->write(PHP_EOL);
        return $this->database->getLocations(true);
    }
}
