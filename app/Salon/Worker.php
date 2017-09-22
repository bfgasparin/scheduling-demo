<?php

namespace App\Salon;

use App\{Salon, BelongsToSalon};

/**
 * Indicates the user works on a salon
 *
 * A worker does salon tasks for a Salon.
 *
 * A worker can offer a service.
 * A worker can manage the Cashier.
 * A worker can manages the salon administrative operations.
 *
 */
interface Worker extends BelongsToSalon
{
    /**
     * Returns if the worker is Admin
     *
     * @return bool
     */
    public function isAdmin() : bool;

    /**
     * Returns if the worker is a Professional
     * A Professional can offer services on the salon
     *
     * @return bool
     */
    public function isProfessional() : bool;
}
