<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\DataTransferObjects\EventsDto;
use ImageViewer\DataTransferObjects\LocationsDto;
use PDO;

class Database
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function insert(string $table, array $data): int
    {
        $columns = [];
        $placeholder = [];
        foreach ($data as $key => $value) {
            $columns[] = $key;
            $placeholder[] = ":$key";
        }
        $statement = $this->pdo->prepare("INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ',
                $placeholder) . ")");
        $statement->execute($data);

        return (int)$this->pdo->lastInsertId();
    }

    public function getImages(): array
    {
        $statement = $this->pdo->prepare("SELECT nameHash FROM files; ");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getMissingThumbnails(): array
    {
        // TODO: via native query and save all that loop BS

        $sizeQuery = $this->pdo->prepare("SELECT id, size FROM thumb_size; ");
        $sizeQuery->execute();
        $size = $sizeQuery->fetchAll(PDO::FETCH_KEY_PAIR);

        $filesQuery = $this->pdo->prepare("SELECT id, fileName FROM files; ");
        $filesQuery->execute();
        $files = $filesQuery->fetchAll(PDO::FETCH_KEY_PAIR);

        $hashQuery = $this->pdo->prepare("SELECT id, fileHash FROM files; ");
        $hashQuery->execute();
        $hash = $hashQuery->fetchAll(PDO::FETCH_KEY_PAIR);

        $thumbsQuery = $this->pdo->prepare("SELECT file_id, size_id FROM thumbs; ");
        $thumbsQuery->execute();
        $thumbs = $thumbsQuery->fetchAll(PDO::FETCH_ASSOC);

        $missingThumbnails = [];
        foreach ($size as $sizeKey => $sizeValue) {
            foreach ($files as $fileKey => $fileValue) {
                $noEntry = true;
                foreach ($thumbs as $thumb) {
                    if($thumb['file_id'] == $fileKey && $thumb['size_id'] == $sizeKey) {
                        $noEntry = false;
                        break;
                    }
                }
                if($noEntry) {
                    $missingThumbnails[] = [
                        'name'=> $hash[$fileKey] . '_' . $sizeValue,
                        'size'=> $sizeValue,
                        'size_id'=> $sizeKey,
                        'file'=> $fileValue,
                        'file_id'=> $fileKey,
                    ];
                }
            }
        }

        return $missingThumbnails;
    }

    public function getLocations(bool $reverse = false): array
    {
        $statement = $this->pdo->prepare("SELECT id, name FROM locations; ");
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_KEY_PAIR);
        if ($reverse) {
            $result = array_flip($result);
        }

        return $result;
    }

    public function getTags(bool $reverse = false): array
    {
        $statement = $this->pdo->prepare("SELECT id, name FROM tags; ");
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_KEY_PAIR);
        if ($reverse) {
            $result = array_flip($result);
        }

        return $result;
    }

    public function getEvents(bool $reverse = false): array
    {
        $statement = $this->pdo->prepare("SELECT id, name FROM events; ");
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_KEY_PAIR);
        if ($reverse) {
            $result = array_flip($result);
        }

        return $result;
    }

    public function getEventDto(): array
    {
        $list = [];

        $statement = $this->pdo->prepare("SELECT id, locationId as location, `date` as eventDate, `name` as eventName FROM events ORDER BY `date`; ");
        $statement->execute();

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $item) {
            $list[] = EventsDto::fromArray($item);
        }

        return $list;
    }

    public function getLocationDto()
    {
        $list = [];

        $statement = $this->pdo->prepare("SELECT id, `name` FROM locations ORDER BY id; ");
        $statement->execute();

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $item) {
            $list[] = LocationsDto::fromArray($item);
        }

        return $list;
    }
}