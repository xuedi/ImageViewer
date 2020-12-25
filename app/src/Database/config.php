<?php
$root = realpath(dirname(__FILE__) . '/../../..');
$data = parse_ini_file($root . '/config/local.ini', true);

return [
    'paths' => [
        'migrations' => [
            'Database\Migrations' => '%%PHINX_CONFIG_DIR%%/Migrations',
        ],
        'seeds' => [
            'Database\\Seeds' => '%%PHINX_CONFIG_DIR%%/Seeds',
        ],
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'default',
        'default' => [
            'adapter' => 'mysql',
            'host' => $data['database']['host'] ?? null,
            'name' => $data['database']['name'] ?? null,
            'user' => $data['database']['user'] ?? null,
            'pass' => $data['database']['pass'] ?? null,
            'port' => $data['database']['port'] ?? null,
            'charset' => 'utf8',
        ],
    ],
    'version_order' => 'creation'
];
