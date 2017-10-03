<?php

namespace LEVIY\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ClassCommentSniff implements Sniff
{
    const SNIFF_COMMENT_TAG_NAMES = ['@author', '@copyright', '@package'];

    /**
     * @return array
     */
    public function register()
    {
        return [
            T_CLASS,
            T_INTERFACE,
            T_TRAIT,
        ];
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $commentStart = $phpcsFile->findPrevious(T_DOC_COMMENT_OPEN_TAG, $stackPtr);
        if ($commentStart === false) {
            return;
        }

        $commentTagNames = $this->resolveCommentTagNames($tokens, $commentStart);
        $intersect = array_intersect(self::SNIFF_COMMENT_TAG_NAMES, $commentTagNames);

        if (count($intersect) === 0) {
            return;
        }

        $error = "We don't do class comments anymore";
        $fix = $phpcsFile->addFixableError($error, $stackPtr, 'RemoveClassComment');

        if ($fix === true) {
            $commentEnd = $tokens[$commentStart]['comment_closer'];

            $phpcsFile->fixer->beginChangeset();

            for ($i = $commentStart; $i <= $commentEnd; $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }

            $phpcsFile->fixer->endChangeset();
        }
    }

    /**
     * @param array $tokens
     * @param $commentStart
     * @return string[]
     */
    private function resolveCommentTagNames(array $tokens, $commentStart)
    {
        $commentTagPositions = $tokens[$commentStart]['comment_tags'];

        return array_reduce($commentTagPositions, function ($commentTagNames, $commentTagPosition) use ($tokens) {
            $commentTagNames[] = $tokens[$commentTagPosition]['content'];

            return $commentTagNames;
        }, []);
    }
}
