<?php

namespace App\Utils;

use App\Entity\Corpse;

class Functions
{
    public function cmpCorpsePosition(Corpse $a, Corpse $b): string
    {
        $a = $a->getPosition();
        $b = $b->getPosition();

        if ($a == $b) return 0;
        return ($a < $b) ? -1 : 1;
    }
}