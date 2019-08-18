<?php

namespace Selective\CdChecker\Test\TestClass\Excluded;

class ClassExcluded
{
    /**
     * @var ClassExcluded
     */
    private $a;

    public function __construct(ClassExcluded $classExcluded)
    {
        $this->a = $classExcluded;
    }
}
