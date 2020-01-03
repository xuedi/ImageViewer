<?php declare(strict_types=1);

namespace ImageViewer\DataTransferObjects;

use PHPUnit\Framework\TestCase;
use RuntimeException;

// TODO: use dataProvider to test each attribute one, but with all wrong types
final class LocationsDtoTest extends TestCase
{
    public function testCanBeBuild(): void
    {
        $expectedId = 1;
        $expectedName = 'test';
        $expectedClass = LocationsDto::class;

        $subject = LocationsDto::fromArray(['id' => $expectedId, 'name' => $expectedName]);

        $this->assertInstanceOf($expectedClass, $subject);
        $this->assertEquals($expectedId, $subject->getId());
        $this->assertEquals($expectedName, $subject->getName());
        $this->assertEquals($expectedName, $subject->getName());
        $this->assertEquals(
            [
                'id' => $expectedId,
                'name' => $expectedName,
            ],
            $subject->jsonSerialize()
        );
    }

    public function testCanNotBeBuildWithoutId(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Missing argument 'id'");

        LocationsDto::fromArray(['name' => 'test']);
    }

    public function testCanNotBeBuildWithWrongTypeOfId(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Field 'id' is not of type integer");

        LocationsDto::fromArray(['name' => 'test', 'id' => 'TEXT']);
    }

    public function testCanNotBeBuildWithoutName(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Missing argument 'name'");

        LocationsDto::fromArray(['id' => 1]);
    }

    public function testCanNotBeBuildWithWrongTypeOfName(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Field 'name' is not of type string");

        LocationsDto::fromArray(['id' => 1, 'name' => 11]);
    }
}
