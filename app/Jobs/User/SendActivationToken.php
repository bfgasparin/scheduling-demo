<?php

namespace App\Jobs\User;

use Cache;
use App\User;
use Exception;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\Notifications\User\ActivationToken;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{SerializesModels,InteractsWithQueue};

/**
 * Represents the Activation Token Sending Use Case.
 *
 * It Sends a new activation token to the user by sms
 * and temporary storaging the token to the cache
 *
 */
class SendActivationToken implements ShouldQueue
{
    use Queueable,
        Dispatchable,
        InteractsWithQueue,
        SerializesModels;

    /** @var int */
    public $tries = 1;

    /** @var App\User */
    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;

        $this->onQueue('jobs');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() : void
    {
        Cache::put(
            "user_activation_token[{$this->user->id}]",
            bcrypt(with($token = sms_token())),
            Carbon::now()->addDay()
        );

        $this->user->notifyActivationToken($token);
    }

    /**
     * Handle when job fails
     *
     * @return void
     */
    public function failed() : void
    {
        Cache::forget("user_activation_token[{$this->user->id}]");
    }
}
