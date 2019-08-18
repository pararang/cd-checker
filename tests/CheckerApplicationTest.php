<?php

namespace Selective\CdChecker\Test;

use PHPUnit\Framework\TestCase;
use Selective\CdChecker\CdCheckerApplication;

/**
 * Class CheckerApplicationTest.
 */
class CheckerApplicationTest extends TestCase
{
    /**
     * Test instance.
     *
     * @return void
     */
    public function testInstance(): void
    {
        $instance = new CdCheckerApplication();
        $this->assertInstanceOf(CdCheckerApplication::class, $instance);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testDefinition(): void
    {
        $instance = new CdCheckerApplication();
        $this->assertNotEmpty($instance->getDefinition());
    }
}
