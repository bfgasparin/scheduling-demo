<?php

namespace App\Jobs\User;

use App\User;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Creates a new user in the system
 *
 * It stores a user account into the storage and sends an activation token
 * to the user in order to activate its account
 */
class Create
{
    use DispatchesJobs;

    public $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Executes the job
     *
     * @return App\User
     */
    public function handle() : User
    {
        return tap(User::create($this->data), function ($user) {
            $this->dispatchNow(new SendActivationToken($user));
        });
    }
}
