<?php

namespace App\Http\Controllers\Api;

use App\Role;
use App\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SidebarTreeController extends Controller
{
    public function getSidebarTree()
    {
        $sidebarTrees = Role::where('name', request('role'))->first()->perms()->get();
        $data = collect($sidebarTrees)->map(function($sidebarTree){
            if ($sidebarTree->parent_id) {

            }
            if ($sidebarTree->parent_id !== null) {
                $data = Permission::where('id', $sidebarTree->parent_id)->first();
                return array_merge($data->toArray(), ['children' => $sidebarTree->toArray()]);
            }
        });
        dd($data->toArray());
        //return $this->responseSuccess('OK', $sidebarTree);
    }

    public $data = array();
    public $cateArray = array();
    public $res = array();
    function Tree()
    {

    }

    function setNode ($id, $parent, $value)
    {
        $parent = $parent ? $parent : 0;
        $this->data[$id] = $value;
        //print_r($this->data);
        //echo "\r\n";
        $this->cateArray[$id] = $parent; //节点数组
        //print_r($this->cateArray);
    }

    function getChildsTree($id=0)
    {
        $childs = array();
        foreach ($this->cateArray as $child => $parent)
        {
            if ($parent == $id)
            {
                $childs[$child] = $this->getChildsTree($child);
            }
        }
        print_r($childs)."/r/n";
        return $childs;
    }

    function getParentsTree($id = 0)
    {
        $parents = array();
        foreach ($this->cateArray as $child => $parent)
        {
            if ($child == $id)
            {
                $parents[$parent] = $this->getParentsTree($parent);
            }
        }
        print_r($parents)."/r/n";
        return $parents;
    }

    function getChilds($id=0)
    {
        $childArray = array();
        $childs = $this->getChild($id);
        foreach ($childs as $child)
        {
            $childArray[] = $child;
            $childArray = array_merge($childArray, $this->getChilds($child));
        }
        return $childArray;
    }

    function getChild($id)
    {
        $childs = array();
        foreach ($this->cateArray as $child => $parent)
        {
            if ($parent == $id)
            {
                $childs[$child] = $child;
            }
        }
        return $childs;
    }

    function getParents($id)
    {
        $parentArray = array();
        $parents = $this->getParent($id);
        foreach ($parents as $parent)
        {
            $parentArray[] = $parent;
            $parentArray = array_merge($parentArray, $this->getParents($parent));
        }
        return $parentArray;
    }

    function getParent($id)
    {
        $parents = array();
        foreach ($this->cateArray as $child => $parent)
        {
            if ($child == $id)
            {
                $parents[$parent] = $parent;
            }
        }
        return $parents;
    }

    //单线获取父节点
    function getNodeLever($id)
    {
        $parents = array();
        if (key_exists($this->cateArray[$id], $this->cateArray))
        {
            $parents[] = $this->cateArray[$id];
            $parents = array_merge($parents,$this->getNodeLever($this->cateArray[$id]));
        }
        return $parents;
    }

    function getLayer($id, $preStr = '|-')
    {
        return str_repeat($preStr, count($this->getNodeLever($id)));
    }

    function getValue($id)
    {
        return $this->data[$id];
    } // end func

    //获取所有节点数据生成树
    function getAll($id = 0, $str = "|-")
    {
        if($id!=0) {
            $str = $str."|-";
        }
        //遍历所有数组检查parent是否有id
        foreach($this->cateArray as $child => $parent){
            //检查是否有相等的ID
            if($parent == $id){
                $this->res[$child] = $str.$this->getValue($child);
                $this->getAll($child,$str);
            }
            //$this->res[$child]=$child.$str.$this->getValue($child);
        }
    }
}
