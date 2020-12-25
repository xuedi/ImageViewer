<?php declare(strict_types=1);

namespace ImageViewer;

use Exception;
use ImageViewer\DataTransferObjects\MissingThumbnail;

class ThumbnailManager
{
    private Database $database;
    private ThumbnailGenerator $generator;
    private int $maxThreads;
    private string $imagePath;

    public function __construct(Database $database, ThumbnailGenerator $generator, string $imagePath, int $maxThreads)
    {
        $this->database = $database;
        $this->maxThreads = $maxThreads;
        $this->imagePath = $imagePath;
        $this->generator = $generator;
    }

    public function run(int $thread = 0): int
    {
        $generated = 0;

        $thumbPath = realpath(__DIR__ . '/../../') . '/public/thumbs/';
        $imagePath = realpath($this->imagePath) . '/';

        // TODO: massive fucked up code,clean up!!
        $missingThumbnails = $this->database->getMissingThumbnails();

        $chunkSize = (int)ceil(count($missingThumbnails) / $this->maxThreads);
        if ($chunkSize == 0) {
            return $generated;
        }
        $workLoad = array_chunk($missingThumbnails, $chunkSize);
        if (!isset($workLoad[$thread])) {
            return $generated;
        }

        /** @var MissingThumbnail $item */
        foreach ($workLoad[$thread] as $item) {
            try {
                $size = $item->getSize();
                $file_id = $item->getFileId();
                $size_id = $item->getSizeId();
                $file = $imagePath . $item->getFile();
                $thumbnail = $thumbPath . $item->getName();
                $this->generator->create($file, $size, $thumbnail);
                $this->database->insert('thumbs', ['file_id' => $file_id, 'size_id' => $size_id]);
                $generated++;
            } catch (Exception $e) {
                continue;
            }
        }
        return $generated;
    }
}
