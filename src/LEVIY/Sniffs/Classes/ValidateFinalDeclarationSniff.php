<?php

namespace LEVIY\Sniffs\Classes;

/**
 * @see https://ocramius.github.io/blog/when-to-declare-classes-final/
 */
class ValidateFinalDeclarationSniff extends AbstractFinalDeclarationSniff implements \PHP_CodeSniffer_Sniff
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

        $warning = 'Only declare classes final if they implement an interface and no other public methods are defined';
        $fix = $phpcsFile->addFixableWarning($warning, $stackPtr, 'RemoveFinal');

        if ($fix === true) {
            $phpcsFile->fixer->beginChangeset();

            $classPosition = $phpcsFile->findNext(T_CLASS, $stackPtr);
            for ($x = $stackPtr; $x < $classPosition; $x++) {
                $phpcsFile->fixer->replaceToken($x, '');
            }

            $phpcsFile->fixer->endChangeset();
        }
    }
}
