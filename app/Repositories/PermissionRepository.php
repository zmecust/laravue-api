<?php
/**
 * Created by PhpStorm.
 * User: zm
 * Date: 2017/4/27
 * Time: 17:50
 */
namespace App\Repositories;

use App\Permission;
use Illuminate\Foundation\Validation\ValidatesRequests;

class PermissionRepository
{
    use ValidatesRequests;
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

    public function getPermissionList($request)
    {
        if (!empty($name = $request->filter)) {
            return $this->permission->where('display_name', 'like', "%$name%")->paginate($request->paginate);
        }

        return $this->permission->paginate($request->paginate);
    }

    /**
     * @return mixed
     */
    public function groupPermissions()
    {
        $permissions = $this->permission->orderBy('name', 'desc')->get();
        $array = [];
        foreach ($permissions as $permission) {
            array_set($array, $permission->name, $permission);
        }

        return $array;
        //return $permissions->toArray();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getPermission($id)
    {
        $permission =  $this->permission->find($id);
        return $permission->toArray();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function createPermission($request)
    {
        //验证数据
        if(method_exists($this->permission, 'rules')) {
            $this->validate($request,
                $this->permission->rules(),
                $this->permission->messages());
        }

        $permission =  $this->permission->create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->display_name,
            'uri' => $request->uri
        ]);

        return $permission->toArray();
    }

    /**
     * @param $request
     * @param $id
     * @return mixed
     */
    public function updatePermission($request, $id)
    {
        //验证数据
        if(method_exists($this->permission, 'rules')) {
            $this->validate($request,
                $this->permission->rules($id),
                $this->permission->messages());
        }

        $permission = $this->permission->find($id);
        $permission->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->display_name,
            'uri' => $request->uri
        ]);

        return $permission->toArray();
    }

    /**
     * @param $id
     * @return bool
     */
    public function deletePermission($id)
    {
        $permission = $this->permission->find($id);

        if (!empty($permission)) {
            $permission->roles()->sync([]);
            $permission->destroy($id);
            return true;
        }

        return false;
    }
}