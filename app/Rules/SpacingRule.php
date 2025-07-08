<?php

namespace App\Rules;

class SpacingRule
{
    public function getRequiredSpots($dayOfWeek)
    {
        if ($dayOfWeek == 0 || $dayOfWeek == 6) {
            return 3;
        }
        return 1;
    }
} 