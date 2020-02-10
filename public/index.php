<?php

namespace ImageViewer;

use Bramus\Router\Router;
use ImageViewer\Configuration\Configuration;

require __DIR__ . '/../app/vendor/autoload.php';


$config = new Configuration(__dir__ . '/../config/local.ini');
$factory = new Factory($config);
$router = new Router();


$router->get('/events', function() use ($factory) {
    echo json_encode($factory->getDatabase()->getEventDto(), JSON_PRETTY_PRINT);
});

$router->get('/locations', function() use ($factory) {
    echo json_encode($factory->getDatabase()->getLocationDto(), JSON_PRETTY_PRINT);
});

$router->post('/register', function() use ($factory) {
    $email = $_GET['email'] ?? '';
    $password = $_GET['password'] ?? '';
    $factory->getRegisterController()->addUser($email, $password);
});

