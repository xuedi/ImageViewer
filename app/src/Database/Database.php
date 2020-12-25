<?php declare(strict_types=1);

namespace Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\DatabaseManager;

class Database
{
    private DatabaseManager $dm;

    public function __construct()
    {
        $config = require_once "config.php";
        $settings = $config['environments']['default'];

        $capsule = new Capsule();

        $capsule->addConnection([
            'driver'    => $settings['adapter'],
            'host'      => $settings['host'],
            'database'  => $settings['name'],
            'username'  => $settings['user'],
            'password'  => $settings['pass'],
            'charset'   => $settings['charset'],
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $capsule->bootEloquent(); // activate for model based loading

        $this->dm = $capsule->getDatabaseManager();
        //dump($this->dm->select('select * from events'));
    }

    public function getDm(): DatabaseManager
    {
        return $this->dm;
    }
}
