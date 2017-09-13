<?php

namespace App\Http\Controllers\Admin;

use Validator;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PermissionsController extends Controller
{
    /**
     * @var PermissionRepository
     */
    protected $permissionRepository;

    /**
     * PermissionsController constructor.
     * @param PermissionRepository $permissionRepository
     */
    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $permissions = $this->permissionRepository->getPermissionList($request);
        return $this->responseSuccess('OK', $permissions->toArray());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function groupPermissions()
    {
        $group_permissions = $this->permissionRepository->groupPermissions();
        return $this->responseSuccess('OK', $group_permissions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $permission = $this->permissionRepository->createPermission($request);
        return $this->responseSuccess('创建成功', $permission);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $permission  = $this->permissionRepository->getPermission($id);
        return $this->responseSuccess('OK', $permission);
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
        $permission = $this->permissionRepository->updatePermission($request, $id);
        return $this->responseSuccess('修改成功', $permission);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->permissionRepository->deletePermission($id)) {
            return $this->responseSuccess('删除成功');
        };

        return $this->responseError('删除失败');
    }
}
