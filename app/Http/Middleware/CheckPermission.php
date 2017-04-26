<?php

namespace App\Http\Middleware;

use App\Permission;
use App\User;
use Closure;
use Route;
use Cache;
use Entrust;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $uri_name = Route::currentRouteName();

        $permission_info = Permission::where('name', $uri_name)->first();
        //如果查不到路由名对应的权限则直接放行

        if (empty($permission_info)) {
            return $next($request);
        }  //检查是否有权限

        $data= explode(' ', $request->header('Authorization'))[1];
        $user_id = Cache::get('CMS'.$data);

        $role_id = User::where('user_id', $user_id);

        if (!Entrust::can(Entrust::can($uri_name))) {

            return response()->json([
                'permission' => 0,
                'message' => 'U have no permission',
                'data' => null
            ]);
        }  //根据路由名称查询权限

        return $next($request);
    }
}
