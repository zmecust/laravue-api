<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{

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
    public function responseError($message, array $data = NULL)
    {
        return $this->setStatus(0)->response($message, $data);
    }

    /**
     * @param $message
     * @param array|NULL $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseSuccess($message, array $data = NULL)
    {
        return $this->response($message, $data);
    }

    /**
     * @param $message
     * @param array|NULL $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function response($message, array $data = NULL)
    {
        return response()->json([
            'status' => $this->getStatus(),
            'message' => $message,
            'data' => $data,
        ]);
    }
}
