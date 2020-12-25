<?php declare(strict_types=1);

namespace ImageViewer\DataTransferObjects;

use ImageViewer\EventDate;
use JsonSerializable;

class Events implements JsonSerializable
{
    private int       $id;
    private int       $location;
    private string    $eventName;
    private EventDate $eventDate;

    static function fromArray(array $input): self
    {
        // TODO: optional $input validation

        return new self(
            (int)$input['id'],
            (int)$input['location'],
            EventDate::fromString((string)$input['eventDate']),
            (string)$input['eventName']
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLocation(): int
    {
        return $this->location;
    }

    public function getEventDate(): EventDate
    {
        return $this->eventDate;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'location' => $this->location,
            'eventDate' => $this->eventDate->asString(),
            'eventName' => $this->eventName,
        ];
    }

    private function __construct(int $id, int $location, EventDate $eventDate, string $eventName)
    {
        $this->id = $id;
        $this->location = $location;
        $this->eventDate = $eventDate;
        $this->eventName = $eventName;
    }
}
