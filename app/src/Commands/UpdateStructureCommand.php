<?php declare(strict_types=1);

namespace ImageViewer\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateStructureCommand extends FactoryCommand
{
    protected function configure()
    {
        $this->setName('app:updateStructure');
        $this->setDescription('Rebuild the location and the events');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->factory->getUpdaterStructure()->update();

        return 0;
    }
}