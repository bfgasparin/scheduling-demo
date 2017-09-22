<?php

namespace App\User\Concerns;

use App\Notifications\User\ActivationToken;
use App\Exceptions\Activation\ModelAlreadyActive;
use App\Eloquent\Concerns\Activatable as EloquentActivatable;

/**
 * Helps User model to activated itself
 */
trait Activatable
{
    use EloquentActivatable;

    /**
     * Notify user about the activation token
     *
     * @param string $token
     *
     * @return self
     */
    public function notifyActivationToken(string $token) : self
    {
        if ($this->isActive()) {
            throw new ModelAlreadyActive($this);
        }

        $this->notify(new ActivationToken($token));

        return $this;
    }
}
