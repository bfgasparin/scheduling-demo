<?php

namespace App\Salon\Employee\Concerns;

use BadMethodCallException;
use App\Salon\Professional\WorkingJorney;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Contains methods to help the professisonal to manage its WorkingJorney
 */
trait ManageWorkingJorney
{
    /**
     * The working jorney of the professional
     */
    public function workingJorney() : HasOne
    {
        if (!$this->isProfessional()) {
            throw new BadMethodCallException('Worker can not have a Working Jorney');
        }

        return $this->hasOne(WorkingJorney::class, 'professional_id');
    }

    /**
     * Returns if the professional can register a schedule on the
     * given date
     *
     * @param mixed $date
     *
     * @return bool
     */
    public function canRegisterScheduleOn($date) : bool
    {
        return $this->workingJorney->representsDate($date) &&
            ! $this->workingJorney->schedules->contains->isOnDate($date);
    }

    /**
     * Returns if the professional can register an absence on the
     * given date
     *
     * @param mixed $date
     *
     * @return bool
     */
    public function canRegisterAbsenceOn($date) : bool
    {
        return $this->workingJorney->representsDate($date) &&
            ! $this->workingJorney->absences->contains->isOnDate($date);
    }
}
