<?php

namespace App\Http\Controllers;

use App\Notifications\FollowUserNotification;
use App\User;
use Auth;
use Illuminate\Http\Request;

class FollowsController extends Controller
{
    /**
     * FollowsController constructor.
     */
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function isFollow(Request $request)
    {
        $user = Auth::user();
        $followers = $user->followers()->pluck('followed_id')->toArray();
        if (in_array($request->get('id'), $followers)) {
            return $this->responseSuccess('OK', ['followed' => true]);
        }
        return $this->responseSuccess('OK', ['followed' => false]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function followThisUser(Request $request)
    {
        $user = Auth::user();
        $userToFollow = User::find($request->get('id'));
        $followed = $user->followThisUser($userToFollow->id);

        if ( count($followed['attached']) > 0 ) {
            $user->increment('followings_count');
            $userToFollow->increment('followers_count');
            $userToFollow->notify(new FollowUserNotification(['name' => $user->name, 'user_id' => $userToFollow->id]));
            return $this->responseSuccess('OK', ['followed' => true]);
        }
        $user->decrement('followings_count');
        $userToFollow->decrement('followers_count');
        return $this->responseSuccess('OK', ['followed' => false]);
    }
}
