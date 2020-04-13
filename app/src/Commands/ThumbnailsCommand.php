<?php declare(strict_types=1);

namespace ImageViewer\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ThumbnailsCommand extends FactoryCommand
{
    protected function configure()
    {
        $this->setName('app:generateThumbnails');
        $this->setDescription('Generates missing thumbnails');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $maxWorker = $this->factory->getConfig()->getOptions()->getThreads();

        $output->writeln("--- Start $maxWorker worker --- ");

        pcntl_async_signals(true);

        $processesPool = [];
        for ($number = 0; $number < $maxWorker; $number++) {
            $process = $this->factory->startThumbnailProcess($number);
            $process->enableOutput();
            $process->start();

            $processesPool[] = $process;
        }

        while (count($processesPool)) {
            // TODO: do microsleep to not use the CPU for just looping

            /** @var Process $runningProcess */
            foreach ($processesPool as $i => $runningProcess) {
                if (!$runningProcess->isRunning()) {

                    foreach ($runningProcess as $type => $data) {
                        if ($runningProcess::OUT !== $type) {
                            echo "Worker::Error: " . (string)$data . PHP_EOL;
                        } else {
                            echo (string)$data . PHP_EOL;
                        }
                    }

                    unset($processesPool[$i]);
                }
            }
        }

        $output->writeln('---');
        return 0;
    }
}
