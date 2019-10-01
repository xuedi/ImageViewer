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
            $files[] = $this->parseFile($newFile);
            $progressBar->advance();
        }
        $progressBar->advance();
        $progressBar->finish();

        $output->write(PHP_EOL);
        return $files;
    }

    private function parseFile(string $file): array
    {
        /*
        $imageExif = exif_read_data($file);
        if ($imageExif) {
            $returnExif = [
                'fileName' => $imageExif['FileName'] ?? null,
                'dateTime' => $imageExif['DateTime'] ?? null,
                'orientation' => $imageExif['Orientation'] ?? null,
            ];
        }
        getimagesize($file, $info);
         */
        return [
            'nameHash' => sha1($file),
            'fileHash' => sha1_file($file),
            'fileName' => $file,
            'createdAt' => date('Y-m-d H:i:s'),
        ];
    }
}
