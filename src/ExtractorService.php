<?php declare(strict_types=1);

namespace ImageViewer;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class ExtractorService
{
    /** @var OutputInterface */
    private $output;

    /** @var LocationExtractor */
    private $locationExtractor;

    /** @var EventExtractor */
    private $eventExtractor;

    /** @var FileScanner */
    private $fileScanner;

    /** @var FileBuilder */
    private $fileBuilder;

    /** @var MetaExtractor */
    private $metaExtractor;

    /** @var FileWriter */
    private $fileWriter;

    public function __construct(
        FileScanner $fileScanner,
        LocationExtractor $locationExtractor,
        EventExtractor $eventExtractor,
        MetaExtractor $metaExtractor,
        FileBuilder $fileBuilder,
        FileWriter $fileWriter
    )
    {
        $this->fileScanner = $fileScanner;
        $this->eventExtractor = $eventExtractor;
        $this->locationExtractor = $locationExtractor;
        $this->fileWriter = $fileWriter;
        $this->metaExtractor = $metaExtractor;
        $this->fileBuilder = $fileBuilder;
    }

    public function scan(OutputInterface $output): void
    {
        $this->output = $output;

        $newFiles = $this->fileScanner->scan($output);
        $locations = $this->locationExtractor->getBy($output, $newFiles);
        $events = $this->eventExtractor->getBy($output, $newFiles, $locations);
        $tags = $this->metaExtractor->parse($output, $newFiles);
        $files = $this->fileBuilder->parse($output, $newFiles, $locations, $events, $tags); // glue together
        $this->fileWriter->write($output, $files);
        dump($events);
    }
}
