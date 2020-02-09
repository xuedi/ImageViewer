<?php declare(strict_types=1);

namespace ImageViewer\DataTransferObjects;

use ImageViewer\EventDate;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ImageViewer\DataTransferObjects\EventsDto
 * TODO: use dataProvider to test each attribute one, but with all wrong types
 */
final class EventsDtoTest extends TestCase
{
    public function testCanBeBuild(): void
    {
        $expectedId = 1;
        $expectedClass = EventsDto::class;
        $expectedLocation = 6;
        $expectedEventDate = '2000-10-00';
        $expectedEventName = 'eventName';
        $expectedJsonData = [
            'id' => $expectedId,
            'location' => $expectedLocation,
            'eventDate' => $expectedEventDate,
            'eventName' => $expectedEventName
        ];

        $subject = EventsDto::fromArray($expectedJsonData); // parameter are the same as json return

        $this->assertInstanceOf($expectedClass, $subject);
        $this->assertEquals($expectedId, $subject->getId());
        $this->assertEquals($expectedLocation, $subject->getLocation());
        $this->assertEquals($expectedEventName, $subject->getEventName());
        $this->assertEquals($expectedEventDate, $subject->getEventDate()->asString());
        $this->assertEquals($expectedJsonData, $subject->jsonSerialize());
    }
}
