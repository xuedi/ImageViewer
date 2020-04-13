<?php declare(strict_types=1);

namespace ImageViewer\Commands;

use ImageViewer\Factory;
use ImageViewer\Updater\Metadata;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \ImageViewer\Commands\UpdateMetadataCommand
 * @covers \ImageViewer\Commands\FactoryCommand
 */
final class UpdateMetadataCommandTest extends TestCase
{
    public function testCanBuildFactory(): void
    {
        $metadataMock = $this->createMock(Metadata::class);
        $metadataMock->expects($this->once())->method('update');

        $factoryMock = $this->createMock(Factory::class);
        $factoryMock->expects($this->once())->method('getUpdaterMetadata')->willReturn($metadataMock);

        $application = new Application();
        $application->add(new UpdateMetadataCommand($factoryMock));

        $command = $application->find('app:updateMetadata');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
    }
}
