<?php declare(strict_types=1);

namespace ImageViewer\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateMetadataCommand extends FactoryCommand
{
    protected function configure()
    {
        $this->setName('app:updateMetadata');
        $this->setDescription('Parses trough all new photos and updated metadata');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->factory->getUpdaterMetadata()->update();

        return 0;
    }
}