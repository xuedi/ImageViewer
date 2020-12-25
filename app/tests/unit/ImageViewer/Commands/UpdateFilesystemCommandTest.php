<?php declare(strict_types=1);

namespace ImageViewer\Commands;

use ImageViewer\Factory;
use ImageViewer\Updater\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \ImageViewer\Commands\UpdateFilesystemCommand
 * @covers \ImageViewer\Commands\FactoryCommand
 */
final class UpdateFilesystemCommandTest extends TestCase
{
    public function testCanBuildFactory(): void
    {
        $filesystemMock = $this->createMock(Filesystem::class);
        $filesystemMock->expects($this->once())->method('update');

        $factoryMock = $this->createMock(Factory::class);
        $factoryMock->expects($this->once())->method('getUpdaterFilesystem')->willReturn($filesystemMock);

        $application = new Application();
        $application->add(new UpdateFilesystemCommand($factoryMock));

        $command = $application->find('app:updateFilesystem');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
    }
}
