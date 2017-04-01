<?php

namespace App\Http\Controllers\Api;

use JWTAuth;
use App\User;
use App\Http\Requests;
use Illuminate\Http\Request;

class RegisterController extends ApiController
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function register(Request $request)
    {
        dd('test');
        $rules = [
            'name' => 'required|unique:users|between:4,12',
            'email' => 'required|email|unique:users',
            'password' => 'required|between:6,32|confirmed',
        ];
        $this->validate($request, $rules);

        $newUser = [
            'email' => $request->get('email'),
            'name' => $request->get('name'),
            'avatar' => 'https://avatars.githubusercontent.com/u/19644407?v=3',
            'information_token' => str_random(40),
            'password' => bcrypt($request->get('password')),
        ];

        $user = User::create($newUser);
        $token = JWTAuth::fromUser($user);

        return $this->responseSuccess('register success', $token);
    }

}
