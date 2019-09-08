<?php declare(strict_types=1);

namespace ImageViewer;

class Factory
{
    /** @var string */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getFileScanner(): FileScanner
    {
        return new FileScanner($this->path);
    }

    public function getThumbGenerator(): ThumbGenerator
    {
        return new ThumbGenerator($this->path);
    }
}