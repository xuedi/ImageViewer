<?php declare(strict_types=1);

namespace ImageViewer;

use ImageViewer\DataTransferObjects\EventsDto;
use ImageViewer\DataTransferObjects\LocationsDto;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ImageViewer\Database
 * @uses   \ImageViewer\DataTransferObjects\EventsDto
 * @uses   \ImageViewer\EventDate
 * @uses   \ImageViewer\DataTransferObjects\LocationsDto
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

        $this->subject = new Database($this->pdo);
    }

    public function testCanBuildFactory(): void
    {
        $this->assertInstanceOf(Database::class, $this->subject);
    }

    public function testRunInsert(): void
    {
        $this->pdo->method('prepare')->willReturn($this->statement);

        $expectedLastId = 132;
        $expectedData = ['varA' => 'valA', 'varB' => 'valB'];

        $this->statement
            ->expects($this->once())
            ->method('execute')
            ->with($expectedData);

        $this->pdo
            ->expects($this->once())
            ->method('lastInsertId')
            ->willReturn($expectedLastId);

        $actual = $this->subject->insert('test_table', $expectedData);

        $this->assertEquals(
            $expectedLastId,
            $actual
        );
    }

    public function testRunUpdate(): void
    {
        $expectedId = 12;
        $expectedData = ['varA' => 'valA', 'varB' => 'valB', 'id' => 100];

        $this->pdo
            ->expects($this->once())
            ->method('prepare')
            ->with("UPDATE test_table SET varA = :varA, varB = :varB WHERE id = :id ")
            ->willReturn($this->statement);

        $this->statement
            ->expects($this->once())
            ->method('execute')
            ->with([
                'varA' => 'valA',
                'varB' => 'valB',
                'id' => 12,
            ]);

        $this->subject->update('test_table', $expectedId, $expectedData);
    }

    public function testDeleteLocations(): void
    {
        $expected = 12;

        $this->pdo
            ->expects($this->at(0))
            ->method('prepare')
            ->with("DELETE FROM locations WHERE id > 1; ")
            ->willReturn($this->statement);

        $this->pdo
            ->expects($this->at(1))
            ->method('prepare')
            ->with("ALTER TABLE locations AUTO_INCREMENT = 1;")
            ->willReturn($this->statement);

        $this->statement
            ->expects($this->exactly(2))
            ->method('execute');

        $this->statement
            ->expects($this->once())
            ->method('rowCount')
            ->willReturn($expected);

        $this->assertEquals($expected, $this->subject->deleteLocations());
    }

    public function testDeleteEvents(): void
    {
        $expected = 12;

        $this->pdo
            ->expects($this->at(0))
            ->method('prepare')
            ->with("DELETE FROM events WHERE id > 1; ")
            ->willReturn($this->statement);

        $this->pdo
            ->expects($this->at(1))
            ->method('prepare')
            ->with("ALTER TABLE events AUTO_INCREMENT = 1;")
            ->willReturn($this->statement);

        $this->statement
            ->expects($this->exactly(2))
            ->method('execute');

        $this->statement
            ->expects($this->once())
            ->method('rowCount')
            ->willReturn($expected);

        $this->assertEquals($expected, $this->subject->deleteEvents());
    }

    public function testUpdateTagIds(): void
    {
        $expectedFileId = 12;
        $expectedTagIds = [1,2,3];

        $this->pdo
            ->expects($this->once())
            ->method('exec')
            ->with("DELETE FROM file_tags WHERE file_id = 12; INSERT INTO file_tags (file_id, tag_id) VALUES (12, 1),(12, 2),(12, 3);");

        $this->subject->updateTagIds($expectedFileId, $expectedTagIds);
    }

    public function testCanNotUpdateTagIdsWithEmptyPayload(): void
    {
        $this->pdo->expects($this->never())->method('exec');

        $this->subject->updateTagIds(12, []);
    }

    public function testCanGetImagesHashes(): void
    {
        $this->pdo
            ->method('prepare')
            ->with("SELECT id, fileHash FROM files; ")
            ->willReturn($this->statement);

        $this->statement->expects($this->once())->method('execute');
        $this->statement->expects($this->once())->method('fetchAll')->willReturn([]);
        $this->subject->getImagesHashes();
    }

    public function testCanGetImages(): void
    {
        $this->pdo
            ->method('prepare')
            ->with("SELECT id, fileName FROM files; ")
            ->willReturn($this->statement);

        $this->statement->expects($this->once())->method('execute');
        $this->statement->expects($this->once())->method('fetchAll')->willReturn([]);
        $this->subject->getAllImagesNames();
    }

    public function testCanGetImagesNamesWithStatus(): void
    {
        $this->pdo
            ->method('prepare')
            ->with("SELECT id, fileName FROM files WHERE status_id = 1; ")
            ->willReturn($this->statement);

        $this->statement->expects($this->once())->method('execute');
        $this->statement->expects($this->once())->method('fetchAll')->willReturn([]);
        $this->subject->getImagesNamesWithStatus(1);
    }

    public function testCanGetTags(): void
    {
        $this->pdo
            ->method('prepare')
            ->with("SELECT id, name FROM tags; ")
            ->willReturn($this->statement);

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
        $this->pdo
            ->method('prepare')
            ->with("SELECT id, name FROM events; ")
            ->willReturn($this->statement);

        $this->statement->expects($this->exactly(2))->method('execute');
        $this->statement->expects($this->exactly(2))->method('fetchAll')->willReturn([
            0 => 'resultA',
            1 => 'resultB',
        ]);

        $this->assertEquals([0 => 'resultA', 1 => 'resultB'], $this->subject->getEvents());
        $this->assertEquals(['resultA' => 0, 'resultB' => 1], $this->subject->getEvents(true));
    }

    public function testCanGetCameras(): void
    {
        $this->pdo
            ->method('prepare')
            ->with("SELECT id, ident FROM camera; ")
            ->willReturn($this->statement);

        $this->statement->expects($this->exactly(2))->method('execute');
        $this->statement->expects($this->exactly(2))->method('fetchAll')->willReturn([
            0 => 'resultA',
            1 => 'resultB',
        ]);

        $this->assertEquals([0 => 'resultA', 1 => 'resultB'], $this->subject->getCameras());
        $this->assertEquals(['resultA' => 0, 'resultB' => 1], $this->subject->getCameras(true));
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

        $this->pdo
            ->method('prepare')
            ->with("SELECT id, locationId as location, `date` as eventDate, `name` as eventName FROM events ORDER BY `date`; ")
            ->willReturn($this->statement);

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

        $this->pdo
            ->method('prepare')
            ->with("SELECT id, `name` FROM locations ORDER BY id; ")
            ->willReturn($this->statement);

        $this->statement->expects($this->once())->method('execute');
        $this->statement->expects($this->once())->method('fetchAll')->willReturn([$itemA, $itemB]);

        $actual = $this->subject->getLocationDto();

        $this->assertEquals($expected, $actual);
    }
}
