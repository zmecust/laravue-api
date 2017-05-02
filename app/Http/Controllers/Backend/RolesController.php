<?php

namespace App\Http\Controllers\Backend;

use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;

class RolesController extends ApiController
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
        return $this->responseSuccess('OK', $role);
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
            return $this->responseSuccess('OK');
        };

        return $this->responseError('Error');
    }
}
