<?php

namespace ImageViewer\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ThumbnailsWorkerCommand extends FactoryCommand
{
    protected static $defaultName = 'app:generateThumbnails:worker';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('start worker');
        sleep(2);
        $output->writeln('stop worker');
        //$this->factory->getThumbnailGenerator()->run();
    }
}
