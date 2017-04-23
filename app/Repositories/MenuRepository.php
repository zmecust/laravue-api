<?php
/**
 * Created by PhpStorm.
 * User: zm
 * Date: 2017/4/22
 * Time: 12:32
 */
namespace App\Repositories;

use Entrust;
use App\Menu;
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

    /**
     * MenuRepository constructor.
     * @param Menu $menu
     * @param Permission $permission
     */
    public function __construct(Menu $menu, Permission $permission)
    {
        $this->menu = $menu;
        $this->permission = $permission;
    }

    /**
     * @param int $parent_id
     * @return array
     */
    public function getMenu($parent_id = 0)
    {
        $menus = $this->menu->where('parent_id', $parent_id)->orderBy('order' ,'asc')->get()->toArray();
        $sidebar_menus = array();

        if (!empty($menus)) {
            foreach ($menus as $menu) {
                //权限验证匹配uri验证uri对应权限
                $permission_info = $this->permission->where(['uri' => $menu['name']])->first();

                //不存在权限验证的直接通过，比如一级菜单
                if (!empty($permission_info)) {
                    //用户权限检查，不存在的权限不显示
                    if (!Entrust::can($permission_info['name'])) {
                        continue;
                    }
                }

                $sidebar_menus[$menu['id']] = $menu;
                $menu_child = $this->getMenu($menu['id']);

                if (!empty($menu_child)) {
                    //子菜单不为空放在 children 数组中
                    $sidebar_menus[$menu['id']]['children'] = array_values($menu_child);
                }
            }
        }
        return $sidebar_menus;
    }
}