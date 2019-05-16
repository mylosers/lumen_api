<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class LoginMiddleware
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
        $token=$_GET['token'];
        $id=$_GET['id'];
        $key='login_token;id:'.$id;
        $token_id=Redis::get($key);
        if($token_id!=$token){
            $res = [
                'error' => 50005,
                'msg' => '请先登陆'
            ];
            die(json_encode($res, JSON_UNESCAPED_UNICODE));
        }else{

        }
        return $next($request);
    }
}
