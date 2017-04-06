<?php

namespace App\Http\Controllers\Api;

use Mail;
use Auth;
use JWTAuth;
use App\User;
use Validator;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;
use Naux\Mail\SendCloudTemplate;

class UserController extends ApiController
{
    public function __construct()
    {
        // 执行 jwt.auth 认证
        $this->middleware('jwt.auth', [
            'only' => ['logout']
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users|between:4,12',
            'email' => 'required|email|unique:users',
            'password' => 'required|between:6,16|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->responseError(trans('validation.failed'), $validator->errors()->toArray());
        }

        $newUser = [
            'email' => $request->get('email'),
            'name' => $request->get('name'),
            'avatar' => 'https://avatars.githubusercontent.com/u/19644407?v=3',
            'information_token' => str_random(40),
            'password' => $request->get('password'),
        ];

        $user = User::create($newUser);
        $this->sendVerifyEmailTo($user);

        return $this->responseSuccess(trans('register_success'));
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

        $user->jwt_token = [
            'access_token' => $token,
            'expires_in' => Carbon::now()->addMinutes(config('jwt.ttl'))->timestamp
        ];

        return $this->responseSuccess('verify email success', $user->toArray());
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required|between:6,16',
        ]);

        if ($validator->fails()) {
            return $this->responseError(trans('validation.failed'), $validator->errors()->toArray());
        }

        $field = filter_var($request->get('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $credentials = array_merge([
            $field => $request->get('login'),
            'password' => $request->get('password'),
            'is_active' => 1,
        ]);

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                $user = User::where($field, $request->get('login'))->first();

                if(is_null($user)!==true && $user->is_active == 0){
                    return $this->responseError(trans('auth.has_not_verify_email'));
                }

                return $this->responseError(trans('auth.failed'));
            }

            $user = User::find(Auth::id());
            // 设置JWT令牌
            $user->jwt_token = [
                'access_token' => $token,
                'expires_in' => Carbon::now()->addMinutes(config('jwt.ttl'))->timestamp
            ];

            return $this->responseSuccess('login success', $user->toArray());
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return $this->responseError(trans('jwt.could_not_create_token'));
        }
    }

    public function logout()
    {
        try {
            JWTAuth::parseToken()->invalidate();
        } catch (TokenBlacklistedException $e) {
            return $this->responseError(trans('jwt.the_token_has_been_blacklisted'));
        } catch (JWTException $e) {
            // 忽略该异常（Authorization为空时会发生）
        }
        return $this->responseSuccess('logout success');
    }

}