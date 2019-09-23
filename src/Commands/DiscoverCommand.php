<?php

namespace ImageViewer\Commands;

use ImageViewer\FileScanner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;


class DiscoverCommand extends FactoryCommand
{
    protected static $defaultName = 'app:discover';

    protected function configure()
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileScanner = $this->factory->getFileScanner();
        $fileScanner->search($output);
        $fileScanner->parse($output);
        $fileScanner->saveNewFiles($output);
    }
}