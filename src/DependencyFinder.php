<?php

namespace Selective\CdChecker;

use ReflectionClass;
use ReflectionParameter;
use RuntimeException;
use SplFileInfo;

/**
 * Finder.
 */
final class DependencyFinder
{
    /**
     * Process classes.
     *
     * @param SplFileInfo $file The file
     * @param ClassDependency[] $classDependency The current class dependencies
     *
     * @throws RuntimeException
     *
     * @return ClassDependency[] The new class dependencies
     */
    public function processFile(SplFileInfo $file, array $classDependency): array
    {
        $realPath = $file->getRealPath();
        if ($realPath === false) {
            throw new RuntimeException(sprintf('File could not be found: %s', $file->getPath()));
        }

        $content = file_get_contents($realPath);

        if ($content === false) {
            throw new RuntimeException(sprintf('File could not be read: %s', $file->getPath()));
        }

        if (!preg_match('#^namespace\s+(.+?);$#sm', $content, $matches)) {
            return $classDependency;
        }

        $namespace = $matches[1];
        $class = pathinfo($file, PATHINFO_FILENAME);
        $fullClass = sprintf('%s\%s', $namespace, $class);

        if (isset($classDependency[$fullClass])) {
            return $classDependency;
        }

        $dependencies = $this->getClassDependencies($file, $fullClass);
        $classDependency[$dependencies->class] = $dependencies;

        foreach ($dependencies->dependencies->all() as $subDependency) {
            $classDependency = $this->processFile($subDependency->file, $classDependency);
        }

        return $classDependency;
    }

    /**
     * Check if we should create the class.
     *
     * @param SplFileInfo $file The file
     * @param string $class The class name
     *
     * @return ClassDependency The relevant dependencies
     */
    private function getClassDependencies(SplFileInfo $file, string $class): ClassDependency
    {
        $result = new ClassDependency($file, $class);

        $reflectionClass = new ReflectionClass($class);

        if (
            $reflectionClass->isInterface() ||
            $reflectionClass->isAbstract() ||
            $reflectionClass->isTrait() ||
            $reflectionClass->isAnonymous()
        ) {
            return $result;
        }

        $constructor = $reflectionClass->getConstructor();
        if (!$constructor) {
            return $result;
        }

        $parameters = $constructor->getParameters();

        /** @var ReflectionParameter $param */
        foreach ($parameters as $param) {
            $type = $param->getType();

            if (!$type) {
                continue;
            }

            if ($type->isBuiltin() || $param->isArray() || $param->isVariadic()) {
                continue;
            }

            $reflectionClass = $param->getClass();
            if ($reflectionClass === null) {
                continue;
            }
            $className = (string)$reflectionClass->getName();
            $name = $param->getName();

            $reflector = new ReflectionClass($className);
            $classFile = $reflector->getFileName();
            if ($classFile === false) {
                // It's a PHP internal class like stdClass
                continue;
            }

            $splFile = new SplFileInfo($classFile);

            $result->dependencies->add(new Dependency($splFile, $className, $name));
        }

        return $result;
    }
}
