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

    public function getRoleList($page = 10, array $condition = [])
    {
        return $this->role->where($condition)->paginate($page);
    }

    public function createRole($request)
    {
        $this->save([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description
        ]);

        if (is_array($request->permission)) {
            $permissions = [];
            foreach ($request->permission as $id) {
                $permissions[] = $this->permission->findOrFail($id);
            }
            $this->attachPermissions($permissions);
        }
    }

    public function updateRole($id, $request)
    {
        $role = $this->role->findOrFail($id);
        $role->name = $request->name;
        $role->display_name = $request->display_name;
        $role->description = $request->description;
        $role->save();

        $role->detachPermissions($role->perms); //清除以前的权限

        if (is_array($request->permission)) {
            $permissions = [];
            foreach ($request->permission as $id) {
                $permissions[] = $this->permission->findOrFail($id);
            }
            $role->attachPermissions($permissions);
        } //写入新权限
    }

    public function deleteRole($id)
    {
        $role = $this->role->findOrFail($id);
        // Force Delete
        $role->users()->sync([]);  // 同步清除角色下的用户关联
        $role->perms()->sync([]);  // 同步清除角色下的权限关联

        $role->forceDelete();  // 删除角色
    }
}