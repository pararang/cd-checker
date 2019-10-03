<?php

namespace Selective\CdChecker\Test;

use Selective\CdChecker\TypeCast;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class TypeCastTest extends TestCase
{

    public function testCastIntWithObjectExpectInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        $testObject = json_decode("{}");
        TypeCast::castInt($testObject);
    }

    public function testCastIntWithStringExpectIntValue()
    {
        $this->assertEquals(
            23,
            TypeCast::castInt("23")
        );
    }

    public function testCastIntWithDoubleExpectIntValue()
    {
        $this->assertEquals(
            23,
            TypeCast::castInt(23.0)
        );
    }

    public function testCastStringExpectInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        $testObject = json_decode("{}");
        TypeCast::castString($testObject);
    }

    public function testCastStringWithIntExpectStringValue()
    {
        $this->assertEquals(
            "23",
            TypeCast::castInt(23)
        );
    }

    public function testCastStringWithDoubleExpectStringValue()
    {
        $this->assertEquals(
            "23",
            TypeCast::castString(23.0)
        );
    }
}
