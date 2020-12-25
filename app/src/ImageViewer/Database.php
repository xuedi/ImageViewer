<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\DataTransferObjects\EventsDto;
use ImageViewer\DataTransferObjects\LocationsDto;
use ImageViewer\DataTransferObjects\MissingThumbnailDto;
use PDO;

/**
 * Cleanup or use an ORM
 */
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
        $statement = $this->pdo->prepare(
            "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholder) . ")");
        $statement->execute($data);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(string $table, int $id, array $data): void
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $placeholder = [];
        foreach ($data as $key => $value) {
            $placeholder[] = "$key = :$key";
        }
        $data['id'] = $id;

        $statement = $this->pdo->prepare("UPDATE $table SET " . implode(', ', $placeholder) . " WHERE id = :id ");
        $statement->execute($data);
    }

    public function getImagesNamesWithStatus(int $statusId): array
    {
        $statement = $this->pdo->prepare("SELECT id, fileName FROM files WHERE status_id = {$statusId}; ");
        $statement->bindParam('statusId', $statusId);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function getAllImagesNames(): array
    {
        $statement = $this->pdo->prepare("SELECT id, fileName FROM files; ");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function deleteLocations(): int
    {
        $statement = $this->pdo->prepare("DELETE FROM locations WHERE id > 1; ");
        $statement->execute();
        $rows = $statement->rowCount();

        $statement = $this->pdo->prepare("ALTER TABLE locations AUTO_INCREMENT = 1;");
        $statement->execute();

        return $rows;
    }

    public function deleteEvents(): int
    {
        $statement = $this->pdo->prepare("DELETE FROM events WHERE id > 1; ");
        $statement->execute();
        $rows = $statement->rowCount();

        $statement = $this->pdo->prepare("ALTER TABLE events AUTO_INCREMENT = 1;");
        $statement->execute();

        return $rows;
    }

    public function updateTagIds(int $fileId, array $tagIds): void
    {
        if (count($tagIds) == 0) {
            return;
        }
        $valuePairs = [];
        foreach ($tagIds as $tagId) {
            $valuePairs[] = "($fileId, $tagId)";
        }
        $values = implode(',',$valuePairs);
        $sql = "DELETE FROM file_tags WHERE file_id = {$fileId}; INSERT INTO file_tags (file_id, tag_id) VALUES {$values};";

        $this->pdo->exec($sql);
    }

    /**
     * TODO: via native query and save all that loop BS
     * TODO: distribute the size of the files evenly hashSort?
     * @codeCoverageIgnore
     */
    public function getMissingThumbnails(): array
    {
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
        /** @var int $sizeKey */
        foreach ($size as $sizeKey => $sizeValue) {
            /** @var int $fileKey */
            foreach ($files as $fileKey => $fileValue) {
                $noEntry = true;
                foreach ($thumbs as $thumb) {
                    if ($thumb['file_id'] == $fileKey && $thumb['size_id'] == $sizeKey) {
                        $noEntry = false;
                        break;
                    }
                }
                if ($noEntry) {
                    $name = $hash[$fileKey] . '_' . $sizeValue;
                    $missingThumbnails[] = MissingThumbnailDto::from(
                        $name,
                        $sizeValue,
                        $sizeKey,
                        $fileValue,
                        $fileKey,
                        );
                }
            }
        }

        return $missingThumbnails;
    }

    public function getImagesHashes(): array
    {
        $statement = $this->pdo->prepare("SELECT id, fileHash FROM files; ");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function getFiles(): array
    {
        $statement = $this->pdo->prepare("SELECT id, event_id, fileHash FROM files; ");
        $statement->execute();

        return $statement->fetchAll();
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

    public function getCameras(bool $reverse = false): array
    {
        $statement = $this->pdo->prepare("SELECT id, ident FROM camera; ");
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

    public function getEventNames(): array
    {
        $statement = $this->pdo->prepare("SELECT e.id, e.locationId, CONCAT(e.`date`, ' ', e.name ) as name FROM events e");
        $statement->execute();

        return $statement->fetchAll();
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
