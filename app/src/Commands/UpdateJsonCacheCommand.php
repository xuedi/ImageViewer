<?php declare(strict_types=1);

namespace ImageViewer\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateJsonCacheCommand extends FactoryCommand
{
    protected function configure()
    {
        $this->setName('app:updateJsonCache');
        $this->setDescription('Rebuild the json cache for the frontend');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->factory->getUpdaterJsonCache()->update();

        return 0;
    }
}