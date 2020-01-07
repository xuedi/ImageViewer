<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Configuration\Configuration;
use ImageViewer\Extractors\EventExtractor;
use ImageViewer\Extractors\LocationExtractor;
use ImageViewer\Extractors\MetaExtractor;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class Factory
{
    private Configuration $config;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    public function getDatabase(): Database
    {
        return new Database($this->config->getDatabase());
    }

    public function getExtractorService(): ExtractorService
    {
        return new ExtractorService(
            $this->getFileScanner(),
            $this->getLocationExtractor(),
            $this->getEventExtractor(),
            $this->getMetaExtractor(),
            $this->getFileBuilder()
        );
    }

    public function getFileScanner(): FileScanner
    {
        return new FileScanner(
            $this->getDatabase(),
            $this->getOutput(),
            $this->getProgressBar(),
            $this->config->getImagePath()
        );
    }

    public function getLocationExtractor(): LocationExtractor
    {
        return new LocationExtractor(
            $this->getDatabase(),
            $this->getOutput(),
            $this->getProgressBar(),
            $this->config->getImagePath()
        );
    }

    public function getEventExtractor(): EventExtractor
    {
        return new EventExtractor(
            $this->getDatabase(),
            $this->getOutput(),
            $this->getProgressBar(),
            $this->config->getImagePath()
        );
    }

    private function getMetaExtractor(): MetaExtractor
    {
        return new MetaExtractor(
            $this->getDatabase(),
            $this->getOutput(),
            $this->config->getImagePath(),
            $this->config->getTagGroup()
        );
    }

    private function getFileBuilder(): FileBuilder
    {
        return new FileBuilder(
            $this->getDatabase(),
            $this->getOutput(),
            $this->getMetaExtractor(),
            $this->config->getImagePath()
        );
    }

    private function getOutput(): ConsoleOutput
    {
        return new ConsoleOutput();
    }

    private function getProgressBar(): ProgressBar
    {
        return new ProgressBar(
            $this->getOutput()
        );
    }
}
