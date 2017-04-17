<?php

namespace App\Http\Controllers\Api;

use App\Role;
use App\Permission;

class SidebarTreeController extends ApiController
{
    public function getSidebarTree()
    {
        //
    }

    function generateTree()
    {
        /* 处理下标从1开始的数组
         * $items = array(
            1 => array('id' => 1, 'parent_id' => 0, 'name' => '安徽省'),
            2 => array('id' => 2, 'parent_id' => 0, 'name' => '浙江省'),
            3 => array('id' => 3, 'parent_id' => 1, 'name' => '合肥市'),
            4 => array('id' => 4, 'parent_id' => 3, 'name' => '长丰县'),
            5 => array('id' => 5, 'parent_id' => 1, 'name' => '安庆市'),
        );

        $tree = array();
        foreach($items as $item){
            if(isset($items[$item['parent_id']])){
                $items[$item['parent_id']]['children'][] = &$items[$item['id']];
            }else{
                $tree[] = &$items[$item['id']];
            }
        }*/

        $items  = Role::where('name', request('role'))->first()->perms()->get();

        $tree = $this->getTree($items->toArray(), 0, 'id', 'parent_id', 'children');

        /*$access_menu = $items->pluck('name')->toArray();
        $data = array_merge(['/backend'], $access_menu);*/

        return $this->responseSuccess('OK', $tree);
    }

    public function getTree($data, $pid = 0, $key = 'id', $pKey = 'parentId', $childKey = 'child', $maxDepth = 0)
    {
        static $depth = 0;
        $depth++;

        if (intval($maxDepth) <= 0)
        {
            $maxDepth = count($data) * count($data);
        }

        if ($depth > $maxDepth)
        {
            exit("error recursion:max recursion depth {$maxDepth}");
        }

        $tree = array();

        foreach ($data as $rk => $rv)
        {
            if ($rv[$pKey] == $pid)
            {
                $rv[$childKey] = $this->getTree($data, $rv[$key], $key, $pKey, $childKey, $maxDepth);
                $tree[] = $rv;
            }
        }

        return $tree;
    }
}
