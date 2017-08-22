<?php

namespace LEVIY\Sniffs\Classes;

/**
 * AbstractFinalDeclarationSniff
 *
 * @author Dennis Coorn <dcoorn@leviy.com>
 * @copyright Copyright (c) 2017 LEVIY <https://leviy.com>
 * @package LEVIY\Sniffs\Classes
 *
 * @see https://ocramius.github.io/blog/when-to-declare-classes-final/
 */
abstract class AbstractFinalDeclarationSniff
{
    /**
     * @param \PHP_CodeSniffer_File $phpCsFile
     * @return string
     */
    protected function getClassName(\PHP_CodeSniffer_File $phpCsFile)
    {
        $fileName = $phpCsFile->getFilename();

        $fileNameParts = explode(DIRECTORY_SEPARATOR, $fileName);
        $sourceDirectoryPosition = array_search('src', array_values($fileNameParts));
        $classNameParts = array_slice($fileNameParts, $sourceDirectoryPosition + 1);

        $className = implode('\\', $classNameParts);
        $className = str_replace('.php', '', $className);

        return $className;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @return string[]
     */
    protected function getMethodNames(\ReflectionClass $reflectionClass)
    {
        $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        $methods = array_filter($methods, [$this, 'filterMethod']);

        return array_map(function(\ReflectionMethod $method) {
            return $method->getName();
        }, $methods);
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @return string[]
     */
    protected function getInterfaceMethodNames(\ReflectionClass $reflectionClass)
    {
        $interfaceMethodNames = [];

        $interfaces = $reflectionClass->getInterfaces();
        foreach ($interfaces as $interface) {
            $methodNames = $this->getMethodNames($interface);

            $interfaceMethodNames = array_merge($interfaceMethodNames, $methodNames);
        }

        return array_unique($interfaceMethodNames);
    }

    /**
     * @param \ReflectionMethod $reflectionMethod
     * @return bool
     */
    protected function filterMethod(\ReflectionMethod $reflectionMethod)
    {
        $name = $reflectionMethod->getName();

        if (substr($name, 0, 2) === '__') {
            return false;
        }

        if ($reflectionMethod->isStatic()) {
            return false;
        }

        return true;
    }
}