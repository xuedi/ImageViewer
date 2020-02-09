<?php declare(strict_types=1);

namespace ImageViewer\Extractors;

use ImageViewer\Database;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class LocationExtractor
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

    public function parse(array $fileNames): array
    {
        $this->progressBar->setMaxSteps(count($fileNames));
        $this->progressBar->setFormat('Locations: [%bar%] %memory:6s%');
        $this->progressBar->start();

        $locations = $this->database->getLocations();
        foreach ($fileNames as $fileName) {
            $this->progressBar->advance();
            if(substr($fileName,0, strlen($this->path)) == $this->path) {
                $fileName = substr($fileName,strlen($this->path));
            }
            $location = strtolower(explode('/', $fileName)[0]);
            if(!in_array($location, $locations)) {
                $locations[] = $location;
                $this->database->insert('locations', ['name' => $location]);
            }
        }
        $this->progressBar->advance();
        $this->progressBar->finish();

        $this->output->write(PHP_EOL);
        return $this->database->getLocations(true);
    }
}
