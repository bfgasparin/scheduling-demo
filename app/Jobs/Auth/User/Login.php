<?php

namespace App\Jobs\Auth\User;

use App\Auth\UserProvider;
use App\Jobs\User\SendActivationToken;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Auth\Factory as FactoryContract;
use App\Exceptions\Auth\User\{InvalidCredentials, IsNotActive as UserIsNotActive};

/**
 * Logins a User to the system
 * @see App\User
 *
 * if the user tying to login is not active, sends an actvation token to that user
 */
class Login
{
    use DispatchesJobs;

    /** @var string The login value user entered to login */
    protected $login;

    /** @var string The password value user entered to login */
    protected $password;

    /** @var string The Auth guard to authenticated the user */
    protected $guard;

    /**
     * Create a new job instance.
     *
     * @param string $login The login value user entered to login
     * @param string $password The password value user entered to login
     * @param string $guard The Auth guard to authenticated the user
     *
     * @return void
     */
    public function __construct(string $login, string $password, string $guard = 'api-users')
    {
        $this->login = $login;
        $this->password = $password;
        $this->guard = $guard;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws UserIsNotActive throws when the credentais are valid but the user is inactive
     * @throws InvalidCredentials throws when the credentials are invalid
     */
    public function handle(FactoryContract $auth, UserProvider $userProvider)
    {
        $credentials = $this->credentials();

         // If the user authentication fails, we try to find if that user has an inactive account.
         // If we find, we send an activation token to that user, so it can login to the system
         // successfully.
        if (false === ($token = $auth->guard($this->guard)->attempt($credentials))) {
            $user = $userProvider->withoutScopes(['active'])->retrieveByCredentials($credentials);

            if ($user && $userProvider->validateCredentials($user, $credentials)) {
                $this->dispatchNow(new SendActivationToken($user));

                throw new UserIsNotActive($user);
            }

            throw new InvalidCredentials($this->challenge());
        }

        return $token;
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials() : array
    {
        return array_combine(
            ['cellphone', 'email', 'password'],
            [$this->login, $this->login, $this->password]
        );
    }

    /**
     * Gets the WWW-Authenticate challenge string according to the guard
     *
     * @return string
     */
    protected function challenge() : string
    {
        switch ($this->guard) {
            case 'api-users':
                return 'jwt-auth';
        };
    }
}
