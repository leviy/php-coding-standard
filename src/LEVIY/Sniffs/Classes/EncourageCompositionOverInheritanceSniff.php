<?php

namespace LEVIY\Sniffs\Classes;

/**
 * @see https://ocramius.github.io/blog/when-to-declare-classes-final/
 */
class EncourageCompositionOverInheritanceSniff extends AbstractFinalDeclarationSniff implements \PHP_CodeSniffer_Sniff
{
    /**
     * @return array
     */
    public function register()
    {
        return [T_CLASS];
    }

    /**
     * @param \PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process(\PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        if ($phpcsFile->findPrevious([T_FINAL, T_ABSTRACT], $stackPtr) !== false) {
            return;
        };

        $className = $this->getClassName($phpcsFile);
        $reflectionClass = new \ReflectionClass($className);

        $methodNames = $this->getMethodNames($reflectionClass);
        $interfaceMethodNames = $this->getInterfaceMethodNames($reflectionClass);

        $methodNamesDiff = array_diff($methodNames, $interfaceMethodNames);

        if (count($methodNamesDiff) > 0) {
            return;
        }

        $warning = 'Declare classes final when they implement an interface and no other public methods are defined';
        $fix = $phpcsFile->addFixableWarning($warning, $stackPtr, 'DeclareFinal');

        if ($fix === true) {
            $phpcsFile->fixer->beginChangeset();

            $phpcsFile->fixer->addContentBefore($stackPtr, 'final ');

            $phpcsFile->fixer->endChangeset();
        }
    }
}
