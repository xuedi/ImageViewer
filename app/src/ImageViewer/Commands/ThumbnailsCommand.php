<?php declare(strict_types=1);

namespace ImageViewer\Commands;

use RuntimeException;
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

        $this->monitorProcessPool(
            $this->createProcessingPool($maxWorker)
        );

        $output->writeln('---');
        return 0;
    }

    private function createProcessingPool(int $maxWorker): array
    {
        $processesPool = [];
        for ($number = 0; $number < $maxWorker; $number++) {
            $process = $this->factory->startThumbnailProcess($number);
            $process->enableOutput();
            $process->start();

            $processesPool[] = $process;
        }
        return $processesPool;
    }

    private function monitorProcessPool(array $processesPool)
    {
        while (count($processesPool)) {
            usleep(20000); // sleep 0.02s to not bother the CPU TODO: exclude from tests via env?

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
    }
}
