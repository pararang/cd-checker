<?php

namespace Selective\CdChecker\Test\TestClass;

class ClassAA
{
    /**
     * @var ClassAA
     */
    private $aa;

    public function __construct(ClassAA $aa)
    {
        $this->aa = $aa;
    }
}
