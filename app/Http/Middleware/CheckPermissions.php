<?php

namespace App\Http\Middleware;

use Route;
use Cache;
use Closure;
use Entrust;
use App\Models\User;
use App\Models\Permission;

class CheckPermissions
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
        $uri_name = Route::currentRouteName(); //后端路由必须全部命名，否则此处为null
        $permission_info = Permission::where('name', $uri_name)->first();
        //如果查不到路由名对应的权限则直接放行

        if (empty($permission_info)) {
            return $next($request);
        }  //检查是否有权限

        $access_token = $request->header('authorization');
        $user_id = Cache::get('CMS'.$access_token);
        $permissions = User::where('id', $user_id)->first()
            ->roles()->first()
            ->perms()->pluck('name')->toArray(); //获取当前用户所有权限名

        if (!in_array($uri_name, $permissions)) {
            return response()->json([
                'status' => 0,
                'message' => 'U have no permission',
                'data' => null
            ]);
        }  //根据路由名称查询权限

        return $next($request);
    }
}