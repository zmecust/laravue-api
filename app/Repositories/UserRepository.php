<?php
/**
 * Created by PhpStorm.
 * User: zm
 * Date: 2017/4/22
 * Time: 12:32
 */
namespace App\Repositories;

use App\User;
use App\Role;

class UserRepository
{
    protected $user;

    protected $role;

    public function __construct(User $user, Role $role)
    {
        $this->user = $user;
        $this->role = $role;
    }

    public function getUserList($request, $page = 10)
    {
        if (!empty($name = $request->get('name'))) {
            return $this->user->where('name', 'like', "%$name%")->with([
                'roles' => function($query) {
                    $query->select('name');
                }])->paginate($page);
        }

        return $this->user->with([
            'roles' => function($query) {
                $query->select('name');
            }])->paginate($page);
    }

    public function getUser($id)
    {
        $user = $this->user->find($id);
        $roles = $user->roles()->pluck('description');
        return $roles;
    }

    public function updateUser($request, $id)
    {
        $user = $this->user->findOrFail($id);
        $user->detachRoles($user->roles); //清除以前的角色

        if (is_array($request->roles)) {
            $roles = [];
            foreach ($request->roles as $description) {
                $roles[] = $this->role->where('description', $description)->first();
            }
            $user->attachRoles($roles);
        } //写入新角色

        return $this->user->where('id', $id)->with([
            'roles' => function($query) {
                $query->select('name');
            }])->first()->toArray();
    }

    public function deleteUser($id)
    {
        $user = $this->user->find($id);

        if ($user) {
            $user->roles()->sync([]);
            $user->destroy($id);
            return true;
        }

        return false;
    }
}