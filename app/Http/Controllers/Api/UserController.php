<?php

namespace App\Http\Controllers\Api;

use Mail;
use Auth;
use Cache;
use JWTAuth;
use App\User;
use App\Role;
use Validator;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;
use Naux\Mail\SendCloudTemplate;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function __construct()
    {
        // 执行 jwt.auth 认证
        $this->middleware('jwt.auth', [
            'only' => ['logout']
        ]);
    }

    public function getRegisterCode()
    {
        $emailCode = str_random(8);
        $email = Request('email');

        if(empty(Cache::get($email))) {
            Cache::put($email, 0, 100);
        }

        if(Cache::get($email) <= 3) {
            Cache::increment($email);

            if(empty(Cache::get('emailCode'.$email))) {
                Cache::put('emailCode'.$email, $emailCode, 5);

                $this->sendVerifyEmailTo($emailCode, $email);

                return $this->responseSuccess('发送注册码成功');

            } else {
                return $this->responseError('请五分钟后再试');
            }

        } else {
            return $this->responseError('发送频繁，请一百分钟后再试');
        }
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
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseError(trans('validation.failed'), $validator->errors()->toArray());
        }

        if ($this->validateEmailCode(Request('email'), Request('code'))) {
            return $this->responseError('注册码验证失败');
        }

        $newUser = [
            'email' => $request->get('email'),
            'name' => $request->get('name'),
            'avatar' => 'https://avatars.githubusercontent.com/u/19644407?v=3',
            'password' => $request->get('password'),
        ];

        $user = User::create($newUser);
        Auth::login($user);
        $token = JWTAuth::fromUser($user);

        $user->jwt_token = [
            'access_token' => $token,
            'expires_in' => Carbon::now()->addMinutes(config('jwt.ttl'))->timestamp
        ];

        /*$roles = $user->roles()->get();
        $role = collect($roles)->map(function($role) {
            return $role->name;
        })->flatten(1)->toArray();

        $data = array_merge($user->toArray(), ['role' => implode($role)]);*/

        return $this->responseSuccess(trans('register_success'), $user);
    }

    private function validateEmailCode($email, $code)
    {
        $emailCode = Cache::get('emailCode'.$email);

        if($emailCode !== $code) {
            return true;
        }

        return false;
    }

    private function sendVerifyEmailTo($emailCode, $email)
    {
        $template = new SendCloudTemplate('laravue_verify', ['code' => $emailCode]);

        Mail::raw($template, function ($message) use ($email) {
            $message->from('root@laravue.org', 'laravue.org');
            $message->to($email);
        });
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
        ]);

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                $user = User::where($field, $request->get('login'))->first();

                if(is_null($user)){
                    return $this->responseError(trans('auth.failed'));
                }
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