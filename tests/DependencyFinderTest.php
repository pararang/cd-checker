<?php

namespace Selective\CdChecker\Test;

use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Selective\CdChecker\DependencyFinder;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

class DependencyFinderTest extends TestCase
{
    /**
     * @var DependencyFinder
     */
    private $dependencyFinder;

    public function setUp()
    {
        $this->dependencyFinder = new DependencyFinder();
    }

    public function testProcessFileWithSplFileInfoFalseExpectRuntimeException()
    {
        $this->expectException(RuntimeException::class);

        /** @var SplFileInfo|MockObject $splFileInfo */
        $splFileInfo = $this->createMock(SplFileInfo::class);
        $splFileInfo->method('getRealPath')
            ->willReturn(false);

        $this->dependencyFinder->processFile($splFileInfo, []);
    }
}
