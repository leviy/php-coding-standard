<?php

namespace LEVIY\Sniffs\Commenting;

if (class_exists('PHP_CodeSniffer_Tokenizers_Comment', true) === false) {
    $error = 'Class PHP_CodeSniffer_Tokenizers_Comment not found';
    throw new \PHP_CodeSniffer_Exception($error);
}

if (class_exists('PEAR_Sniffs_Commenting_ClassCommentSniff', true) === false) {
    $error = 'Class PEAR_Sniffs_Commenting_ClassCommentSniff not found';
    throw new \PHP_CodeSniffer_Exception($error);
}

/**
 * ClassCommentSniff
 *
 * @author Dennis Coorn <dcoorn@leviy.com>
 * @copyright Copyright (c) 2017 LEVIY <https://leviy.com>
 * @package LEVIY\Sniffs\Commenting
 */
class ClassCommentSniff extends \Symfony2_Sniffs_Commenting_ClassCommentSniff
{
    /** @var array */
    protected $tags = [
        '@author' => [
            'required' => true,
            'allow_multiple' => true,
            'order_text' => 'precedes @copyright',
        ],
        '@copyright' => [
            'required' => true,
            'allow_multiple' => true,
            'order_text' => 'follows @author',
        ],
        '@package' => [
            'required' => true,
            'allow_multiple' => false,
            'order_text' => 'follows @copyright',
        ],
        '@deprecated' => [
            'required' => false,
            'allow_multiple' => false,
            'order_text' => 'follows @package',
        ],
    ];

    /**
     * @param \PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     * @param int $commentStart
     * @return void
     */
    protected function processTags(\PHP_CodeSniffer_File $phpcsFile, $stackPtr, $commentStart)
    {
        $tokens = $phpcsFile->getTokens();

        $next = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $commentStart);

        $className = $phpcsFile->getDeclarationName($stackPtr);

        if ($tokens[$next]['content'] !== $className) {
            $error = 'First line of class docblock must contain the ClassName';
            $phpcsFile->addError($error, $next, 'MissingClassName');
        }

        parent::processTags($phpcsFile, $stackPtr, $commentStart);
    }

    /**
     * @param \PHP_CodeSniffer_File $phpcsFile
     * @param array $tags
     * @return void
     */
    protected function processCopyright(\PHP_CodeSniffer_File $phpcsFile, array $tags)
    {
        $tokens = $phpcsFile->getTokens();
        foreach ($tags as $tag) {
            if ($tokens[($tag + 2)]['code'] !== T_DOC_COMMENT_STRING) {
                continue;
            }

            $content = $tokens[($tag + 2)]['content'];
            $matches = array();
            if (!preg_match('/^(.*)?([0-9]{4})((.{1})([0-9]{4}))? (.+)$/', $content, $matches)) {
                $error = '@copyright tag must contain a year and the name of the copyright holder';
                $phpcsFile->addError($error, $tag, 'IncompleteCopyright');
            }
        }
    }
}
