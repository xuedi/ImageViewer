<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Commands\ThumbnailsWorkerCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class ThumbnailsWorkerCommandTest extends TestCase
{
    public function testCanBuildFactory(): void
    {
        $factory = $this->createMock(Factory::class);
        $factory->expects($this->once())->method('getThumbnailGenerator');

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
