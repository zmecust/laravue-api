<?php
/**
 * Created by PhpStorm.
 * User: zm
 * Date: 2017/4/19
 * Time: 13:14
 */
namespace App\Http\Controllers\Admin;

use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RolesController extends Controller
{
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * RolesController constructor.
     * @param RoleRepository $roleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $roles = $this->roleRepository->getRoleList($request);
        return $this->responseSuccess('OK', $roles->toArray());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role = $this->roleRepository->createRole($request);
        return $this->responseSuccess('OK', $role);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = $this->roleRepository->getRole($id);
        return $this->responseSuccess('OK', $role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = $this->roleRepository->updateRole($request, $id);
        return $this->responseSuccess('修改成功', $role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->roleRepository->deleteRole($id)) {
            return $this->responseSuccess('删除成功');
        };

        return $this->responseError('删除失败');
    }
}
