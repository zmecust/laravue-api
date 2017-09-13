<?php
/**
 * Created by PhpStorm.
 * User: zm
 * Date: 2017/4/19
 * Time: 18:14
 */
namespace App\Http\Controllers\Admin;

use Cache;
use Illuminate\Http\Request;
use App\Repositories\MenuRepository;
use App\Http\Controllers\Controller;

class MenusController extends Controller
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSidebarTree(Request $request)
    {
        return $this->responseSuccess('OK', array_values($this->menuRepository->getSidebarMenu($request)));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getParentMenu(Request $request)
    {
        if (empty($parent_id = $request->get('parent_id'))) {
            $parent_id = 0;
        }
        return $this->responseSuccess('OK', $this->menuRepository->getParentMenu($parent_id));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (empty($menus = Cache::get('all_menus'))) {
            $menus = array_values($this->menuRepository->getAllMenu());
        }
        return $this->responseSuccess('OK', $menus);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        if (empty($parent_id = $request->get('parent_id'))) {
            $parent_id = 0;
        }

        if ($this->menuRepository->createMenu($parent_id, $request)) {
            return $this->responseSuccess('创建成功');
        }
        return $this->responseError('创建失败');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $menu = $this->menuRepository->getMenu($id);
        return $this->responseSuccess('OK', $menu);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        if ($this->menuRepository->updateMenu($request, $id)) {
            return $this->responseSuccess('修改成功');
        }
        return $this->responseError('修改失败');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        if ($this->menuRepository->delMenu($id)) {
            $this->menuRepository->setMenuAllCache();
            return $this->responseSuccess('删除成功');
        };
        return $this->responseError('删除失败');
    }
}