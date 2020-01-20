<?php declare(strict_types=1);

namespace ImageViewer\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ThumbnailsWorkerCommand extends FactoryCommand
{
    protected function configure()
    {
        $this->setName('app:generateThumbnails:worker');
        $this->setDescription('Internal thumbnail generator, please use \'app:generateThumbnails\' instead');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('start worker');

        $this->factory->getThumbnailGenerator()->run();

        $output->writeln('stop worker');

        return 0;
    }
}
