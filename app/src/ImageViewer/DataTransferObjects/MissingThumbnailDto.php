<?php declare(strict_types=1);

namespace ImageViewer\DataTransferObjects;

class MissingThumbnailDto
{
    private int $size;
    private int $fileId;
    private int $sizeId;
    private string $file;
    private string $name;

    static function from(string $name, int $size, int $sizeId, string $file, int $fileId): self
    {
        // todo: validate

        return new self(
            $name,
            $size,
            $sizeId,
            $file,
            $fileId
        );
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getSizeId(): int
    {
        return $this->sizeId;
    }

    public function getFileId(): int
    {
        return $this->fileId;
    }

    private function __construct(string $name, int $size, int $sizeId, string $file, int $fileId)
    {
        $this->name = $name;
        $this->size = $size;
        $this->sizeId = $sizeId;
        $this->file = $file;
        $this->fileId = $fileId;
    }
}
