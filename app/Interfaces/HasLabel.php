<?php

namespace App\Interfaces;

interface HasLabel
{
    /**
     * Get the Arabic label for display.
     */
    public function label(): string;
}
