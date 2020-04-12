<?php declare(strict_types=1);

namespace ImageViewer\Updater;

use ImageViewer\Database;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class Filesystem
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
        $fileSystemImages = $this->makeFileList($this->path);
        $this->insertUpdate($fileSystemImages);
        $this->markOrphaned($fileSystemImages);
    }

    private function insertUpdate(array $fileSystemImages): void
    {
        $databaseImageNames = $this->database->getAllImagesNames();
        $databaseImageHashes = $this->database->getImagesHashes();
        $renamedImages = 0;
        $newImages = 0;
        $maxImages = 1000000;

        $this->output->write(PHP_EOL);

        $this->progressBar->setMaxSteps(count($fileSystemImages));
        $this->progressBar->setFormat('Syncing filesystem and database: [%bar%] %memory:6s%');
        $this->progressBar->start();

        foreach ($fileSystemImages as $file) {
            if($newImages >= $maxImages) {
                continue; // development
            }

            $fileName = $this->getFileName($file);
            if (in_array($fileName, $databaseImageNames)) {
                $this->progressBar->advance();
                continue; // got this already, skip (detailed verification with the integrity checker)
            }

            $fileHash = sha1_file($file);
            if (!in_array($fileHash, $databaseImageHashes)) {
                $this->saveFile($fileHash, $fileName);
                $newImages++;
            } else {
                $id = array_search($fileHash, $databaseImageHashes);
                $this->updateName($id, $fileName);
                $renamedImages++;
            }

            $this->progressBar->advance();
        }
        $this->progressBar->advance();
        $this->progressBar->finish();

        $this->output->write(PHP_EOL);
        $this->output->write(' -> database: ' . count($databaseImageNames) . PHP_EOL);
        $this->output->write(' -> filesystem: ' . count($fileSystemImages) . PHP_EOL);
        $this->output->write(' -> newImages: ' . $newImages . PHP_EOL);
        $this->output->write(' -> renamedImages: ' . $renamedImages . PHP_EOL);
    }

    private function markOrphaned(array $fileSystemImages)
    {
        $orphaned = 0;
        $fileSystemImages = array_flip($fileSystemImages); // flip faster than search
        $databaseImageNames = $this->database->getAllImagesNames();
        foreach ($databaseImageNames as $id => $fileName) {
            $file = $this->path . $fileName;
            if (!isset($fileSystemImages[$file])) {
                $this->updateStatus($id, 4);
                $orphaned++;
            }
        }
        $this->output->write(' -> markOrphaned: ' . $orphaned . PHP_EOL);
    }

    private function makeFileList(string $dir, array &$results = []): array
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

    private function getFileName($file): string
    {
        if (substr($file, 0, strlen($this->path)) == $this->path) {
            $file = substr($file, strlen($this->path));
        }
        return $file;
    }

    private function saveFile(string $fileHash, string $fileName)
    {
        $this->database->insert(
            'files',
            [
                'event_id' => 1,
                'status_id' => 1,
                'fileHash' => $fileHash,
                'fileName' => $fileName,
                'width' => 0,
                'height' => 0,
                'pixel' => 0,
                'size' => 0,
            ]
        );
    }

    private function updateName(int $id, string $fileName)
    {
        $this->database->update('files', $id, ['fileName' => $fileName]);
    }

    private function updateStatus(int $id, int $newStatus)
    {
        $this->database->update('files', $id, ['status_id' => $newStatus]);
    }
}
