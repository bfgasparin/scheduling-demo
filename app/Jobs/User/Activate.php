<?php

namespace App\Jobs\User;

use Hash;
use Cache;
use App\User;
use App\Exceptions\Activation\{InvalidActivationToken, ModelAlreadyActive};

class Activate
{
    /** @var App\User */
    public $user;

    /** @var string */
    public $token;

    /**
     * Create a new job instance. @return void */
    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return App\User
     */
    public function handle() : User
    {
        if (! Hash::check($this->token, Cache::get("user_activation_token[{$this->user->id}]"))) {
            throw new InvalidActivationToken;
        }

        return $this->user->activate();
    }
}
