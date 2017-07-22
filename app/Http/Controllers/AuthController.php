<?php
/**
 * Created by PhpStorm.
 * User: zm
 * Date: 2017/7/13
 * Time: 17:48
 */
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
use Overtrue\Socialite\SocialiteManager;

class AuthController extends Controller
{
    public function __construct()
    {
        // 执行 jwt.auth 认证
        $this->middleware('jwt.auth', [
            'only' => ['logout']
        ]);
    }

    //获取注册验证码
    public function getRegisterCode()
    {
        $emailCode = str_random(8);
        $email = Request('email');

        if(empty(Cache::get($email))) {
            Cache::put($email, 0, 60);
        }

        if(Cache::get($email) <= 3) {  //60分钟内只能发送三次
            Cache::increment($email);

            if(empty(Cache::get('emailCode'.$email))) {
                Cache::put('emailCode'.$email, $emailCode, 3); //验证码有效期3分钟

                $this->sendVerifyEmailTo($emailCode, $email);

                return $this->responseSuccess('发送注册码成功');

            } else {
                return $this->responseError('请三分钟后再试'); //验证码还在有效期内
            }

        } else {
            return $this->responseError('验证码发送频繁，请稍后再试');
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
            'code' => 'required|size:8',
        ]);

        if ($validator->fails()) {
            return $this->responseError('表单验证失败', $validator->errors()->toArray());
        }

        if ($this->validateEmailCode(Request('email'), Request('code'))) {
            return $this->responseError('注册码验证失败');
        }

        $newUser = [
            'email' => $request->get('email'),
            'name' => $request->get('name'),
            'avatar' => public_path('/image/avatar.jpeg'),
            'password' => $request->get('password'),
        ];

        $user = User::create($newUser);
        $user->attachRole(2);
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

        return $this->responseSuccess('注册成功', $user);
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
            return $this->responseError('表单验证失败', $validator->errors()->toArray());
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

                if (is_null($user)) {
                    return $this->responseError('用户名或密码错误');
                }
            }

            $user = User::find(Auth::id());
            // 设置JWT令牌
            $user->jwt_token = [
                'access_token' => $token,
                'expires_in' => Carbon::now()->addMinutes(config('jwt.ttl'))->timestamp
            ];

            return $this->responseSuccess('登录成功', $user->toArray());
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return $this->responseError('无法创建令牌');
        }
    }

    public function logout()
    {
        try {
            JWTAuth::parseToken()->invalidate();
        } catch (TokenBlacklistedException $e) {
            return $this->responseError('令牌已被列入黑名单');
        } catch (JWTException $e) {
            // 忽略该异常（Authorization为空时会发生）
        }
        return $this->responseSuccess('登出成功');
    }

    //三方登录
    public function github()
    {
        $socialite = new SocialiteManager(config('services'));
        return $socialite->driver('github')->redirect();
    }

    public function githubLogin()
    {
        $socialite = new SocialiteManager(config('services'));
        $githubUser = $socialite->driver('github')->user();
        $user_names = User::pluck('name')->toArray();

        if (in_array($githubUser->getNickname(), $user_names)) {
            $user = User::where('name', $githubUser->getNickname())->first();
            Auth::login($user->id);
        } else {
            $user = User::create([
                'name' => $githubUser->getNickname(),
                'avatar' => $githubUser->getAvatar(),
                'email' => $githubUser->getEmail(),
                'password' => $githubUser->getToken(),
            ]);
            $user->attachRole(2);
            Auth::login($user);
        }

        $token = JWTAuth::fromUser($user);

        $user->jwt_token = [
            'access_token' => $token,
            'expires_in' => Carbon::now()->addMinutes(config('jwt.ttl'))->timestamp
        ];

        return $this->responseSuccess('登录成功', $user);
    }
}