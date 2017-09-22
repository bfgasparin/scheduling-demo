<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\User\Create as CreateUser;

/**
 * User`s actions to used by anyone
 *
 * @see App\User;
 */
class UserController extends Controller
{
    /**
     * Store a neyly User in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|case_diff|numbers|max:60',
            'cellphone'  => 'required|unique:users|phone:BR,mobile',
        ]);

        return dispatch(new CreateUser($request->all()));
    }
}
