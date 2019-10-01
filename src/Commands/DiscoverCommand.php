<?php

namespace ImageViewer\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class DiscoverCommand extends FactoryCommand
{
    protected static $defaultName = 'app:discover';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->factory->getExtractorService()->scan($output);
    }
}