<?php

namespace ImageViewer;

use ImageViewer\Extractors\EventExtractor;
use ImageViewer\Extractors\LocationExtractor;
use ImageViewer\Extractors\MetaExtractor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ExtractorServiceTest extends TestCase
{
    private ExtractorService $subject;

    /** @var MockObject|FileBuilder */
    private $fileBuilderMock;

    /** @var MockObject|MetaExtractor */
    private $metaExtractorMock;

    /** @var MockObject|EventExtractor */
    private $eventExtractorMock;

    /** @var MockObject|LocationExtractor */
    private $locationExtractorMock;

    /** @var MockObject|FileScanner */
    private $fileScannerMock;

    public function setUp(): void
    {
        $this->fileScannerMock = $this->createMock(FileScanner::class);
        $this->locationExtractorMock = $this->createMock(LocationExtractor::class);
        $this->eventExtractorMock = $this->createMock(EventExtractor::class);
        $this->metaExtractorMock = $this->createMock(MetaExtractor::class);
        $this->fileBuilderMock = $this->createMock(FileBuilder::class);
        $this->subject = new ExtractorService(
            $this->fileScannerMock,
            $this->locationExtractorMock,
            $this->eventExtractorMock,
            $this->metaExtractorMock,
            $this->fileBuilderMock
        );
    }

    public function testCanBuild(): void
    {
        $this->assertInstanceOf(ExtractorService::class, $this->subject);
    }

    public function testExtractAndBuild(): void
    {
        $this->fileScannerMock->expects($this->once())->method('scan');
        $this->locationExtractorMock->expects($this->once())->method('parse');
        $this->eventExtractorMock->expects($this->once())->method('parse');
        $this->metaExtractorMock->expects($this->once())->method('parse');
        $this->fileBuilderMock->expects($this->once())->method('build');

        $this->subject->scan();
    }
}
