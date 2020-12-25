<?php declare(strict_types=1);

namespace ImageViewer;

use DateTime;
use Exception;
use RuntimeException;

class CameraSettings
{
    private DateTime $dateTime;
    private string $fileType;
    private int $width;
    private int $height;
    private ?float $aperture;
    private ?string $exposure;
    private ?int $iso;

    public static function fromExifData(array $exifData): CameraSettings
    {
        return new self(
            self::extractDateTime($exifData),
            self::extractFileType($exifData),
            self::extractWidth($exifData),
            self::extractHeight($exifData),
            self::extractAperture($exifData),
            self::extractExposure($exifData),
            self::extractIso($exifData)
        );
    }

    public function getAperture(): ?float
    {
        return $this->aperture;
    }

    public function getExposure(): ?string
    {
        return $this->exposure;
    }

    public function getIso(): ?int
    {
        return $this->iso;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getPixel(): int
    {
        return ($this->width * $this->height);
    }

    public function getFileType(): string
    {
        return $this->fileType;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->dateTime;
    }

    private function __construct(
        DateTime $dateTime,
        string $fileType,
        int $width,
        int $height,
        ?float $aperture,
        ?string $exposure,
        ?int $iso
    )
    {
        $this->dateTime = $dateTime;
        $this->fileType = $fileType;
        $this->height = $height;
        $this->width = $width;
        $this->aperture = $aperture;
        $this->exposure = $exposure;
        $this->iso = $iso;
    }

    private static function extractDateTime(array $exifData): DateTime
    {
        try {
            return new DateTime($exifData['DateTime'] ?? '');
        } catch (Exception $e) {
            return new DateTime('1970-01-01 00:00:00');
        }
    }

    private static function extractFileType(array $exifData): string
    {
        return (string)($exifData['MimeType'] ?? 'unknown');
    }

    private static function extractExposure(array $exifData): ?string
    {
        $exposureTime = $exifData['ExposureTime'] ?? null;
        if ($exposureTime) {
            list($numerator, $denominator) = self::extractFactors((string)$exposureTime);
            if ($numerator == 0) {
                return $exposureTime;
            }
            if ($denominator % $numerator == 0) {
                $denominator = ($denominator / $numerator);
                $numerator = 1;
            }
            $exposureTime = "$numerator/$denominator";
        }

        return $exposureTime;
    }

    private static function extractIso(array $exifData): ?int
    {
        return $exifData['ISOSpeedRatings'] ?? null;
    }

    private static function extractAperture(array $exifData): ?float
    {
        $aperture = $exifData['FNumber'] ?? null;
        if ($aperture) {
            list($numerator, $denominator) = self::extractFactors((string)$aperture);
            $aperture = (float)($numerator / $denominator);
        }

        return $aperture;
    }

    private static function extractWidth(array $exifData): int
    {
        return (int)($exifData['COMPUTED']['Width'] ?? 0);
    }

    private static function extractHeight(array $exifData): int
    {
        return (int)($exifData['COMPUTED']['Height'] ?? 0);
    }

    private static function extractFactors(string $factors): array
    {
        list($numerator, $denominator) = explode('/', $factors);

        if (!is_numeric($numerator)) {
            throw new RuntimeException("Numerator has to be a numeric: '$numerator'");
        }
        if (!is_numeric($denominator)) {
            throw new RuntimeException("Denominator has to be a numeric: '$denominator'");
        }

        return [(int)$numerator, (int)$denominator];
    }
}
