<?php
/**
 * Created by PhpStorm.
 * User: zm
 * Date: 2017/4/22
 * Time: 12:32
 */
namespace App\Repositories;

use Cache;
use Entrust;
use App\Menu;
use App\User;
use App\Permission;

class MenuRepository
{
    /**
     * @var Menu
     */
    protected $menu;

    /**
     * @var Permission
     */
    protected $permission;

    protected $user;

    public function __construct(Menu $menu, User $user, Permission $permission)
    {
        $this->menu = $menu;
        $this->user = $user;
        $this->permission = $permission;
    }

    public function getSidebarMenu($request, $parent_id = 0)
    {
        $menus = $this->menu->where('parent_id', $parent_id)->orderBy('order' ,'asc')->get()->toArray();
        $sidebar_menus = array();

        if (!empty($menus)) {
            foreach ($menus as $menu) {
                //权限验证匹配uri验证uri对应权限
                $permission_info = $this->permission->where(['uri' => $menu['name']])->pluck('name');

                //不存在权限验证的直接通过，比如一级菜单
                if (!empty($permission_info)) {
                    $access_token = $request->header('authorization');
                    $user_id = Cache::get('CMS'.$access_token);
                    $permissions = $this->user->where('id', $user_id)->first()
                        ->roles()->first()
                        ->perms()->pluck('name')->toArray(); //获取当前用户所有权限名

                    if (!in_array(implode($permission_info->toArray()), $permissions)) {
                        continue;
                    }  //根据路由名称查询权限
                    //用户权限检查，不存在的权限不显示
                }

                $sidebar_menus[$menu['id']] = $menu;
                $menu_child = $this->getSidebarMenu($request, $menu['id']);

                if (!empty($menu_child)) {
                    //子菜单不为空放在 children 数组中
                    $sidebar_menus[$menu['id']]['children'] = array_values($menu_child);
                }
            }
        }
        return $sidebar_menus;
    }

    public function getAllMenu($parent_id = 0)
    {
        $menus = $this->menu->where('parent_id', $parent_id)->orderBy('order' ,'asc')->get()->toArray();
        $sidebar_menus = array();

        if (!empty($menus)) {
            foreach ($menus as $menu) {
                $sidebar_menus[$menu['id']] = $menu;
                $menu_child = $this->getAllMenu($menu['id']);

                if (!empty($menu_child)) {
                    //子菜单不为空放在 children 数组中
                    $sidebar_menus[$menu['id']]['children'] = array_values($menu_child);
                }
            }
        }
        return $sidebar_menus;
    }

    public function getParentMenu()
    {
        return $this->menu->where('parent_id', 0)->get()->toArray();
    }

    public function getChildrenMenu($parent_id)
    {
        return $this->menu->where('parent_id', $parent_id)->get()->toArray();
    }

    public function getMenu($id)
    {
        $menu =  $this->menu->find($id);
        return $menu->toArray();
    }

    public function createMenu($parent_id, $request)
    {
        if ($this->menu->create([
            'parent_id' => $parent_id,
            'name' => $request->name,
            'display_name' => $request->display_name,
        ])) {
            $this->setMenuAllCache();
        } else {
            return false;
        }
        return true;
    }

    public function updateMenu($id, $request)
    {
        $menu = $this->menu->find($id);
        $menu->name = $request->name;
        $menu->display_name = $request->display_name;

        if ($menu->save()) {
            $this->setMenuAllCache();
        } else {
            return false;
        }
        return true;
    }

    public function delMenu ($id)
    {
        $menu = $this->menu->find($id);
        if ($menu) {
            $this->menu->destroy($id);
            $child = $this->menu->where(['parent_id' => $menu->id])->get();
            if ($child) {
                foreach ($child as $value) {
                    $this->delMenu($value->id);
                }
            }
        }
    }

    public function setMenuAllCache()
    {
        $menus = $this->getAllMenu();
        Cache::forever('all_menus', array_values($menus));
    }
}