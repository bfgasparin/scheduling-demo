<?php

namespace App\Eloquent\Concerns;

use Illuminate\Database\Eloquent\Builder;
use App\Exceptions\Activation\ModelAlreadyActive;

/**
 * Helps Eloquent Models that has the ability to be activated
 */
trait Activatable
{
    /**
     * Scope a query to only include active models
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public static function bootActivatable() : void
    {
        static::addGlobalScope('active', function (Builder $query) {
            return $query->where('active', true);
        });
    }

    /**
     * Scope a query to only include inactive models
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public static function scopeOnlyInactive(Builder $query) : Builder
    {
        return $query->withInactive()->where('active', false);
    }

    /**
     * Scope a query to include inactive models
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public static function scopeWithInactive(Builder $query) : Builder
    {
        return $query->withoutGlobalScope('active');
    }


    /**
     * Returns if the user is active
     *
     * @return self
     */
    public function isActive() : bool
    {
        return $this->active;
    }

    /**
     * Activate the user
     *
     * @return self
     */
    public function activate() : self
    {
        if ($this->isActive()) {
            throw new ModelAlreadyActive($this);
        }

        return tap($this->forceFill(['active' => true]))->save();
    }
}
