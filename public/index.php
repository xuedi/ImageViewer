<?php

namespace ImageViewer;

use ImageViewer\Configuration\Configuration;

require __DIR__ . '/../vendor/autoload.php';

$config  = new Configuration(__dir__ . '/../config/local.ini');
$factory = new Factory($config);

header('Content-Type: application/json');

switch ($_GET['action'] ?? '') {
    case '':
        echo json_encode(['action' => ['events', 'locations']], JSON_PRETTY_PRINT);
        break;
    case 'events':
        echo json_encode($factory->getDatabase()->getEventDto(), JSON_PRETTY_PRINT);
        break;
    case 'locations':
        echo json_encode($factory->getDatabase()->getLocationDto(), JSON_PRETTY_PRINT);
        break;
}
