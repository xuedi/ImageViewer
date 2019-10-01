<?php declare(strict_types=1);

namespace ImageViewer;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class FileBuilder
{
    /** @var Database */
    private $database;

    /** @var string */
    private $path;

    /** @var OutputInterface */
    private $output;

    public function __construct(Database $database, string $path)
    {
        $this->database = $database;
        $this->path = $path;
    }

    public function parse(OutputInterface $output, array $newFiles, array $locations, array $events, array $tags): array
    {
        $files = [];

        $progressBar = new ProgressBar($output, count($newFiles));
        $progressBar->setFormat('Files:     [%bar%] %memory:6s%');
        $progressBar->start();
        foreach ($newFiles as $newFile) {
            $this->database->insert('files', $this->parseFile($newFile));
            $progressBar->advance();
        }
        $progressBar->advance();
        $progressBar->finish();

        $output->write(PHP_EOL);
        return $files;
    }

    private function parseFile(string $file): array
    {
        $imageExif = exif_read_data($file);
        list($width, $height) = getimagesize($file);

        $fileName = $file;
        if(substr($file,0, strlen($this->path)) == $this->path) {
            $fileName = substr($file,strlen($this->path));
        }

        return [
            'nameHash' => sha1($file),
            'fileHash' => sha1_file($file),
            'fileName' => $fileName,
            'createdAt' => date('Y-m-d H:i:s',strtotime($imageExif['DateTime'] ?? date())),
            'width' => $width,
            'height' => $height,
            'size' => filesize($file),
        ];
    }
}
