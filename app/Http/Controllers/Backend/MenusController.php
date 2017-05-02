<?php

namespace App\Http\Controllers\Backend;

use Entrust;
use Cache;
use Validator;
use Illuminate\Http\Request;
use App\Repositories\MenuRepository;
use App\Http\Controllers\Api\ApiController;

class MenusController extends ApiController
{
    /**
     * @var MenuRepository
     */
    protected $menuRepository;

    /**
     * MenusController constructor.
     * @param MenuRepository $menuRepository
     */
    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    public function getSidebarTree(Request $request)
    {
        return $this->responseSuccess('OK', array_values($this->menuRepository->getSidebarMenu($request)));
    }

    public function getParentMenu()
    {
        return $this->responseSuccess('OK', $this->menuRepository->getParentMenu());
    }

    public function getChildrenMenu(Request $request)
    {
        return $this->responseSuccess('OK', $this->menuRepository->getChildrenMenu($request->get('parent_id')));
    }

    public function index()
    {
        if (empty($menus = Cache::get('all_menus'))) {
            $menus = array_values($this->menuRepository->getAllMenu());
        }

        return $this->responseSuccess('OK', $menus);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'display_name' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseError('validation.failed', $validator->errors()->toArray());
        }

        if (empty($request->get('parent_id_1'))) {
            $parent_id = 0;
        } else {
            if (empty($request->get('parent_id_2'))) {
                $parent_id = $request->get('parent_id_1');
            } else {
                $parent_id = $request->get('parent_id_2');
            }
        }

        if ($this->menuRepository->createMenu($parent_id, $request)) {
            return $this->responseSuccess('OK');
        }
    }

    public function show($id)
    {
        $menu = $this->menuRepository->getMenu($id);
        return $this->responseSuccess('OK', $menu);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'display_name' => 'required|max:10',
        ]);

        if ($validator->fails()) {
            return $this->responseError('validation.failed', $validator->errors()->toArray());
        }

        if ($this->menuRepository->updateMenu($id, $request)) {
            return $this->responseSuccess('OK');
        }
    }

    public function destroy($id)
    {
        $this->menuRepository->delMenu($id);
        $this->menuRepository->setMenuAllCache();

        return $this->responseSuccess('OK');
    }
}