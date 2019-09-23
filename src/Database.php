<?php declare(strict_types=1);

namespace ImageViewer;

use Exception;
use ImageViewer\Configuration\DatabaseConfig;
use PDO;

class Database
{
    /** @var PDO */
    private $pdo;

    public function __construct(DatabaseConfig $config)
    {
        $options = [
            PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
        ];
        $this->pdo = new PDO($config->getDsn(), $config->getUser(), $config->getPass(), $options);
    }

    public function getImages(): array
    {
        //TODO: get imageList from Database as (id: uuid => file: relativeFileName)
        return [];
    }

}