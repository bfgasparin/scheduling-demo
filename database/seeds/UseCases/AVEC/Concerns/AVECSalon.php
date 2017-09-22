<?php

namespace UseCases\AVEC\Concerns;

use Illuminate\Database\Eloquent\Collection;

/**
 * Help AVEC database seeders to seed data for AVEC salon
 */
trait AVECSalon
{
    /**
     * Returns the last seeded salons
     * @see self::limit
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    protected function salons() : Collection
    {
        return \App\Salon::whereName('Salão AVEC')->get();
    }
}
