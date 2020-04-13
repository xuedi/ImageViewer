<?php declare(strict_types=1);

namespace ImageViewer\Commands;

use ImageViewer\Factory;
use ImageViewer\Updater\Structure;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \ImageViewer\Commands\UpdateStructureCommand
 * @covers \ImageViewer\Commands\FactoryCommand
 */
final class UpdateStructureCommandTest extends TestCase
{
    public function testCanBuildFactory(): void
    {
        $structureMock = $this->createMock(Structure::class);
        $structureMock->expects($this->once())->method('update');

        $factoryMock = $this->createMock(Factory::class);
        $factoryMock->expects($this->once())->method('getUpdaterStructure')->willReturn($structureMock);

        $application = new Application();
        $application->add(new UpdateStructureCommand($factoryMock));

        $command = $application->find('app:updateStructure');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
    }
}
