<?php declare(strict_types=1);

namespace ImageViewer;

use PHPUnit\Framework\TestCase;
use RuntimeException;

final class EventDateTest extends TestCase
{
    /**
     * @dataProvider validDates
     */
    public function testCanBuildEventDate(string $expected): void
    {
        $subject = EventDate::fromString($expected);
        $actual = $subject->asString();
        $this->assertInstanceOf(EventDate::class, $subject);
        $this->assertEquals($expected, $actual);
    }

    public function testExceptionOnMismatchingDashes(): void
    {
        $badDate = '2019-03';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Cound not create EventDate from '{$badDate}'");

        EventDate::fromString($badDate);
    }

    /**
     * @dataProvider wrongFormattedYears
     */
    public function testExceptionOnMismatchingYear(string $badDate): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Cound not create EventDate from '{$badDate}' invalid year");

        EventDate::fromString($badDate);
    }

    /**
     * @dataProvider wrongFormattedMonths
     */
    public function testExceptionOnMismatchingMonth(string $badDate): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Cound not create EventDate from '{$badDate}' invalid month");

        EventDate::fromString($badDate);
    }

    /**
     * @dataProvider wrongFormattedDays
     */
    public function testExceptionOnMismatchingDay(string $badDate): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Cound not create EventDate from '{$badDate}' invalid day");

        EventDate::fromString($badDate);
    }
    /**
     * @dataProvider wrongOrder
     */
    public function testWrongOrderOfUndefinedChunks(string $badDate): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Cound not create EventDate from '{$badDate}' wrong order");

        EventDate::fromString($badDate);
    }

    public function validDates()
    {
        return [
            'regular' => ['2019-03-22'],
            'no year' => ['0000-00-00'],
            'no month' => ['2019-00-00'],
            'no day' => ['2019-03-00'],
        ];
    }

    public function wrongFormattedYears()
    {
        return [
            'One digit to few' => ['200-01-01'],
            'No numeric' => ['text-01-01'],
            'No numeric but roman ^^' => ['XVII-01-01'],
            'One digit to many' => ['20000-01-01'],
        ];
    }

    public function wrongFormattedMonths()
    {
        return [
            'One digit to few' => ['2000-0-01'],
            'No numeric' => ['2000-test-01'],
            'No numeric but roman ^^' => ['2000-XVII-01'],
            'One digit to many' => ['2000-100-01'],
        ];
    }

    public function wrongFormattedDays()
    {
        return [
            'One digit to few' => ['2000-01-0'],
            'No numeric' => ['2000-01-test'],
            'No numeric but roman ^^' => ['2000-01-XVII'],
            'One digit to many' => ['2000-01-257'],
        ];
    }

    public function wrongOrder()
    {
        return [
            'no year, but month & day' => ['0000-01-01'],
            'no year, but day' => ['0000-00-22'],
            'no month but day' => ['2019-00-22'],
            'no month and year but day' => ['0000-00-22'],
        ];
    }
}
