<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /** 以下为出错处理
     * @var int
     */
    protected $status = 1;

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param $message
     * @param array|NULL $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseError($message, $data = NULL)
    {
        return $this->setStatus(0)->response($message, $data);
    }

    /**
     * @param $message
     * @param array|NULL $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseSuccess($message, $data = NULL)
    {
        return $this->response($message, $data);
    }

    /**
     * @param $message
     * @param array|NULL $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function response($message, $data = NULL)
    {
        return response()->json([
            'status' => $this->getStatus(),
            'message' => $message,
            'data' => $data,
        ]);
    }
}
