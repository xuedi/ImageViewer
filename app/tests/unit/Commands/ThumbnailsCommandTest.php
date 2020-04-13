<?php declare(strict_types=1);

namespace ImageViewer\Commands;

use ImageViewer\Configuration\Configuration;
use ImageViewer\Configuration\OptionsConfig;
use ImageViewer\Factory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\Process;

/**
 * @covers \ImageViewer\Commands\ThumbnailsCommand
 * @covers \ImageViewer\Commands\FactoryCommand
 */
final class ThumbnailsCommandTest extends TestCase
{
    public function testCanExecuteCommand(): void
    {
        $this->setOutputCallback(function () {
        });

        $expectedSimulatedLoops = 10;
        $expectedNumberOfThreads = 1; // only do one thread! (concurrency, yeah)

        $isRunningCalls = $array = array_fill(0, ($expectedSimulatedLoops-1), true);
        $isRunningCalls[] = false; // X times 'true', last one is 'false', then unwindas vars with '...'
        $iteratorObjects = [];

        $factory = $this->createMock(Factory::class);
        $optionsMock = $this->createMock(OptionsConfig::class);
        $processMock = $this->createMock(Process::class);
        $configMock = $this->createMock(Configuration::class);

        $processMock
            ->expects($this->exactly($expectedSimulatedLoops))
            ->method('isRunning')
            ->will($this->onConsecutiveCalls(...$isRunningCalls)); // unwind the X arguments again

        $processMock
            //->expects($this->exactly($expectedSimulatedLoops))
            ->method('getIterator')
            ->willReturn(['key1'=>'value1']);

        $optionsMock
            ->expects($this->once())
            ->method('getThreads')
            ->willReturn($expectedNumberOfThreads);

        $configMock
            ->expects($this->once())
            ->method('getOptions')
            ->willReturn($optionsMock);

        $factory
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn($configMock);

        $factory
            ->expects($this->exactly($expectedNumberOfThreads))
            ->method('startThumbnailProcess')
            ->willReturn($processMock);


        $application = new Application();
        $application->add(new ThumbnailsCommand($factory));

        $command = $application->find('app:generateThumbnails');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
    }
}
