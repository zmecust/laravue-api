<?php
/**
 * Created by PhpStorm.
 * User: zm
 * Date: 2017/4/19
 * Time: 18:14
 */

namespace App\Repositories;

use Cache;
use Auth;
use App\Menu;
use App\User;
use App\Permission;
use Illuminate\Foundation\Validation\ValidatesRequests;

class MenuRepository
{
    use ValidatesRequests;
    /**
     * @var Menu
     */
    protected $menu;

    /**
     * @var Permission
     */
    protected $permission;

    /**
     * @var User
     */
    protected $user;

    /**
     * MenuRepository constructor.
     * @param Menu $menu
     * @param User $user
     * @param Permission $permission
     */
    public function __construct(Menu $menu, User $user, Permission $permission)
    {
        $this->menu = $menu;
        $this->user = $user;
        $this->permission = $permission;
    }

    /**
     * 递归获取当前用户菜单树
     * @param $request
     * @param int $parent_id
     * @return array
     */
    public function getSidebarMenu($request, $parent_id = 0)
    {
        $menus = $this->menu->where('parent_id', $parent_id)->orderBy('sort' ,'asc')->get()->toArray();
        $sidebar_menus = array();

        if (!empty($menus)) {
            foreach ($menus as $menu) {
                //权限验证匹配uri验证uri对应权限
                $permission_info = $this->permission->where(['uri' => $menu['name']])->pluck('name');

                //不存在权限验证的直接通过，比如一级菜单
                if (!empty($permission_info)) {
                    $permissions = Auth::user()->roles()->first()
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

    /**
     * 递归获取全部菜单树
     * @param int $parent_id
     * @return array
     */
    public function getAllMenu($parent_id = 0)
    {
        $menus = $this->menu->where('parent_id', $parent_id)->orderBy('sort' ,'asc')->get()->toArray();
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

    /**
     * 获取父菜单或子菜单
     * @param int $parent_id
     * @return mixed
     */
    public function getParentMenu($parent_id = 0)
    {
        return $this->menu->where('parent_id', $parent_id)->get()->toArray();
    }

    /**
     * 获取某个菜单
     * @param $id
     * @return mixed
     */
    public function getMenu($id)
    {
        $menu =  $this->menu->find($id);
        return $menu->toArray();
    }

    /**
     * @param $parent_id
     * @param $request
     * @return bool
     */
    public function createMenu($parent_id, $request)
    {
        //验证数据
        if(method_exists($this->menu, 'rules')) {
            $this->validate($request,
                $this->menu->rules(),
                $this->menu->messages());
        }

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

    /**
     * @param $request
     * @param $id
     * @return bool
     */
    public function updateMenu($request, $id)
    {
        //验证数据
        if(method_exists($this->menu, 'rules')) {
            $this->validate($request,
                $this->menu->rules($id),
                $this->menu->messages());
        }

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

    /**
     * @param $id
     * @return bool
     */
    public function delMenu($id)
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
            return true;
        }

        return false;
    }

    /**
     * 缓存菜单
     */
    public function setMenuAllCache()
    {
        $menus = $this->getAllMenu();
        Cache::forever('all_menus', array_values($menus));
    }
}