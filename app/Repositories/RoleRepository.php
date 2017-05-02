<?php
/**
 * Created by PhpStorm.
 * User: zm
 * Date: 2017/4/22
 * Time: 12:32
 */
namespace App\Repositories;

use App\Permission;
use App\Role;

class RoleRepository
{
    /**
     * @var Role
     */
    protected $role;

    /**
     * @var Permission
     */
    protected $permission;


    /**
     * RoleRepository constructor.
     * @param Role $role
     * @param Permission $permission
     */
    public function __construct(Role $role, Permission $permission)
    {
        $this->role = $role;
        $this->permission = $permission;
    }

    public function getRoleList($request, $page = 10)
    {
        if (!empty($name = $request->get('name'))) {
            return $this->role->where('name', 'like', "%$name%")->paginate($page);
        }

        return $this->role->paginate($page);
    }

    public function getRole($id)
    {
        $role = $this->role->where('id', $id)->first();

        $permissions = collect($role->perms()->get())->map(function ($permission) {
            return $permission->description;
        })->toArray();

        $data = array_merge($role->toArray(), ['permissions' => $permissions]);
        return $data;
    }

    public function createRole($request)
    {
        $role =  $this->role->create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        if (is_array($request->permission)) {
            $permissions = [];
            foreach ($request->permission as $name) {
                $permissions[] = $this->permission->where('display_name', $name)->first();
            }
            $role->attachPermissions($permissions);
        }

        return $role->toArray();
    }

    public function updateRole($request, $id)
    {
        $role = $this->role->findOrFail($id);
        $role->name = $request->name;
        $role->description = $request->description;
        $role->save();

        $role->detachPermissions($role->perms); //清除以前的权限

        if (is_array($request->permission)) {
            $permissions = [];
            foreach ($request->permission as $name) {
                $permissions[] = $this->permission->where('display_name', $name)->first();
            }
            $role->attachPermissions($permissions);
        } //写入新权限

        return $role->toArray();
    }

    public function deleteRole($id)
    {
        $role = $this->role->find($id);
        // Force Delete
        if (!empty($role)) {
            $role->users()->detach($id); // 同步清除角色下的用户关联
            $role->perms()->detach($id); // 同步清除角色下的权限关联
            $role->delete(); // 删除角色
            return true;
        }

        return false;
    }
}