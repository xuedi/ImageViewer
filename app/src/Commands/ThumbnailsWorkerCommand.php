<?php declare(strict_types=1);

namespace ImageViewer\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ThumbnailsWorkerCommand extends FactoryCommand
{
    protected function configure()
    {
        $this->setName('app:generateThumbnails:worker');
        $this->setDescription('Internal thumbnail generator, please use \'app:generateThumbnails\' instead');
        $this->addArgument('thread', InputArgument::REQUIRED, 'The number of the worker');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $worker = (int)$input->getArgument('thread');

        $generated = $this->factory->getThumbnailManager()->run($worker);

        $output->write("worker $worker: $generated");

        return 0;
    }
}
