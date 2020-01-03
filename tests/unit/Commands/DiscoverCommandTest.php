<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Commands\DiscoverCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class DiscoverCommandTest extends TestCase
{
    public function testCanBuildFactory(): void
    {
        $factory = $this->createMock(Factory::class);
        $factory->expects($this->once())->method('getExtractorService');

        $application = new Application();
        $application->add(new DiscoverCommand($factory));

        $command = $application->find('app:discover');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
    }
}
