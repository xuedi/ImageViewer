<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Configuration\Configuration;
use ImageViewer\Controller\Controller;
use ImageViewer\Controller\RegisterController;
use ImageViewer\Extractors\EventExtractor;
use ImageViewer\Extractors\LocationExtractor;
use ImageViewer\Extractors\MetaExtractor;
use PDO;
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
        return new Database($this->getPdo());
    }

    public function getPdo(): PDO
    {
        $config = $this->config->getDatabase();
        $options = [
            PDO::ATTR_EMULATE_PREPARES => false, // turn off emulation mode for "real" prepared statements
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
        ];
        return new PDO(
            $config->getDsn(),
            $config->getUser(),
            $config->getPass(),
            $options
        );
    }

    public function getConfig(): Configuration
    {
        return $this->config;
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

    public function getThumbnailGenerator(): ThumbnailGenerator
    {
        return new ThumbnailGenerator(
            $this->getDatabase(),
            $this->getConfig()->getImagePath(),
            $this->getConfig()->getOptions()->getThreads()
        );
    }

    public function getRegisterController(): RegisterController
    {
        return new RegisterController(
            $this->getDatabase()
        );
    }

    private function getMetaExtractor(): MetaExtractor
    {
        return new MetaExtractor(
            $this->getDatabase(),
            $this->getOutput(),
            $this->getProgressBar(),
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
            $this->getProgressBar(),
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
