<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Extractors\EventExtractor;
use ImageViewer\Extractors\LocationExtractor;
use ImageViewer\Extractors\MetaExtractor;

class ExtractorService
{
    private LocationExtractor $locationExtractor;
    private EventExtractor $eventExtractor;
    private FileScanner $fileScanner;
    private FileBuilder $fileBuilder;
    private MetaExtractor $metaExtractor;

    public function __construct(
        FileScanner $fileScanner,
        LocationExtractor $locationExtractor,
        EventExtractor $eventExtractor,
        MetaExtractor $metaExtractor,
        FileBuilder $fileBuilder
    )
    {
        $this->fileScanner = $fileScanner;
        $this->eventExtractor = $eventExtractor;
        $this->locationExtractor = $locationExtractor;
        $this->metaExtractor = $metaExtractor;
        $this->fileBuilder = $fileBuilder;
    }

    public function scan(): void
    {
        $newFiles = $this->fileScanner->scan();
        $locations = $this->locationExtractor->parse($newFiles);
        $events = $this->eventExtractor->parse($newFiles, $locations);
        $tags = $this->metaExtractor->parse($newFiles);
        $this->fileBuilder->build($newFiles, $events, $tags); // glue together
    }
}
