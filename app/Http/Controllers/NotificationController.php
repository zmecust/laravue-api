<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class NotificationController extends Controller
{
    /**
     * NotificationController constructor.
     */
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->responseSuccess('OK', Auth::user()->notifications);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function read()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return $this->responseSuccess('OK');
    }
}
