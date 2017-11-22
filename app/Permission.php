<?php

namespace App;

use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'uri', 'id', 'display_name'];

    /**
     * @param null $id
     * @return array
     */
    public function rules($id = null)
    {
        return [
            'name' => 'required|unique:permissions,name,'.$id,
            'display_name' => 'required',
            'uri' => 'required'
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => '权限名不能为空',
            'name.unique' => '该权限名已存在',
            'display_name.required' => '权限标识不能为空',
            'uri.required' => '绑定前端路由名不能为空',
        ];
    }
}
