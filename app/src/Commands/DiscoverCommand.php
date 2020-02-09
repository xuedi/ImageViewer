<?php declare(strict_types=1);

namespace ImageViewer\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiscoverCommand extends FactoryCommand
{
    protected function configure()
    {
        $this->setName('app:discover');
        $this->setDescription('Checks all files and updates its database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->factory->getExtractorService()->scan();

        return 0;
    }
}