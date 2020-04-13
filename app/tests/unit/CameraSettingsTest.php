<?php declare(strict_types=1);

namespace ImageViewer;

use DateTime;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \ImageViewer\CameraSettings
 */
final class CameraSettingsTest extends TestCase
{
    public function testCanBuildCamera(): void
    {
        $expectedHeight = 1920;
        $expectedWidth = 1080;
        $expectedPixel = $expectedHeight * $expectedWidth;
        $expectedCreatedAt = new DateTime('2020-04-10 10:20:30');
        $expectedFileType = 'image/Jpeg';
        $expectedIso = 12345;
        $expectedAperture = '2.8';
        $expectedExposure = '1/600';

        $subject = CameraSettings::fromExifData([
            'DateTime' => $expectedCreatedAt->format('Y-m-d H:i:s'),
            'MimeType' => $expectedFileType,
            'ISOSpeedRatings' => $expectedIso,
            'FNumber' => '280/100',
            'ExposureTime' => '20/12000',
            'COMPUTED' => [
                'Height' => $expectedHeight,
                'Width' => $expectedWidth,
            ]
        ]);

        $this->assertEquals($expectedHeight, $subject->getHeight());
        $this->assertEquals($expectedWidth, $subject->getWidth());
        $this->assertEquals($expectedPixel, $subject->getPixel());
        $this->assertEquals($expectedCreatedAt, $subject->getCreatedAt());
        $this->assertEquals($expectedFileType, $subject->getFileType());
        $this->assertEquals($expectedIso, $subject->getIso());
        $this->assertEquals($expectedAperture, $subject->getAperture());
        $this->assertEquals($expectedExposure, $subject->getExposure());
    }

    public function testCanBuildCameraWithBackupData(): void
    {
        $expectedHeight = 1920;
        $expectedWidth = 1080;
        $expectedCreatedAt = new DateTime('1970-01-01 00:00:00');
        $expectedFileType = 'image/Jpeg';
        $expectedIso = null;
        $expectedAperture = null;
        $expectedExposure = null;

        $subject = CameraSettings::fromExifData([
            'DateTime' => 'BROKEN-DATE-FORMAT',
            'MimeType' => $expectedFileType,
            'COMPUTED' => [
                'Height' => $expectedHeight,
                'Width' => $expectedWidth,
            ]
        ]);

        $this->assertEquals($expectedCreatedAt, $subject->getCreatedAt());
        $this->assertEquals($expectedIso, $subject->getIso());
        $this->assertEquals($expectedAperture, $subject->getAperture());
        $this->assertEquals($expectedExposure, $subject->getExposure());
    }

    public function testExceptionOnInvalidMimeType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown MimeType');

        CameraSettings::fromExifData([
            'COMPUTED' => [
                'Height' => 1920,
                'Width' => 1080,
            ]
        ]);
    }

    public function testExceptionOnInvalidNumerator(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Numerator has to be a numeric: 'x'");

        CameraSettings::fromExifData([
            'MimeType' => 'someType',
            'FNumber' => 'x/100',
            'COMPUTED' => [
                'Height' => 1920,
                'Width' => 1080,
            ]
        ]);
    }

    public function testExceptionOnInvalidDenominator(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Denominator has to be a numeric: 'x'");

        CameraSettings::fromExifData([
            'MimeType' => 'someType',
            'FNumber' => '10/x',
            'COMPUTED' => [
                'Height' => 1920,
                'Width' => 1080,
            ]
        ]);
    }

    public function testExceptionOnInvalidHeight(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not extract height');

        CameraSettings::fromExifData([
            'MimeType' => 'someType',
            'COMPUTED' => [
                'Width' => 1080,
            ]
        ]);
    }

    public function testExceptionOnInvalidWidth(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not extract width');

        CameraSettings::fromExifData([
            'MimeType' => 'someType',
            'COMPUTED' => [
                'Height' => 1920,
            ]
        ]);
    }
}
