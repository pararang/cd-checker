<?php

namespace Selective\CdChecker;

use SplFileInfo;

/**
 * Data.
 */
final class ClassDependency
{
    /**
     * @var SplFileInfo
     */
    public $file;

    /**
     * @var string
     */
    public $class;

    /**
     * @var DependencyList
     */
    public $dependencies;

    /**
     * The constructor.
     *
     * @param SplFileInfo $file
     * @param string $class
     */
    public function __construct(SplFileInfo $file, string $class)
    {
        $this->file = $file;
        $this->class = $class;
        $this->dependencies = new DependencyList();
    }
}
