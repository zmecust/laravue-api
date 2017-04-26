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

    public function getPermissionList($page = 10, array $condition = [])
    {
        return $this->permission->where($condition)->get();
    }
}