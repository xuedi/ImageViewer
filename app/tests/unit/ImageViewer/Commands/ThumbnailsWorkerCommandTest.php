<?php declare(strict_types=1);

namespace ImageViewer\Commands;

use ImageViewer\Factory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \ImageViewer\Commands\ThumbnailsWorkerCommand
 * @covers \ImageViewer\Commands\FactoryCommand
 */
final class ThumbnailsWorkerCommandTest extends TestCase
{
    public function testCanBuildFactory(): void
    {
        $this->setOutputCallback(function () {

        });

        $factory = $this->createMock(Factory::class);
        $factory->expects($this->once())->method('getThumbnailManager');

        $application = new Application();
        $application->add(new ThumbnailsWorkerCommand($factory));

        $command = $application->find('app:generateThumbnails:worker');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'thread' => '2',
        ]);
    }
}
