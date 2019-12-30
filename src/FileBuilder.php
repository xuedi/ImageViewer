<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Extractors\MetaExtractor;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class FileBuilder
{
    private string $path;
    private Database $database;
    private OutputInterface $output;
    private MetaExtractor $metaExtractor;

    public function __construct(Database $database, OutputInterface $output, MetaExtractor $metaExtractor, string $path)
    {
        $this->database = $database;
        $this->output = $output;
        $this->path = $path;
        $this->metaExtractor = $metaExtractor;
    }

    public function build(array $newFiles, array $events, array $tags): void
    {
        $progressBar = new ProgressBar($this->output, count($newFiles));
        $progressBar->setFormat('Files:     [%bar%] %memory:6s%');
        $progressBar->start();
        foreach ($newFiles as $newFile) {
            $file = $this->parseFile($newFile, $events);
            $this->parseTags($file, $tags, $this->database->insert('files', $file));

            $progressBar->advance();
        }
        $progressBar->advance();
        $progressBar->finish();

        $this->output->write(PHP_EOL);
    }

    private function parseFile(string $file, array $events): array
    {
        $imageExif = exif_read_data($file);
        list($width, $height) = getimagesize($file);

        $fileName = $file;
        if (substr($file, 0, strlen($this->path)) == $this->path) {
            $fileName = substr($file, strlen($this->path));
        }

        $event = strtolower(explode('/', $fileName)[1]);
        $eventName = trim(substr($event, 10));
        $eventId = $events[$eventName] ?? 0;

        return [
            'event_id' => $eventId,
            'nameHash' => sha1($file),
            'fileHash' => sha1_file($file),
            'fileName' => $fileName,
            'createdAt' => date('Y-m-d H:i:s', strtotime($imageExif['DateTime'] ?? date('Y-m-d H:i:s'))),
            'width' => $width,
            'height' => $height,
            'pixel' => $width * $height,
            'size' => filesize($file),
        ];
    }

    private function parseTags(array $file, array $tags, int $fileId): void
    {
        $fileName = $this->path . $file['fileName'];
        if(file_exists($fileName)) {
            $fileTags = $this->metaExtractor->getTags($fileName);
            foreach ($fileTags as $tag) {
                $tagId = $tags[strtolower($tag)] ?? null;
                if ($tagId == null) {
                    echo "Unknown TagId for '$tag'" . PHP_EOL;
                    continue;
                }
                $this->database->insert('file_tags', [
                    'file_id' => $fileId,
                    'tag_id' => $tagId
                ]);
            }
        }
    }
}
