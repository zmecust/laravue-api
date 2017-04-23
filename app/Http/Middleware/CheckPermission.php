<?php

namespace App\Http\Middleware;

use App\Permission;
use Closure;
use Route;
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
