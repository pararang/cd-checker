<?php

namespace Selective\CdChecker\Test\TestClass;

class ClassA
{
    /**
     * @var ClassB
     */
    private $b;

    public function __construct(ClassB $b)
    {
        $this->b = $b;
    }
}
