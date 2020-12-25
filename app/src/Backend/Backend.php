<?php declare(strict_types=1);

namespace Backend;

use Backend\Response\HtmlResponse;
use Backend\Response\JsonResponse;
use Bramus\Router\Router;
use Database\Database;
use Database\Models\Event;
use Database\Models\Location;
use Illuminate\Database\DatabaseManager;

class Backend
{
    private Database $db;
    private DatabaseManager $dm;

    public function __construct()
    {
        $this->db = new Database();
        $this->dm = $this->db->getDm();
    }

    public function process()
    {
        $router = new Router();

        $router->all('/', function() {
            new HtmlResponse(file_get_contents(__DIR__ . '/readme.html'));
        });

        $router->get('/events', function() {
            new JsonResponse(Event::all());
        });

        $router->get('/event/(\d+)', function($id) {
            new JsonResponse(Event::find($id));
        });

        $router->get('/locations', function() {
            new JsonResponse(Location::all());
        });

        $router->get('/location/(\d+)', function($id) {
            new JsonResponse(Location::find($id));
        });

        $router->get('/test/', function() {
            dump($this->dm);
        });

        $router->run();
    }
}
