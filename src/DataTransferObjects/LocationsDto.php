<?php declare(strict_types=1);

namespace ImageViewer\DataTransferObjects;

use ImageViewer\EventDate;
use JsonSerializable;

class LocationsDto implements JsonSerializable
{
    private int          $id;
    private string       $name;

    static function fromArray(array $input) : self
    {
        return new self($input['id'], $input['name']);
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function jsonSerialize()
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
        ];
    }

    private function __construct(int $id, string $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }
}
