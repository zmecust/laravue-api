<?php
/**
 * Created by PhpStorm.
 * User: zm
 * Date: 2017/4/22
 * Time: 12:32
 */
namespace App\Repositories;

use App\User;

class UserRepository
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUserList($page = 10, array $condition = [])
    {
        return $this->user->where($condition)->get();
    }
}