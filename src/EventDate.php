<?php declare(strict_types=1);

namespace ImageViewer;

use RuntimeException;

class EventDate
{
    /** @var int|null */
    private $year;

    /** @var int|null */
    private $month;

    /** @var int|null */
    private $day;

    public static function fromString(string $data)
    {
        return new self($data);
    }

    public function asString(): string
    {
        return $this->year . '-' . $this->month . '-' . $this->day;
    }

    private function __construct(string $data)
    {
        list($this->year, $this->month, $this->day) = $this->process($data);
    }

    private function process(string $data)
    {
        $chunks = explode('-', $data);
        if(empty($chunks) || !is_array($chunks) || count($chunks) != 3) {
            throw new RuntimeException("Cound not create EventDate from '{$data}'");
        }

        $year = $chunks[0];
        if(!is_numeric($year) || strlen($year) != 4) {
            throw new RuntimeException("Cound not create EventDate from '{$data}' invalid year");
        }

        $month = $chunks[1];
        if(!is_numeric($month) || strlen($month) != 2) {
            throw new RuntimeException("Cound not create EventDate from '{$data}' invalid month");
        }

        $day = $chunks[1];
        if(!is_numeric($day) || strlen($day) != 2) {
            throw new RuntimeException("Cound not create EventDate from '{$data}' invalid day");
        }

        return [
            $year,
            $month,
            $day,
        ];
    }


}