<?php

namespace App\Http\Controllers\Api;

use Mail;
use Auth;
use JWTAuth;
use App\User;
use Validator;
use App\Http\Requests;
use Illuminate\Http\Request;
use Naux\Mail\SendCloudTemplate;

class UserController extends ApiController
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users|between:4,12',
            'email' => 'required|email|unique:users',
            'password' => 'required|between:6,32|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->responseError('表单验证失败', $validator->errors()->toArray());
        }

        $newUser = [
            'email' => $request->get('email'),
            'name' => $request->get('name'),
            'avatar' => 'https://avatars.githubusercontent.com/u/19644407?v=3',
            'information_token' => str_random(40),
            'password' => bcrypt($request->get('password')),
        ];

        $user = User::create($newUser);
        $this->sendVerifyEmailTo($user);
    }

    private function sendVerifyEmailTo($user)
    {
        $data = ['url' => route('email.verify', ['token' => $user->information_token]),
            'name' => $user->name,
        ];
        $template = new SendCloudTemplate('laravue_verify', $data);

        Mail::raw($template, function ($message) use ($user) {
            $message->from('root@laravue.org', 'laravue.org');
            $message->to($user->email);
        });
    }

    public function verifyToken($confirm_code)
    {
        $user = User::where('information_token', $confirm_code)->first();

        if (is_null($user)) {
            return $this->responseError('verify email failed');
        }

        $user->is_active = 1;
        $user->information_token = str_random(40);
        $user->save();
        Auth::login($user);
        $token = JWTAuth::fromUser($user);

        return $this->responseSuccess('verify email success', array_merge($user->toArray(), ['jwt_token' => $token]));
    }

}
