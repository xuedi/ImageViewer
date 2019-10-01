<?php declare(strict_types=1);

namespace ImageViewer;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class FileWriter
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

    public function write(OutputInterface $output, array $files): array
    {
        $progressBar = new ProgressBar($output, count($files));
        $progressBar->setFormat('Locations: [%bar%] %memory:6s%');
        $progressBar->start();

        foreach ($files as $file) {
            $progressBar->advance();
            // TODO: Add to PDO until 500 then flush
            $this->database->insert('files', $file);
        }
        $progressBar->advance();
        $progressBar->finish();

        $output->write(PHP_EOL);
    }
}
