<?php declare(strict_types=1);

namespace ImageViewer;

use RuntimeException;

class EventDate
{
    private ?string $year;
    private ?string $month;
    private ?string $day;

    public static function fromString(string $data)
    {
        return new self($data);
    }

    public function asString(): string
    {
        return implode('-', [
            $this->year ?? '0000',
            $this->month ?? '00',
            $this->day ?? '00'
        ]);
    }

    private function __construct(string $data)
    {
        list($this->year, $this->month, $this->day) = $this->process($data);
    }

    private function process(string $data)
    {
        list($year, $month, $day) = $this->ensureDashes($data);

        $this->ensureYear($data, $year);
        $this->ensureMonth($data, $month);
        $this->ensureDay($data, $day);
        $this->ensureOrder($data, $year, $month, $day);

        return [
            $year,
            $month,
            $day,
        ];
    }

    private function ensureDashes(string $data): array
    {
        $chunks = explode('-', $data);
        if (empty($chunks) || count($chunks) != 3) {
            throw new RuntimeException("Cound not create EventDate from '{$data}'");
        }

        return $chunks;
    }

    private function ensureYear(string $date, string $year = '0000'): void
    {
        if (!is_numeric($year) || strlen($year) != 4) {
            throw new RuntimeException("Cound not create EventDate from '{$date}' invalid year");
        }
    }

    private function ensureMonth(string $date, string $month = '00'): void
    {
        if (!is_numeric($month) || strlen($month) != 2) {
            throw new RuntimeException("Cound not create EventDate from '{$date}' invalid month");
        }
    }

    private function ensureDay(string $date, string $day = '00'): void
    {
        if (!is_numeric($day) || strlen($day) != 2) {
            throw new RuntimeException("Cound not create EventDate from '{$date}' invalid day");
        }
    }

    private function ensureOrder(string $date, string $year, string $month, string $day): void
    {
        if ($year === '0000' && ($month !== '00' || $day !== '00')) {
            throw new RuntimeException("Cound not create EventDate from '{$date}' wrong order");
        }
        if ($month === '00' && $day !== '00') {
            throw new RuntimeException("Cound not create EventDate from '{$date}' wrong order");
        }
    }
}