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
        $this->setDescription('Regenerates all thumbnails');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $maxWorker = $this->factory->getConfig()->getOptions()->getThreads();

        $output->writeln("--- Start $maxWorker worker --- ");

        pcntl_async_signals(true);

        $runningProcesses = [];
        for ($number = 0; $number < $maxWorker; $number++) {
            $process = new Process(['./ImageViewer', 'app:generateThumbnails:worker', "$number"]);
            $process->enableOutput();
            $process->setInput($number);
            $process->start();

            $runningProcesses[] = $process;
        }

        while (count($runningProcesses)) {
            /** @var Process $runningProcess */
            foreach ($runningProcesses as $i => $runningProcess) {
                if (!$runningProcess->isRunning()) {


                    foreach ($runningProcess as $type => $data) {
                        if ($runningProcess::OUT === $type) {
                            echo (string)$data . PHP_EOL;
                        } else {
                            echo "Worker::Error: " . (string)$data . PHP_EOL;
                        }
                    }


                    //$runningProcess->

                    unset($runningProcesses[$i]);
                }
            }
        }

        $output->writeln('---');
    }
}
