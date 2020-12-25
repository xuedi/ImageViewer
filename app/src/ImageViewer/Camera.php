<?php declare(strict_types=1);

namespace ImageViewer;

class Camera
{
    private string $model;
    private string $manufacturer;

    public static function fromExifData(array $exifData): Camera
    {
        return new self(
            self::extractModel($exifData),
            self::extractManufacturer($exifData),
        );
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getManufacturer(): string
    {
        return $this->manufacturer;
    }

    public function getIdent(): string
    {
        return md5(trim($this->manufacturer) . '-' . trim($this->model));
    }

    private function __construct(
        string $model,
        string $manufacturer
    ) {
        $this->model = $model;
        $this->manufacturer = $manufacturer;
    }

    private static function extractModel(array $exifData): string
    {
        $model = $exifData['Model'] ?? null;
        if ($model === null) {
            return 'unknown';
        }
        $model = trim($model);
        if ($model == '') {
            return 'unknown';
        }

        return strtolower($model);
    }

    private static function extractManufacturer(array $exifData): string
    {
        $manufacturer = $exifData['Make'] ?? null;
        if ($manufacturer === null) {
            return 'unknown';
        }
        $manufacturer = trim($manufacturer);
        if ($manufacturer == '') {
            return 'unknown';
        }

        return strtolower($manufacturer);
    }
}
