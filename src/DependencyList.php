<?php

namespace Selective\CdChecker;

/**
 * Data.
 */
final class DependencyList
{
    /**
     * @var Dependency[]
     */
    private $dependencies = [];

    /**
     * @param Dependency $dependency    Dependency
     */
    public function add(Dependency $dependency): void
    {
        $this->dependencies[] = $dependency;
    }

    /**
     * @return Dependency[]
     */
    public function all(): array
    {
        return $this->dependencies;
    }
}
