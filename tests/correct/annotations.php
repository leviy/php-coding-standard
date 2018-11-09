<?php
declare(strict_types=1);

/**
 * @whatever
 */
function allUsefulAnnotationsAreAllowed(): void
{
}

/**
 * @param int[] $numbers
 */
function omittingUselessReturnAnnotationsIsAllowed(array $numbers): void
{
}

/**
 * @param int[] $numbers
 */
function omittingUselessParamAnnotationsIsAllowed(string $string, int $int, array $numbers, bool $bool): void
{
}
