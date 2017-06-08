<?php

namespace LEVIY\Sniffs\WhiteSpace;

/**
 * OpeningTagSniff
 *
 * @author Dennis Coorn <dcoorn@leviy.com>
 * @copyright Copyright (c) 2017 LEVIY <https://leviy.com>
 * @package LEVIY\Sniffs\WhiteSpace
 */
class OpeningTagSniff implements \PHP_CodeSniffer_Sniff
{
    /**
     * @return array
     */
    public function register()
    {
        return [T_OPEN_TAG];
    }

    /**
     * @param \PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process(\PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        for ($i = ($stackPtr + 1); $i < ($phpcsFile->numTokens - 1); $i++) {
            if ($tokens[$i]['line'] === $tokens[$stackPtr]['line']) {
                continue;
            }

            break;
        }

        // The $i var now points to the first token on the line after the PHP opening tag, which must be a blank line.
        $next = $phpcsFile->findNext(T_WHITESPACE, $i, $phpcsFile->numTokens, true);
        if ($next === false) {
            return;
        }

        $diff = ($tokens[$next]['line'] - $tokens[$i]['line']);
        if ($diff === 1) {
            return;
        }

        if ($diff < 0) {
            $diff = 0;
        }

        $error = 'There must be one blank line after the PHP opening tag';
        $fix = $phpcsFile->addFixableError($error, $stackPtr, 'BlankLineAfter');

        if ($fix === true) {
            if ($diff === 0) {
                $phpcsFile->fixer->addNewlineBefore($i);
            } else {
                $phpcsFile->fixer->beginChangeset();
                for ($x = $i; $x < $next; $x++) {
                    if ($tokens[$x]['line'] === $tokens[$next]['line']) {
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($x, '');
                }

                $phpcsFile->fixer->addNewline($i);
                $phpcsFile->fixer->endChangeset();
            }
        }
    }
}
