<?php

namespace App\Http\Controllers\API\Auth;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Helper functions to Authenticate a User
 */
trait AuthenticatesUser
{
    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (false === ($token = $this->attemptLogin($request))) {
            throw new UnauthorizedHttpException('jwt-auth', __('Invalid Credentials'));
        }

        return response()->json([
            'token' => $token
        ]);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string', 'password' => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return false|string
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request)
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request) : array
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username() : string
    {
        return 'email';
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    protected function guard() : Guard
    {
        return Auth::guard('api-users');
    }
}
