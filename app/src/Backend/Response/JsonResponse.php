<?php declare(strict_types=1);

namespace Backend\Response;

use JsonSerializable;

class JsonResponse
{
    public function __construct(JsonSerializable $object)
    {
        header('Content-Type: application/json');
        echo json_encode($object);
    }
}
