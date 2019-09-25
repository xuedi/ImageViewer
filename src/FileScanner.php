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
            if (!$this->doesFileExist($file)) {
                $this->newFiles[] = $file;
            }
            $progressBar->advance();
        }
        $progressBar->advance();
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
            $this->toBeSaved[] = $this->parseFile($newFile);
            $progressBar->advance();
        }
        $progressBar->advance();
        $progressBar->finish();

        $output->write(PHP_EOL);
    }

    public function saveNewFiles(OutputInterface $output): void
    {
        $progressBar = new ProgressBar($output, count($this->toBeSaved));
        $progressBar->setFormat('Write:  [%bar%] %memory:6s%');
        $progressBar->start();
        foreach ($this->toBeSaved as $data) {
            // TODO: Add to PDO until 500 then flush
            $this->database->insert('files', $data);
            $progressBar->advance();
        }
        $progressBar->advance();
        $progressBar->finish();

        $output->write(PHP_EOL);
        // TODO: flush the rest
    }

    // Todo: PARAM: 'File' value object (must must exisit, get as string, get changed date, get fileHash, getNameHash)
    private function doesFileExist(string $file): bool
    {
        $hash = sha1($file);
        if (in_array($hash, $this->knownFiles)) {
            return true;
        }

        return false;
    }

    private function parseFile(string $file): array
    {
        return [
            'nameHash' => sha1($file),
            'fileHash' => sha1_file($file),
            'fileName' => $file,
            'createdAt' => date('Y-m-d H:i:s'),
        ];
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
