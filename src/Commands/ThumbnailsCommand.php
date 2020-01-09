<?php

namespace ImageViewer\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ThumbnailsCommand extends FactoryCommand
{
    protected static $defaultName = 'app:generateThumbnails';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Start X worker ... ');

        pcntl_async_signals(true);

        $runningProcesses = [];
        $maxWorker = 16;
        for ($numbers = 1; $numbers <= $maxWorker; $numbers++) {
            //$process = new Process(['./ImageViewer', 'app:generateThumbnails:worker', $number]);
            $process = new Process(['stress', '--cpu', '1', '--timeout', '2']);
            $process->disableOutput();
            $process->start();

            $runningProcesses[] = $process;
        }

        while (count($runningProcesses)) {
            foreach ($runningProcesses as $i => $runningProcess) {
                if (!$runningProcess->isRunning()) {
                    unset($runningProcesses[$i]);
                }
                sleep(1);
            }
        }

        $output->writeln('DONE');
    }
}
