<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Extractors\EventExtractor;
use ImageViewer\Extractors\LocationExtractor;
use ImageViewer\Extractors\MetaExtractor;
use Symfony\Component\Console\Output\OutputInterface;

class ExtractorService
{
    private OutputInterface $output;
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

    public function scan(OutputInterface $output): void
    {
        $this->output = $output;

        $newFiles = $this->fileScanner->scan($output);
        dump($newFiles);
        $locations = $this->locationExtractor->parse($output, $newFiles);
        dump($locations);
        $events = $this->eventExtractor->parse($output, $newFiles, $locations);
        dump($events);
        $tags = $this->metaExtractor->parse($output, $newFiles);
        dump($tags);
        $files = $this->fileBuilder->parse($output, $newFiles, $locations, $events, $tags); // glue together
        dump($files);
    }
}
