<?php

namespace App\Http\Controllers\Backend;

use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PermissionsController extends Controller
{
    protected $permissionRepository;

    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function index(Request $request)
    {
        $permissions = $this->permissionRepository->getPermissionList($request);
        return $this->responseSuccess('OK', $permissions->toArray());
    }

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
        /*if ($this->permissionRepository->createPermission($request)) {
            return $this->responseSuccess('OK');
        }*/
        $permission = $this->permissionRepository->createPermission($request);
        return $this->responseSuccess('OK', $permission);
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
        return $this->responseSuccess('OK', $permission);
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
            return $this->responseSuccess('OK');
        };

        return $this->responseError('Error');
    }
}
