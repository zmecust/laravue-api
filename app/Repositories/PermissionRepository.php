<?php
/**
 * Created by PhpStorm.
 * User: zm
 * Date: 2017/4/26
 * Time: 18:51
 */
namespace App\Repositories;

use App\Permission;

class PermissionRepository
{
    /**
     * @var Permission
     */
    protected $permission;

    /**
     * RoleRepository constructor.
     * @param Permission $permission
     */
    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    public function getPermissionList($request, $page = 10)
    {
        if (!empty($name = $request->get('name'))) {
            return $this->permission->where('name', 'like', "%$name%")->paginate($page);
        }

        return $this->permission->paginate($page);
    }

    public function groupPermissions()
    {
        $permissions = $this->permission->orderBy('name', 'desc')->get();

        /*$array = [];
        foreach ($permissions as $permission) {
            array_set($array, $permission->name, $permission);
        }

        return $array;*/
        return $permissions->toArray();
    }

    public function getPermission($id)
    {
        $permission =  $this->permission->find($id);

        return $permission->toArray();
    }

    public function createPermission($request)
    {
        $permission =  $this->permission->create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'uri' => $request->uri
        ]);

        return $permission->toArray();
    }

    public function updatePermission($request, $id)
    {
        $permission = $this->permission->find($id);
        $permission->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'uri' => $request->uri
        ]);

        return $permission->toArray();
    }

    public function deletePermission($id)
    {
        $permission = $this->permission->find($id);

        if ($permission) {
            $permission->roles()->sync([]);
            $permission->destroy($id);
            return true;
        }

        return false;
    }
}