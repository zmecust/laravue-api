<?php
/**
 * Created by PhpStorm.
 * User: zm
 * Date: 2017/7/13
 * Time: 17:48
 */
namespace App\Http\Controllers;

use Mail;
use Auth;
use JWTAuth;
use App\User;
use Validator;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;
use Naux\Mail\SendCloudTemplate;
use Overtrue\Socialite\SocialiteManager;

class AuthController extends Controller
{
    /**
     * AuthController constructor.
     */
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
            return $this->responseError('表单验证失败', $validator->errors()->toArray());
        }

        $newUser = [
            'email' => $request->get('email'),
            'name' => $request->get('name'),
            'avatar' => env('APP_URL') . '/image/avatar.jpeg',
            'password' => $request->get('password'),
            'confirm_code' => str_random(60),
        ];

        $user = User::create($newUser);
        $this->sendVerifyEmailTo($user);
        $user->attachRole(3);

        return $this->responseSuccess('感谢您支持LaraVue社区，请前往邮箱激活该用户');
    }

    /**
     * @param $user
     */
    private function sendVerifyEmailTo($user)
    {
        $data = [ 'url' => 'https://laravue.org/#/verify_email/' . $user->confirm_code,
                  'name' => $user->name ];
        $template = new SendCloudTemplate('laravue_verify', $data);

        Mail::raw($template, function ($message) use ($user) {
            $message->from('root@laravue.org', 'laravue.org');
            $message->to($user->email);
        });
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyToken()
    {
        $user = User::where('confirm_code', Request('code'))->first();
        if (empty($user)) {
            return $this->responseError('激活失败');
        }
        $user->is_confirmed = 1;
        $user->confirm_code = str_random(60);
        $user->save();
        Auth::login($user);

        $token = JWTAuth::fromUser($user);
        $user->jwt_token = [
            'access_token' => $token,
            'expires_in' => Carbon::now()->addMinutes(config('jwt.ttl'))->timestamp
        ];
        return $this->responseSuccess('注册成功', $user);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
               return $this->responseError('用户名或密码错误');
            }
            $user = Auth::user();
            if ($user->is_confirmed == 0) {
                return $this->responseError('您还未激活该账号，请先前往邮箱激活');
            }
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

    /**
     * @return \Illuminate\Http\JsonResponse
     */
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

    // 第三方Github登录
    /**
     * @return mixed
     */
    public function github()
    {
        $socialite = new SocialiteManager(config('services'));
        return $socialite->driver('github')->redirect();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function githubLogin()
    {
        $socialite = new SocialiteManager(config('services'));
        $githubUser = $socialite->driver('github')->user();
        $user_names = User::pluck('name')->toArray();

        if (in_array($githubUser->getNickname(), $user_names)) {
            $user = User::where('name', $githubUser->getNickname())->first();
        } else {
            $user = User::create([
                'name' => $githubUser->getNickname(),
                'avatar' => $githubUser->getAvatar(),
                'email' => $githubUser->getEmail(),
                'password' => $githubUser->getToken(),
                'is_confirmed' => 1,
                'confirm_code' => str_random(60)
            ]);
            $user->attachRole(3);
        }
        $token = JWTAuth::fromUser($user);
        $user->jwt_token = [
            'access_token' => $token,
            'expires_in' => Carbon::now()->addMinutes(config('jwt.ttl'))->timestamp
        ];
        return redirect('https://laravue.org/#/github/login')->cookie('user', $user, 24*365);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function githubLoginRedirect()
    {
        $user = user::find(request('id'));
        $token = JWTAuth::fromUser($user);
        $user->jwt_token = [
            'access_token' => $token,
            'expires_in' => Carbon::now()->addMinutes(config('jwt.ttl'))->timestamp
        ];
        return $this->responseSuccess('登录成功', $user->toArray());
    }
}
