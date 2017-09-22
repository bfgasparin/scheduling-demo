<?php

namespace App\Http\Controllers\API\Auth\Salon;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use App\Http\Controllers\API\Auth\AuthenticatesUser;

/**
 * Salon Admin Login Controller
 */
class AdminLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Salon Admin Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating salon admins for the application.
    | The controller uses a trait to conveniently handle authentication issues
    |
    */
    use AuthenticatesUser;

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    protected function guard() : Guard
    {
        return Auth::guard('api-salon-admins');
    }
}
