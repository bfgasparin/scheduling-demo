<?php

use Illuminate\Database\Eloquent\Collection;

/**
 * Help database seeders to seed data for salons
 */
trait SeededSalons
{
    /**
     * Returns the last seeded salons
     * @see self::limit
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    protected function salons() : Collection
    {
        return App\Salon::latest()->take($this->limit())->get();
    }

    /**
     * Get the ammount of salons to limit on seeding
     *
     * @return int
     */
    protected function limit() : int
    {
        return 10;
    }


}
