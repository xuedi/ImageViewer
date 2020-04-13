<?php declare(strict_types=1);

namespace ImageViewer\DataTransferObjects;

use ImageViewer\EventDate;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ImageViewer\DataTransferObjects\MissingThumbnailDto
 */
final class MissingThumbnailDtoTest extends TestCase
{
    public function testCanBeBuild(): void
    {
        $expectedName = 'name';
        $expectedSize = 200;
        $expectedSizeId = 2;
        $expectedFile = 'someFile.jpg';
        $expectedFileId = 1;

        $subject = MissingThumbnailDto::from(
            $expectedName,
            $expectedSize,
            $expectedSizeId,
            $expectedFile,
            $expectedFileId,
        );

        $this->assertEquals($expectedName, $subject->getName());
        $this->assertEquals($expectedSize, $subject->getSize());
        $this->assertEquals($expectedSizeId, $subject->getSizeId());
        $this->assertEquals($expectedFile, $subject->getFile());
        $this->assertEquals($expectedFileId, $subject->getFileId());
    }
}
