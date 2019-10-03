<?php

namespace Selective\CdChecker\Test;

use Selective\CdChecker\TypeCast;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

/**
 * Class TypeCastTest.
 */
class TypeCastTest extends TestCase
{
    /**
     * Test.
     *
     * @return void
     */
    public function testCastIntWithObjectExpectInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        $testObject = json_decode('{}');
        TypeCast::castInt($testObject);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testCastIntWithStringExpectIntValue()
    {
        $this->assertEquals(
            23,
            TypeCast::castInt('23')
        );
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testCastIntWithDoubleExpectIntValue()
    {
        $this->assertEquals(
            23,
            TypeCast::castInt(23.0)
        );
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testCastStringExpectInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        $testObject = json_decode('{}');
        TypeCast::castString($testObject);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testCastStringWithIntExpectStringValue()
    {
        $this->assertEquals(
            '23',
            TypeCast::castInt(23)
        );
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testCastStringWithDoubleExpectStringValue()
    {
        $this->assertEquals(
            '23',
            TypeCast::castString(23.0)
        );
    }
}
