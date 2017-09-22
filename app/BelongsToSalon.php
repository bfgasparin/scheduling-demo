<?php

namespace App;

/**
 * Indicates the model belongs to a salon
 */
interface BelongsToSalon
{
    /**
     * Returns the salon the model belongs to
     *
     * @return Salon
     */
    public function getSalon() : Salon;
}
