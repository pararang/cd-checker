<?php

namespace Selective\CdChecker\Test\TestClass;

use stdClass;

class ClassOk
{
    /**
     * @var stdClass
     */
    private $a;

    public function __construct(stdClass $a)
    {
        $this->a = $a;
    }
}
