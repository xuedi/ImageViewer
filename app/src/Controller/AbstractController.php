<?php declare(strict_types=1);

namespace ImageViewer\Controller;

use ImageViewer\OutputWrapper;

abstract class AbstractController
{
    private OutputWrapper $output;

    public function __construct(OutputWrapper $output)
    {
        $this->output = $output;
    }

    public function jsonReturn(array $response): void
    {
        $this->output->addHeader('Access-Control-Allow-Origin: *');
        $this->output->addHeader('Content-Type: application/json');
        $this->output->echo(json_encode($response));
    }
}
