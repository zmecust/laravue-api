<?php
/**
 * Created by PhpStorm.
 * User: zm
 * Date: 2017/4/24
 * Time: 14:34
 */
namespace  App\Repositories;

use App\Role;
use App\Permission;
use Illuminate\Foundation\Validation\ValidatesRequests;

class RoleRepository
{
    use ValidatesRequests;
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

    /**
     * @param $request
     * @return mixed
     */
    public function getRoleList($request)
    {
        if (!empty($name = $request->filter)) {
            return $this->role->where('name', 'like', "%$name%")->paginate($request->paginate);
        }

        return $this->role->paginate($request->paginate);
    }

    /**
     * @param $id
     * @return array
     */
    public function getRole($id)
    {
        $role = $this->role->where('id', $id)->first();

        $permissions = collect($role->perms()->get())->map(function ($permission) {
            return $permission->display_name;
        })->toArray();

        $data = array_merge($role->toArray(), ['permissions' => $permissions]);
        return $data;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function createRole($request)
    {
        //验证数据
        if(method_exists($this->role, 'rules')) {
            $this->validate($request,
                $this->role->rules(),
                $this->role->messages());
        }

        $role =  $this->role->create([
            'name' => $request->name,
            'display_name' => $request->name,
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

    /**
     * @param $request
     * @param $id
     * @return mixed
     */
    public function updateRole($request, $id)
    {
        //验证数据
        if(method_exists($this->role, 'rules')) {
            $this->validate($request,
                $this->role->rules($id),
                $this->role->messages());
        }

        $role = $this->role->findOrFail($id);
        $role->name = $request->name;
        $role->display_name = $request->name;
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

    /**
     * @param $id
     * @return bool
     */
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