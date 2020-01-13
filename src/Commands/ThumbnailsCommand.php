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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $maxWorker = 16;

        $output->write("Start $maxWorker worker ... ");

        pcntl_async_signals(true);

        $runningProcesses = [];
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

        return 0;
    }
}
