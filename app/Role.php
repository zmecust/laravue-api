<?php

namespace App;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'id', 'description'];

    /**
     * @param null $id
     * @return array
     */
    public function rules($id = null)
    {
        return [
            'name' => 'required|unique:roles,name,'.$id,
            'description' => 'required|unique:roles,description,'.$id,
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => '角色名不能为空',
            'name.unique' => '该角色名已存在',
            'description.required' => '角色描述不能为空',
            'description.unique' => '该角色描述已存在',
        ];
    }
}
