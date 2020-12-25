<?php declare(strict_types=1);

namespace ImageViewer\Controller;

use ImageViewer\Database;
use ImageViewer\OutputWrapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ImageViewer\Controller\RegisterController
 * @covers \ImageViewer\Controller\AbstractController
 */
class RegisterControllerTest extends TestCase
{
    /** @var MockObject|Database */
    private $databaseMock;

    /** @var MockObject|OutputWrapper */
    private $outputMock;

    private RegisterController $subject;

    public function setUp(): void
    {
        $this->databaseMock = $this->createMock(Database::class);
        $this->outputMock = $this->createMock(OutputWrapper::class);

        $this->subject = new RegisterController($this->outputMock, $this->databaseMock);
    }

    public function testCanAddUser(): void
    {
        $expectedOutput = '{"status":"OK","email":"user","password":"pass"}';

        $this->outputMock
            ->expects($this->at(0))
            ->method('addHeader')
            ->with('Access-Control-Allow-Origin: *');

        $this->outputMock
            ->expects($this->at(1))
            ->method('addHeader')
            ->with('Content-Type: application/json');

        $this->outputMock
            ->expects($this->once())
            ->method('echo')
            ->with($expectedOutput);

        $this->subject->addUser('user', 'pass');
    }
}
