<?php

namespace App\Eloquent\Concerns;

use App\Salon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Contains methods to access Salon
 */
trait HasSalon
{
    /**
     * Returns the salon the User works on
     *
     * @return App\Salon
     */
    public function getSalon() : Salon
    {
        return $this->salon;
    }

    /**
     * The salon that owns the model
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salon() : BelongsTo
    {
        return $this->belongsTo(Salon::class);
    }
}
