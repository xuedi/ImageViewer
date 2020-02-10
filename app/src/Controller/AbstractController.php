<?php declare(strict_types=1);

namespace ImageViewer\Controller;

abstract class AbstractController
{
    public function jsonReturn(array $response): void
    {
        header("Access-Control-Allow-Origin: *");
        header('Content-Type: application/json');

        echo json_encode($response);
    }
}
