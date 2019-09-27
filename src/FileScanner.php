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

    /** @var string */
    private $path;

    /** @var OutputInterface */
    private $output;

    public function __construct(Database $database, string $path)
    {
        $this->database = $database;
        $this->path = $path;
    }

    public function scan(OutputInterface $output): void
    {
        $this->output = $output;

        $newFiles = $this->search();
        $toBeSaved = $this->parse($newFiles);
        $this->saveNewFiles($toBeSaved);
    }

    private function search(): array
    {
        $fileList = $this->makeFileList($this->path);
        $knownFiles = $this->database->getImages();

        $newFiles = [];

        $progressBar = new ProgressBar($this->output, count($this->fileList));
        $progressBar->setFormat('Search: [%bar%] %memory:6s%');
        $progressBar->start();
        foreach ($fileList as $file) {
            if (!in_array(sha1($file), $knownFiles)) {
                $newFiles[] = $file;
            }
            $progressBar->advance();
        }
        $progressBar->advance();
        $progressBar->finish();

        $this->output->write(PHP_EOL);
        return $newFiles;
    }

    private function parse(array $newFiles): array
    {
        $toBeSaved = [];

        $progressBar = new ProgressBar($this->output, count($newFiles));
        $progressBar->setFormat('Parse:  [%bar%] %memory:6s%');
        $progressBar->start();
        foreach ($newFiles as $newFile) {
            $toBeSaved[] = $this->parseFile($newFile);
            $progressBar->advance();
        }
        $progressBar->advance();
        $progressBar->finish();

        $this->output->write(PHP_EOL);
        return $toBeSaved;
    }

    private function saveNewFiles(array $toBeSaved): void
    {
        $progressBar = new ProgressBar($this->output, count($this->toBeSaved));
        $progressBar->setFormat('Write:  [%bar%] %memory:6s%');
        $progressBar->start();
        foreach ($toBeSaved as $data) {
            // TODO: Add to PDO until 500 then flush
            $this->database->insert('files', $data);
            $progressBar->advance();
        }
        $progressBar->advance();
        $progressBar->finish();

        $this->output->write(PHP_EOL);
        // TODO: flush the rest
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
