<?php

namespace Selective\CdChecker;

use SplFileInfo;

/**
 * Data.
 */
final class Dependency
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
     * @var string
     */
    public $name;

    /**
     * The constructor.
     *
     * @param string $class The class
     * @param string $name The name
     * @param SplFileInfo $file The file
     */
    public function __construct(SplFileInfo $file, string $class, string $name)
    {
        $this->file = $file;
        $this->class = $class;
        $this->name = $name;
    }
}
