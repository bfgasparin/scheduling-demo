<?php

namespace App\Salon\Employee\Concerns;

/**
 * Contains Professional Employee responsabilities for the Employee Model
 */
trait HasProfessionalResponsabilities
{
    use ManageServices,
        ManageWorkingJorney,
        ManageClientBookings;
}
