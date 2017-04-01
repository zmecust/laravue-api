<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function verifyToken($confirm_code)
    {
        $user = User::where('information_token', $confirm_code)->first();
        if (is_null($user)) {
            return redirect('/');
        }
        $user->is_active = 1;
        $user->information_token = str_random(40);
        $user->save();
        Auth::login($user);
        return redirect('/home');
    }
}
