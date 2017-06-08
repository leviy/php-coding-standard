<?php

namespace LEVIY\Sniffs\Classes;

/**
 * FinalDeclarationSniff
 *
 * @author Dennis Coorn <dcoorn@leviy.com>
 * @copyright Copyright (c) 2017 LEVIY <https://leviy.com>
 * @package LEVIY\Sniffs\Classes
 *
 * @see https://ocramius.github.io/blog/when-to-declare-classes-final/
 */
class FinalDeclarationSniff implements \PHP_CodeSniffer_Sniff
{
    /**
     * @return array
     */
    public function register()
    {
        return [T_FINAL];
    }

    /**
     * @param \PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process(\PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $className = $this->getClassName($phpcsFile);
        $reflectionClass = new \ReflectionClass($className);

        $methodNames = $this->getMethodNames($reflectionClass);
        $interfaceMethodNames = $this->getInterfaceMethodNames($reflectionClass);

        $methodNamesDiff = array_diff($methodNames, $interfaceMethodNames);

        if (count($methodNamesDiff) === 0) {
            return;
        }

        $error = 'Only make classes final if they implement an interface and no other public methods are defined';
        $fix = $phpcsFile->addFixableError($error, $stackPtr, 'RemoveFinal');

        if ($fix === true) {
            $phpcsFile->fixer->beginChangeset();

            $classPosition = $phpcsFile->findNext(T_CLASS, $stackPtr);
            for ($x = $stackPtr; $x < $classPosition; $x++) {
                $phpcsFile->fixer->replaceToken($x, '');
            }

            $phpcsFile->fixer->endChangeset();
        }
    }
    /**
     * @param \PHP_CodeSniffer_File $phpCsFile
     * @return string
     */
    private function getClassName(\PHP_CodeSniffer_File $phpCsFile)
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
    private function getMethodNames(\ReflectionClass $reflectionClass)
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
    private function getInterfaceMethodNames(\ReflectionClass $reflectionClass)
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
    private function filterMethod(\ReflectionMethod $reflectionMethod)
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