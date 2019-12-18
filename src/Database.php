<?php declare(strict_types=1);

namespace ImageViewer;

use Exception;
use ImageViewer\Configuration\DatabaseConfig;
use PDO;

class Database
{
    private PDO $pdo;

    public function __construct(DatabaseConfig $config)
    {
        $options = [
            PDO::ATTR_EMULATE_PREPARES => false, // turn off emulation mode for "real" prepared statements
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
        ];
        $this->pdo = new PDO($config->getDsn(), $config->getUser(), $config->getPass(), $options);
    }

    public function insert(string $table, array $data)
    {
        $columns = [];
        $placeholder = [];
        foreach ($data as $key => $value) {
            $columns[] = $key;
            $placeholder[] = ":$key";
        }
        $statement = $this->pdo->prepare("INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholder) . ")");
        $statement->execute($data);
    }

    public function getImages(): array
    {
        $statement = $this->pdo->prepare("SELECT nameHash FROM files; ");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getLocations(bool $reverse = false)
    {
        $statement = $this->pdo->prepare("SELECT id, name FROM locations; ");
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_KEY_PAIR);
        if ($reverse) {
            $result = array_flip($result);
        }

        return $result;
    }

    public function getTags(bool $reverse = false)
    {
        $statement = $this->pdo->prepare("SELECT id, name FROM tags; ");
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_KEY_PAIR);
        if ($reverse) {
            $result = array_flip($result);
        }

        return $result;
    }

    public function getEvents()
    {
        $statement = $this->pdo->prepare("SELECT id, name FROM events; ");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_KEY_PAIR);

    }

}