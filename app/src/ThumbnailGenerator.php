<?php declare(strict_types=1);

namespace ImageViewer;

use Exception;

class ThumbnailGenerator
{
    private Database $database;
    private int $maxThreads;
    private string $imagePath;

    public function __construct(Database $database, string $imagePath, int $maxThreads)
    {
        $this->database = $database;
        $this->maxThreads = $maxThreads;
        $this->imagePath = $imagePath;
    }

    public function run(int $thread = 0): int
    {
        $generated = 0;

        $thumbPath = realpath(__DIR__ . '/../../') . '/public/thumbs/';

        $imagePath = realpath($this->imagePath) . '/';
        $missingThumbnails = $this->database->getMissingThumbnails();

        $chunkSize = (int)ceil(count($missingThumbnails) / $this->maxThreads);
        if ($chunkSize == 0) {
            return $generated;
        }
        $workLoad = array_chunk($missingThumbnails, $chunkSize);
        if (!isset($workLoad[$thread])) {
            return $generated;
        }
        foreach ($workLoad[$thread] as $item) {
            $size = (int)$item['size'];
            $file_id = (int)$item['file_id'];
            $size_id = (int)$item['size_id'];
            $file = $imagePath . $item['file'];
            $thumbnail = $thumbPath . $item['name'];
            $this->database->insert('thumbs', ['file_id' => $file_id, 'size_id' => $size_id]);
            $this->generate($file, $size, $thumbnail);
            $generated++;
        }
        return $generated;
    }

    private function generate(string $file, int $size, string $thumbnail): void
    {
        if (!file_exists($file)) {
            throw new Exception("Could not find file '$file'");
        }
        if (file_exists($thumbnail)) {
            throw new Exception("Thumbnail already exist '$thumbnail'");
        }

    }
}
