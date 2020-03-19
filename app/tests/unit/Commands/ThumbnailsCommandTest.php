<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Commands\ThumbnailsCommand;
use ImageViewer\Configuration\Configuration;
use ImageViewer\Configuration\OptionsConfig;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class ThumbnailsCommandTest extends TestCase
{
    public function testCanBuildFactory(): void
    {
        $this->setOutputCallback(function () {
        });

        $optionsMock = $this->createMock(OptionsConfig::class);
        $optionsMock->expects($this->once())->method('getThreads')->willReturn(2);

        $configMock = $this->createMock(Configuration::class);
        $configMock->expects($this->once())->method('getOptions')->willReturn($optionsMock);

        $factory = $this->createMock(Factory::class);
        $factory->expects($this->once())->method('getConfig')->willReturn($configMock);

        $application = new Application();
        $application->add(new ThumbnailsCommand($factory));

        $command = $application->find('app:generateThumbnails');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
    }
}
