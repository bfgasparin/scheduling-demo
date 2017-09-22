<?php

namespace App\Providers;

use Auth;
use App\Auth\JWTGuard;
use App\Auth\UserProvider;
use App\Auth\EmployeeProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\User' => 'App\Policies\UserPolicy',
        'App\Salon\Client\Booking' => 'App\Policies\Salon\Client\BookingPolicy',
        'App\Salon\Employee' => 'App\Policies\Salon\EmployeePolicy',
        'App\Salon\Service' => 'App\Policies\Salon\ProtectedToAdminUsers',
        'App\Salon\Client' => 'App\Policies\Salon\ClientPolicy',
        'App\Salon\Professional\WorkingJorney' => 'App\Policies\Salon\Professional\WorkingJorneyPolicy',
    ];

    /**
     *
     * Register any authentication / authorization services.
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->addProviders();

        $this->addGuards();
    }

    protected function addGuards() : void
    {
        Auth::extend('jwt', function ($app, $name, array $config) {
            $guard = new JWTGuard(
                $app['tymon.jwt'],
                $app['auth']->createUserProvider($config['provider']),
                $app['request']
            );

            $app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }

    protected function addProviders() : void
    {
        Auth::provider('user', function ($app, array $config) {
            return new UserProvider($app['hash']);
        });

        Auth::provider('salon-admin', function ($app, array $config) {
            return new EmployeeProvider($app['hash'], 'admin');
        });

        Auth::provider('salon-professional', function ($app, array $config) {
            return new EmployeeProvider($app['hash'], 'professional');
        });
    }
}
