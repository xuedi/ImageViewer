<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Commands\ThumbnailsCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class ThumbnailsCommandTest extends TestCase
{
    public function testCanBuildFactory(): void
    {
        $this->markTestSkipped('Not testable yet');

        $factory = $this->createMock(Factory::class);

        $application = new Application();
        $application->add(new ThumbnailsCommand($factory));

        $command = $application->find('app:generateThumbnails');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
    }
}
