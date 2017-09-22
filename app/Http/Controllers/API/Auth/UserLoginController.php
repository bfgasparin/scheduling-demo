<?php

namespace App\Http\Controllers\API\Auth;

use Illuminate\Http\Request;
use App\Jobs\Auth\User\Login;
use App\Http\Controllers\Controller;

/**
 * User Login Controller
 */
class UserLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | User Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application.
    | The controller uses a trait to conveniently handle authentication issues
    |
    */
    use AuthenticatesUser;

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return false|string
     */
    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);

        return dispatch(new Login(...array_values($credentials)));
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username() : string
    {
        return 'login';
    }
}
