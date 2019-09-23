<?php declare(strict_types=1);

namespace ImageViewer;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

// TODO: To be put into a nice service

class FileScanner
{
    /** @var Database */
    private $database;

    /** @var array */
    private $fileList;

    /** @var array */
    private $newFiles;

    /** @var array */
    private $toBeSaved;

    /** @var array */
    private $knownFiles;

    public function __construct(Database $database, string $path)
    {
        $this->database = $database;
        $this->fileList = $this->makeFileList($path);
        $this->knownFiles = $this->database->getImages();
    }

    public function search(OutputInterface $output)
    {
        $this->newFiles = [];

        $progressBar = new ProgressBar($output, count($this->fileList));
        $progressBar->setFormat('Search: [%bar%] %memory:6s%');
        $progressBar->start();
        foreach ($this->fileList as $file) {
            if($this->doesFileExist($file)) {
                $newFiles[] = $file;
            }
            $progressBar->advance();
        }
        $progressBar->finish();

        $output->write(PHP_EOL);
    }

    public function parse(OutputInterface $output)
    {
        $this->toBeSaved = [];

        $progressBar = new ProgressBar($output, count($this->newFiles));
        $progressBar->setFormat('Parse:  [%bar%] %memory:6s%');
        $progressBar->start();
        foreach ($this->newFiles as $newFile) {
            $this->parseFile($newFile);
            $progressBar->advance();
        }
        $progressBar->finish();

        $output->write(PHP_EOL);
    }

    public function saveNewFiles(OutputInterface $output): void
    {
        $progressBar = new ProgressBar($output, count($this->toBeSaved));
        $progressBar->setFormat('Write:  [%bar%] %memory:6s%');
        $progressBar->start();
        foreach ($this->toBeSaved as $file) {
            // TODO: Add to PDO until 500 then flush
            $progressBar->advance();
        }
        $progressBar->finish();

        $output->write(PHP_EOL);
        // TODO: flush the rest
    }

    private function doesFileExist(string $file): bool
    {
        $hash = sha1($file);
        if(isset($this->knownFiles[$hash])) {
            return false;
        }

        return true;
    }

    private function parseFile(string $file): void
    {
        // TODO: check file details and add values as to be saved into the database cache
        /*
            "af139927012e76633f585d07ddbaccb81297defa": { // is name hash
                "fileName": "China\/2006-01 Leshan\/2006-01-26 Abreise aus Beijing\/p1000736.jpg",
                "fileHash": "f085b5aac5a55888cf6ecb3f8106625d74e7db88",
                "meta": {
                    "fileName": "p1000736.jpg",
                    "dateTime": "2010:05:31 01:04:55",
                    "orientation": 1,
                    "tags": [
                        "2006",
                        "2006 Chinese new year",
                        "Leshan",
                        "events",
                        "inChina",
                        "places",
                        "timeline"
                    ]
                }
            },
         */
        $this->toBeSaved[] = $file;
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
