<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Extractors\EventExtractor;
use ImageViewer\Extractors\LocationExtractor;
use ImageViewer\Extractors\MetaExtractor;

class ThumbnailGenerator
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function run(int $thread = 0): void
    {
        $thumbPath = realpath(__DIR__ . '/../') . '/public/thumbs';
        $missingThumbnails = $this->database->getMissingThumbnails();
        dump($missingThumbnails);
    }
}
