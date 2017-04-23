<?php

namespace App\Http\Controllers\Backend;

use Entrust;
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

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function GetSidebarTree()
    {
        return $this->responseSuccess('OK', array_values($this->menuRepository->getMenu()));
    }
}