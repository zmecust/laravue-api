<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function show($id)
    {
        if (empty($user = Cache::get('users_cache' . $id))) {
            $user = User::findOrFail($id);
            Cache::put('users_cache' . $id, $user, 10);
        }
        return $this->responseSuccess('查询成功', $user);
    }
}
