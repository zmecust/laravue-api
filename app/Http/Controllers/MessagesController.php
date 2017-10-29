<?php

namespace App\Http\Controllers;

use App\Message;
use App\Notifications\MessageNotification;
use Illuminate\Http\Request;
use Auth;

class MessagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    //保存私信内容
    public function store()
    {
        $toUserId = request('user_id');
        $fromUserId = Auth::id();
        $message = Message::create([
            'to_uid' => $toUserId,
            'from_uid' => $fromUserId,
            'content' => request('content'),
            'dialog_id' => $fromUserId . $toUserId,
        ]);
        $message->toUser->notify(new MessageNotification($message));

        if ($message) {
            return $this->responseSuccess('发送成功');
        }
        return $this->responseError('发送失败');
    }

    public function count()
    {
        $user = Auth::user();
        return $this->responseSuccess('OK', ['count' => $user->unreadNotifications->count()]);
    }
}
