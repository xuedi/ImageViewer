<?php declare(strict_types=1);

namespace ImageViewer;

use Exception;
use Imagick;
use RuntimeException;

class ThumbnailGenerator
{
    public function create(string $file, int $size, string $thumbnail, int $compression = 90): void
    {
        if (!file_exists($file)) {
            throw new RuntimeException("Could not find file '$file'");
        }
        if (file_exists($thumbnail)) {
            throw new RuntimeException("Thumbnail already exist '$thumbnail'");
        }

        try {
            $imagick = new Imagick();
            $imagick->readImage($file);
            $imagick->setImageCompressionQuality($compression);
            $imagick->thumbnailImage($size, $size);
            $imagick->writeImage($thumbnail);
        } catch (Exception $e) {
            throw new RuntimeException("Could not create thumbnail '$thumbnail' with error: " . $e->getMessage());
        }

        if (!file_exists($thumbnail)) {
            throw new RuntimeException("Could not create thumbnail '$thumbnail'");
        }
    }
}
