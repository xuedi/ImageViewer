<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Configuration\Configuration;
use ImageViewer\Configuration\DatabaseConfig;
use Symfony\Component\Console\Output\OutputInterface;

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

    public function getFileScanner(): FileScanner
    {
        return new FileScanner(
            $this->getDatabase(),
            $this->config->getImagePath()
        );
    }
}