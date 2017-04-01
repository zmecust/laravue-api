<?php

namespace App\Http\Controllers\Auth;

use Mail;
use App\User;
use Validator;
use Illuminate\Http\Request;
use Naux\Mail\SendCloudTemplate;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        return redirect($this->redirectPath());
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user =  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'avatar' => '/images/favicon.jpg',
            'information_token' => str_random(40),
            'password' => bcrypt($data['password']),
        ]);
        $this->sendVerifyEmailTo($user);
    }

    private function sendVerifyEmailTo($user)
    {
        $data = ['url' => route('email.verify', ['token' => $user->information_token]),
            'name' => $user->name,
        ];
        $template = new SendCloudTemplate('ZMC_welcome', $data);

        Mail::raw($template, function ($message) use ($user) {
            $message->from('247281377@qq.com', 'ZMC社区');
            $message->to($user->email);
        });
    }
}
