<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Configuration\Configuration;
use ImageViewer\Controller\RegisterController;
use ImageViewer\Updater\Filesystem as updaterFilesystem;
use ImageViewer\Updater\Metadata as updaterMetadata;
use ImageViewer\Updater\Structure as updaterStructure;
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

    public function getUpdaterFilesystem(): updaterFilesystem
    {
        return new updaterFilesystem(
            $this->getDatabase(),
            $this->getOutput(),
            $this->getProgressBar(),
            $this->config->getImagePath()
        );
    }

    public function getUpdaterStructure(): updaterStructure
    {
        return new updaterStructure(
            $this->getDatabase(),
            $this->getOutput(),
            $this->getProgressBar(),
            $this->config->getImagePath()
        );
    }

    public function getUpdaterMetadata(): updaterMetadata
    {
        return new updaterMetadata(
            $this->getDatabase(),
            $this->getOutput(),
            $this->getProgressBar(),
            $this->config->getImagePath()
        );
    }

    public function getThumbnailManager(): ThumbnailManager
    {
        return new ThumbnailManager(
            $this->getDatabase(),
            $this->getThumbnailGenerator(),
            $this->getConfig()->getImagePath(),
            $this->getConfig()->getOptions()->getThreads()
        );
    }

    public function getRegisterController(): RegisterController
    {
        return new RegisterController(
            $this->getOutputWrapper(),
            $this->getDatabase()
        );
    }

    private function getThumbnailGenerator(): ThumbnailGenerator
    {
        return new ThumbnailGenerator();
    }

    private function getOutputWrapper(): OutputWrapper
    {
        return new OutputWrapper();
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
