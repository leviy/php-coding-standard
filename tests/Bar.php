<?php

declare(strict_types = 1);

namespace Bar;

use \DateTime;

class Bar
{
    const NOW = 'now';

    public function getDate() : DateTime
    {
        $now = strtolower(self::NOW);

        return new DateTime($now);
    }
}
