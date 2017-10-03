<?php

namespace LEVIY\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class DiscourageFitzinatorSniff implements Sniff
{
    /**
     * @return array
     */
    public function register()
    {
        return [T_WHITESPACE];
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
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
