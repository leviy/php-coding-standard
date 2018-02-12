<?php
declare(strict_types=1);

namespace Bar;

use DateTime;

final class Foo
{
    private const NOW = 'now';

    public function getDate(): DateTime
    {
        return new DateTime(self::NOW);
    }
}