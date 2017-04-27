<?php

namespace App\Http\Middleware;

use Closure;

class CheckLogin
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
        if(empty($access_token = $request->header('authorization'))) {

            return response()->json([
                'status' => 0,
                'message' => '请提供正确的access_token',
                'data' => null
            ]);
        } elseif (empty(Cache::get('CMS'.$access_token))) {

            return response()->json([
                'status' => 0,
                'message' => 'access_token过期',
                'data' => null
            ]);
        }
        return $next($request);
    }
}
