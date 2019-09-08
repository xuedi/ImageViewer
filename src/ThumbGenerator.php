<?php declare(strict_types=1);

namespace ImageViewer;

class ThumbGenerator
{
    /** @var string */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function build(int $size): void
    {
        echo 'generate thumbs(' . $size . '): ';

        $thumbDir = "../cache/{$size}/";
        if(is_dir($thumbDir)) {
            mkdir($thumbDir);
        }

        $storage = '../data/files.json';
        if (!file_exists($storage)) {
            echo 'The files cache could not be found: ' . $storage;
            exit;
        }
        $filesJson = file_get_contents($storage);
        $files = json_decode($filesJson, true);
        foreach ($files as $hash => $file) {
            $this->genThumbnails($file['fileName'], $hash, $size);
            echo '.';
        }
        echo 'OK' . PHP_EOL;
    }

    private function genThumbnails(string $source, string $nameHash, int $size)
    {
        $thumb = "../public/cache/{$size}/{$nameHash}.jpg";
        if (!file_exists($source) || file_exists($thumb)) {
            return;
        }

        // read the source image
        $source_image = imagecreatefromjpeg($source);

        // fetch height width
        $width = imagesx($source_image);
        $height = imagesy($source_image);

        // its a square :-P
        $new_w = $size;
        $new_h = $size;

        // calculate ratios
        $w_ratio = ($new_w / $width);
        $h_ratio = ($new_h / $height);

        // calculate positions and chopping points
        if ($width > $height) { //landscape
            $crop_w = (int) round($width * $h_ratio);
            $crop_h = $new_h;
            $src_x = (int) ceil(($width - $height) / 2);
            $src_y = 0;
        } elseif ($width < $height) { //portrait
            $crop_h = (int) round($height * $w_ratio);
            $crop_w = $new_w;
            $src_x = 0;
            $src_y = (int) ceil(($height - $width) / 2);
        } else {//square
            $crop_w = $new_w;
            $crop_h = $new_h;
            $src_x = 0;
            $src_y = 0;
        }

        // create the gd image
        $virtual_image = imagecreatetruecolor($new_w, $new_h);

        // resize & chopp the thumnail
        imagecopyresampled($virtual_image, $source_image, 0, 0, $src_x, $src_y, $crop_w, $crop_h, $width, $height);

        // write the image into a real picture
        imagejpeg($virtual_image, $thumb, 87);
    }
}
