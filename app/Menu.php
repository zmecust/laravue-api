<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['parent_id', 'order', 'name', 'display_name'];

    /**
     * @param null $id
     * @return array
     */
    public function rules($id = null)
    {
        return [
            'name' => 'required|unique:menus,name,'.$id,
            'display_name' => 'required|unique:menus,display_name,'.$id,
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => '菜单名不能为空',
            'name.unique' => '该菜单名已存在',
            'display_name.required' => '导航地址不能为空',
            'display_name.unique' => '该导航地址已存在',
        ];
    }
}
