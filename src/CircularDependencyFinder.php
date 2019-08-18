<?php

namespace Selective\CdChecker;

/**
 * Finder.
 */
final class CircularDependencyFinder
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var array
     */
    protected $warnings = [];

    /**
     * Get Errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get Warnings.
     *
     * @return array
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * Process classes.
     *
     * @param ClassDependency[] $classDependencies
     *
     * @return void
     */
    public function processDependencies(array $classDependencies): void
    {
        foreach ($classDependencies as $classDependency) {
            $this->findCircularDependencies($classDependencies, $classDependency);
        }
    }

    private function findCircularDependencies(array $classDependencies, ClassDependency $classDependency): void
    {
        foreach ($classDependency->dependencies->all() as $dependency) {
            if ($this->hasDependency($classDependencies, $dependency, $classDependency->class)) {
                $message = 'Circular reference detected. ';
                $message .= sprintf('Class %s depends on class: %s ', $classDependency->class, $dependency->class);
                $message .= sprintf('and Class %s depends on class: %s', $dependency->class, $classDependency->class);

                $this->errors[] = $message;
            }
        }
    }

    /**
     * @param ClassDependency[] $classDependencies
     * @param Dependency $dependency
     * @param string $class
     *
     * @return bool
     */
    private function hasDependency(array $classDependencies, Dependency $dependency, string $class): bool
    {
        if (!isset($classDependencies[$dependency->class])) {
            $this->warnings[] = sprintf('No dependencies defined in class %s', $dependency->class);

            return false;
        }

        /** @var ClassDependency $classDependency */
        $classDependency = $classDependencies[$dependency->class];

        foreach ($classDependency->dependencies->all() as $subDependency) {
            if ($subDependency->class === $class) {
                return true;
            }
        }

        return false;
    }
}
