<?php

namespace Selective\CdChecker\Test\TestClass;

class ClassB
{
    /**
     * @var ClassA
     */
    private $a;

    public function __construct(ClassA $a)
    {
        $this->a = $a;
    }
}
