<?php

namespace ImageViewer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \ImageViewer\Camera
 */
final class CameraTest extends TestCase
{
    public function testCanBuildCamera(): void
    {
        $expectedMaker = 'cannon';
        $expectedModel = 'd5';
        $expectedIdent = md5(trim($expectedMaker) . '-' . trim($expectedModel));

        $subject = Camera::fromExifData([
            'Model' => strtoupper($expectedModel),
            'Make' => strtoupper($expectedMaker),
        ]);

        $this->assertEquals($expectedIdent, $subject->getIdent());
        $this->assertEquals($expectedModel, $subject->getModel());
        $this->assertEquals($expectedMaker, $subject->getManufacturer());
    }

    public function testCanBuildCameraWithUnknownData(): void
    {
        $expectedMaker = 'unknown';
        $expectedModel = 'unknown';
        $expectedIdent = md5(trim($expectedMaker) . '-' . trim($expectedModel));

        $subject = Camera::fromExifData([]);

        $this->assertEquals($expectedIdent, $subject->getIdent());
        $this->assertEquals($expectedModel, $subject->getModel());
        $this->assertEquals($expectedMaker, $subject->getManufacturer());
    }

    public function testCanBuildCameraWithEmptryData(): void
    {
        $expectedMaker = 'unknown';
        $expectedModel = 'unknown';
        $expectedIdent = md5(trim($expectedMaker) . '-' . trim($expectedModel));

        $subject = Camera::fromExifData([
            'Model' => '',
            'Make' => '',
        ]);

        $this->assertEquals($expectedIdent, $subject->getIdent());
        $this->assertEquals($expectedModel, $subject->getModel());
        $this->assertEquals($expectedMaker, $subject->getManufacturer());
    }
}
