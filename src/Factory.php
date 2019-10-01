<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Configuration\Configuration;
use ImageViewer\Configuration\TagGroupConfig;

class Factory
{
    /** @var Configuration */
    private $config;

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
            $this->config->getImagePath()
        );
    }

    public function getLocationExtractor(): LocationExtractor
    {
        return new LocationExtractor(
            $this->getDatabase(),
            $this->config->getImagePath()
        );
    }

    public function getEventExtractor(): EventExtractor
    {
        return new EventExtractor(
            $this->getDatabase(),
            $this->config->getImagePath()
        );
    }

    private function getMetaExtractor(): MetaExtractor
    {
        return new MetaExtractor(
            $this->getDatabase(),
            $this->config->getImagePath(),
            $this->config->getTagGroup()
        );
    }

    private function getFileBuilder(): FileBuilder
    {
        return new FileBuilder(
            $this->getDatabase(),
            $this->config->getImagePath()
        );
    }
}