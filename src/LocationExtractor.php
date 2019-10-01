<?php declare(strict_types=1);

namespace ImageViewer;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class LocationExtractor
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

    public function getBy(OutputInterface $output, array $fileNames): array
    {
        $progressBar = new ProgressBar($output, count($fileNames));
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

        $output->write(PHP_EOL);
        return $this->database->getLocations(true);
    }
}
