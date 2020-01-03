<?php declare(strict_types=1);

namespace ImageViewer\DataTransferObjects;

use DtoTypes;
use JsonSerializable;

class LocationsDto implements JsonSerializable
{
    private int          $id;
    private string       $name;

    use DtoTypes;

    static function fromArray(array $parameter): self
    {
        self::ensureParameter($parameter, ['id', 'name']);
        self::ensureInteger($parameter, 'id');
        self::ensureString($parameter, 'name');

        return new self($parameter['id'], $parameter['name']);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    private function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
