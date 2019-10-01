<?php declare(strict_types=1);

namespace ImageViewer;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

// TODO: To be put into a nice service

class FileScanner
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

    public function scan(OutputInterface $output): array
    {
        $fileList = $this->makeFileList($this->path);
        $knownFiles = $this->database->getImages();

        $newFiles = [];

        $progressBar = new ProgressBar($output, count($fileList));
        $progressBar->setFormat('Search:    [%bar%] %memory:6s%');
        $progressBar->start();
        foreach ($fileList as $file) {
            if (!in_array(sha1($file), $knownFiles)) {
                $newFiles[] = $file;
            }
            $progressBar->advance();
        }
        $progressBar->advance();
        $progressBar->finish();

        $output->write(PHP_EOL);
        return $newFiles;
    }

    private function makeFileList($dir, &$results = [])
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value != "." && $value != "..") {
                $this->makeFileList($path, $results);
            }
        }

        return $results;
    }
}
