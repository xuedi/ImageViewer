<?php declare(strict_types=1);

namespace ImageViewer\Updater;

use ImageViewer\Camera;
use ImageViewer\CameraSettings;
use ImageViewer\Database;
use RuntimeException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class Metadata
{
    private string $path;
    private Database $database;
    private ProgressBar $progressBar;
    private OutputInterface $output;
    private array $stats;

    public function __construct(
        Database $database,
        OutputInterface $output,
        ProgressBar $progressBar,
        string $path
    )
    {
        $this->database = $database;
        $this->output = $output;
        $this->path = $path;
        $this->progressBar = $progressBar;
        $this->stats = [];
    }


    public function update()
    {
        $fileList = $this->database->getImagesNamesWithStatus(1);
        $tagList = $this->database->getTags();
        $eventList = $this->database->getEvents();
        $cameraList = $this->database->getCameras();

        $this->output->write(PHP_EOL);

        $this->progressBar->setMaxSteps(count($fileList));
        $this->progressBar->setFormat('Updating metadata and tags: [%bar%] %memory:6s%');
        $this->progressBar->start();

        foreach ($fileList as $fileId => $fileName) {

            // handle tags
            $tags = $this->extractTags($fileName);
            $tagList = $this->updateTagList($tagList, $tags);
            $this->saveTags($tagList, $tags, $fileId);

            // handle cameras
            $cameraList = $this->updateCameraList($cameraList, $fileName);

            // extract metadata
            $this->parseFile($fileName, $fileId, $eventList, $cameraList);

            $this->progressBar->advance();
        }
        $this->progressBar->advance();
        $this->progressBar->finish();

        $this->output->write(PHP_EOL);

        $this->output->write(' -> images: ' . count($fileList) . PHP_EOL);
        $this->output->write(' -> cameras: ' . count($cameraList) . PHP_EOL);
        $this->output->write(' -> events: ' . count($eventList) . PHP_EOL);
        $this->output->write(' -> tags: ' . count($tagList) . PHP_EOL);

    }

    private function parseFile(string $fileName, int $fileId, array $events, array $cameraList): void
    {
        $file = $this->path . $fileName;
        $imageExif = $this->extractExifData($file);

        $settings = CameraSettings::fromExifData($imageExif);
        $camera = Camera::fromExifData($imageExif);
        $cameraId = array_search($camera->getIdent(), $cameraList) ?? 1;
        $event = strtolower(explode('/', $fileName)[1]);
        $eventName = trim(substr($event, 10));
        $eventId = $events[$eventName] ?? 1;

        $this->database->update('files', $fileId, [
            'event_id' => $eventId,
            'camera_id' => $cameraId,
            'status_id' => 2,
            'fileSize' => filesize($file),
            'fileType' => $settings->getFileType(),
            'pixel' => $settings->getPixel(),
            'iso' => $settings->getIso(),
            'exposure' => $settings->getExposure(),
            'aperture' => $settings->getAperture(),
            'width' => $settings->getWidth(),
            'height' => $settings->getHeight(),
            'createdAt' => $settings->getCreatedAt()->format('Y-m-d H:i:s'), // on null: use event date
        ]);
    }

    private function saveTags(array $tagList, array $tags, int $fileId)
    {
        $tagIds = [];
        foreach ($tags as $tag) {
            $tagIds[] = $this->findTagId($tag, $tagList);
        }

        $this->database->updateTagIds($fileId, $tagIds);
    }

    private function findTagId($tag, array $tagList): int
    {
        $tagId = array_search($tag, $tagList);
        if ($tagId === false) {
            throw new RuntimeException('Expected tag not found!');
        }

        return $tagId;
    }

    private function updateTagList(array $tagList, array $tags): array
    {
        foreach ($tags as $tag) {
            if (!in_array($tag, $tagList)) {
                $tagId = $this->database->insert('tags', ['name' => $tag]);
                $tagList[$tagId] = $tag;
            }
        }

        return $tagList;
    }

    private function extractTags(string $fileName): array
    {
        $tags = [];
        $file = $this->path . $fileName;
        getimagesize($file, $info);
        if (is_array($info) && isset($info["APP13"])) {
            $iptc = iptcparse($info["APP13"]);
            if (isset($iptc['2#025']) && is_array($iptc['2#025'])) {
                foreach ($iptc['2#025'] as $tag) {
                    $tag = strtolower(trim($tag));
                    if(!empty($tag)) {
                        $tags[] = $tag;
                    }
                }
            }
        }

        return $tags;
    }

    private function updateCameraList(array $cameraIdentList, string $fileName): array
    {
        $file = $this->path . $fileName;
        $imageExif = $this->extractExifData($file);

        $camera = Camera::fromExifData($imageExif);

        $cameraId = array_search($camera->getIdent(), $cameraIdentList);
        if ($cameraId == false) {
            $cameraId = $this->database->insert('camera', [
                'ident' => $camera->getIdent(),
                'model' => $camera->getModel(),
                'manufacturer' => $camera->getManufacturer(),
            ]);
            $cameraIdentList[$cameraId] = $camera->getIdent();
        }

        return $cameraIdentList;
    }

    private function extractExifData(string $file): array
    {
        $imageExif = @exif_read_data($file);
        if ($imageExif === false) {
            $imageExif = [];
        }

        return $imageExif;
    }
}
