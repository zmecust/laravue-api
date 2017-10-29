<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function index()
    {
        return $this->responseSuccess('OK', Auth::user()->notifications);
    }

    public function read()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return $this->responseSuccess('OK');
    }
}
