<?php

namespace ImageViewer;

use ImageViewer\Configuration\Configuration;

require __DIR__ . '/../app/vendor/autoload.php';

$uri = trim($_GET['uri'] ?? '', '/');
list($controller, $action) = array_pad(explode('/', $uri), 2, '');

$config = new Configuration(__dir__ . '/../config/local.ini');
$factory = new Factory($config);

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

switch ($controller) {
    case '':
        echo json_encode(['controller' => ['register', 'events', 'locations']], JSON_PRETTY_PRINT);
        break;
    case 'events':
        echo json_encode($factory->getDatabase()->getEventDto(), JSON_PRETTY_PRINT);
        break;
    case 'locations':
        echo json_encode($factory->getDatabase()->getLocationDto(), JSON_PRETTY_PRINT);
        break;
    case 'register':
        echo json_encode('OK', JSON_PRETTY_PRINT);
        break;
}
