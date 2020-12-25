<?php declare(strict_types=1);

namespace Backend\Response;

class HtmlResponse
{
    public function __construct(string $text)
    {
        header('Content-Type: text/html; charset=utf-8');
        echo $text;
    }
}
