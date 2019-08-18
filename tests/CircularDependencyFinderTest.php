<?php

namespace Selective\CdChecker\Test;

use PHPUnit\Framework\TestCase;
use Selective\CdChecker\CircularDependencyFinder;
use Selective\CdChecker\DependencyFinder;
use SplFileInfo;

/**
 * Class CheckerCommandTest.
 */
class CircularDependencyFinderTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testProcessFile(): void
    {
        $dependencies = [];

        $cdFinder = new DependencyFinder();

        $file = new SplFileInfo(__DIR__ . '/TestClass/ClassA.php');
        $dependencies = $cdFinder->processFile($file, $dependencies);

        $file = new SplFileInfo(__DIR__ . '/TestClass/ClassAA.php');
        $dependencies = $cdFinder->processFile($file, $dependencies);

        $file = new SplFileInfo(__DIR__ . '/TestClass/ClassB.php');
        $dependencies = $cdFinder->processFile($file, $dependencies);

        $cdFinder = new CircularDependencyFinder();
        $cdFinder->processDependencies($dependencies);

        $errors = $cdFinder->getErrors();
        $warnings = $cdFinder->getWarnings();

        static::assertCount(3, $errors);
        static::assertCount(0, $warnings);
    }
}
