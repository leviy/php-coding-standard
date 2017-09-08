<?php

namespace LEVIY\Sniffs\WhiteSpace;

class DiscourageFitzinatorSniff implements \PHP_CodeSniffer_Sniff
{
    /**
     * @return array
     */
    public function register()
    {
        return [T_WHITESPACE];
    }

    /**
     * @param \PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process(\PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Make sure this is trailing whitespace.
        $line = $tokens[$stackPtr]['line'];
        if (($stackPtr < count($tokens) - 1) && $tokens[($stackPtr + 1)]['line'] === $line) {
            return;
        }

        if (strpos($tokens[$stackPtr]['content'], "\n") > 0 || strpos($tokens[$stackPtr]['content'], "\r") > 0) {
            $error = 'Please trim any trailing whitespace';
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'TrimTrailingWhiteSpace');

            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();

                $phpcsFile->fixer->replaceToken($stackPtr, '');
                $phpcsFile->fixer->addNewline($stackPtr);

                $phpcsFile->fixer->endChangeset();
            }
        }
    }
}
