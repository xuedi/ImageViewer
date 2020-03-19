<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\Configuration\DatabaseConfig;
use ImageViewer\DataTransferObjects\EventsDto;
use ImageViewer\DataTransferObjects\LocationsDto;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ImageViewer\Database
 */
final class DatabaseTest extends TestCase
{
    /** @var MockObject|PDOStatement */
    private MockObject $statement;

    /** @var MockObject|PDO */
    private MockObject $pdo;

    private Database $subject;

    public function setUp(): void
    {
        $this->statement = $this->createMock(PDOStatement::class);

        $this->pdo = $this->createMock(PDO::class);
        $this->pdo->method('prepare')->willReturn($this->statement);

        $this->subject = new Database($this->pdo);
    }

    public function testCanBuildFactory(): void
    {
        $this->assertInstanceOf(Database::class, $this->subject);
    }

    public function testRunInsert(): void
    {
        $expectedLastId = 132;
        $expected = ['varA' => 'valA', 'varB' => 'valB'];

        $this->statement
            ->expects($this->once())
            ->method('execute')
            ->with($expected);

        $this->pdo
            ->expects($this->once())
            ->method('lastInsertId')
            ->willReturn($expectedLastId);

        $actual = $this->subject->insert('test_table', $expected);

        $this->assertEquals(
            $expectedLastId,
            $actual
        );
    }

    public function testCanGetImages(): void
    {
        $this->statement->expects($this->once())->method('execute');
        $this->statement->expects($this->once())->method('fetchAll')->willReturn([]);
        $this->subject->getImages();
    }

    public function testCanGetLocations(): void
    {
        $this->statement->expects($this->exactly(2))->method('execute');
        $this->statement->expects($this->exactly(2))->method('fetchAll')->willReturn([
            0 => 'resultA',
            1 => 'resultB',
        ]);

        $this->assertEquals([0 => 'resultA', 1 => 'resultB'], $this->subject->getLocations());
        $this->assertEquals(['resultA' => 0, 'resultB' => 1], $this->subject->getLocations(true));
    }

    public function testCanGetTags(): void
    {
        $this->statement->expects($this->exactly(2))->method('execute');
        $this->statement->expects($this->exactly(2))->method('fetchAll')->willReturn([
            0 => 'resultA',
            1 => 'resultB',
        ]);

        $this->assertEquals([0 => 'resultA', 1 => 'resultB'], $this->subject->getTags());
        $this->assertEquals(['resultA' => 0, 'resultB' => 1], $this->subject->getTags(true));
    }

    public function testCanGetEvents(): void
    {
        $this->statement->expects($this->exactly(2))->method('execute');
        $this->statement->expects($this->exactly(2))->method('fetchAll')->willReturn([
            0 => 'resultA',
            1 => 'resultB',
        ]);

        $this->assertEquals([0 => 'resultA', 1 => 'resultB'], $this->subject->getEvents());
        $this->assertEquals(['resultA' => 0, 'resultB' => 1], $this->subject->getEvents(true));
    }

    public function testCanGetEventDto(): void
    {
        $itemA = [
            'id' => 1,
            'location' => 'locationA',
            'eventDate' => '2019-01-01',
            'eventName' => 'eventA',
        ];
        $itemB = [
            'id' => 1,
            'location' => 'locationA',
            'eventDate' => '2019-01-01',
            'eventName' => 'eventA',
        ];
        $expected = [
            EventsDto::fromArray($itemA),
            EventsDto::fromArray($itemB),
        ];

        $this->statement->expects($this->once())->method('execute');
        $this->statement->expects($this->once())->method('fetchAll')->willReturn([$itemA, $itemB]);

        $actual = $this->subject->getEventDto();

        $this->assertEquals($expected, $actual);
    }

    public function testCanGetLocationDto(): void
    {
        $itemA = [
            'id' => 1,
            'name' => 'locationA',
        ];
        $itemB = [
            'id' => 2,
            'name' => 'locationB',
        ];
        $expected = [
            LocationsDto::fromArray($itemA),
            LocationsDto::fromArray($itemB),
        ];

        $this->statement->expects($this->once())->method('execute');
        $this->statement->expects($this->once())->method('fetchAll')->willReturn([$itemA, $itemB]);

        $actual = $this->subject->getLocationDto();

        $this->assertEquals($expected, $actual);
    }
}
