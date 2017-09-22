<?php

namespace App\Http\Controllers\API;

use Auth;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\User\SendActivationToken;
use App\Jobs\User\Activate as ActivateUser;

/**
 * Actions to manage activation user account to be used by anyone
 *
 * @see App\User;
 */
class UserActivationController extends Controller
{
    /**
     * Sends a new activation token o the user
     *
     * @param Request $request
     */
    public function sendNewToken(Request $request)
    {
        $this->validate($request, ['cellphone' => 'required|phone:BR,mobile']);

        dispatch(new SendActivationToken(
            User::onlyInactive()->where('cellphone', $request->input('cellphone'))->firstOrFail()
        ));

        return ['message' => __('Token was sent')];
    }

    /**
     * Activate the user account
     *
     * @param mixed $param
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'cellphone' => 'required|phone:BR,mobile',
            'token' => 'required|string'
        ]);

        dispatch(new ActivateUser(
            $user = User::onlyInactive()->where('cellphone', $request->input('cellphone'))->firstOrFail(),
            $request->input('token')
        ));

        return [
            'token' => Auth::guard('api-users')->login($user->fresh())
        ];
    }
}
