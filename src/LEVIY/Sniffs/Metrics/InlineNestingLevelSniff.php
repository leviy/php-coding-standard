<?php

namespace LEVIY\Sniffs\Metrics;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class InlineNestingLevelSniff implements Sniff
{
    /** @var int */
    public $nestingLevel = 2;
    /** @var int */
    public $absoluteNestingLevel = 3;

    /**
     * @return array
     */
    public function register()
    {
        return [T_OPEN_PARENTHESIS];
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $start = $tokens[$stackPtr]['parenthesis_opener'];
        $end = $tokens[$stackPtr]['parenthesis_closer'];

        if ($tokens[$start]['line'] !== $tokens[$end]['line']) {
            return;
        }

        $nestingLevel = $this->countNestingLevel($phpcsFile, $start, $end);

        if ($nestingLevel > $this->absoluteNestingLevel) {
            $error = 'Inline nesting level (%s) exceeds allowed maximum of %s';
            $data  = [
                $nestingLevel,
                $this->absoluteNestingLevel,
            ];

            $phpcsFile->addError($error, $stackPtr, 'MaxExceeded', $data);
        } else if ($nestingLevel > $this->nestingLevel) {
            $warning = 'Inline nesting level (%s) exceeds %s; consider refactoring';
            $data    = [
                $nestingLevel,
                $this->nestingLevel,
            ];

            $phpcsFile->addWarning($warning, $stackPtr, 'TooHigh', $data);
        }
    }

    /**
     * @param File $phpcsFile
     * @param int $start
     * @param int $end
     * @return int
     */
    private function countNestingLevel(File $phpcsFile, $start, $end)
    {
        $tokens = $phpcsFile->getTokens();

        $count = 0;

        for ($i = ($start + 1); $i < $end; $i++) {
            if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                $count++;
            }

            if ($tokens[$i]['code'] === T_CLOSE_PARENTHESIS) {
                break;
            }
        }

        return $count;
    }
}
