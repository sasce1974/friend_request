<?php

namespace App\Http\Middleware;

use Closure;
use \Illuminate\Support\Facades\Auth;
class AuthBasic
{
    /**
     * Handle an incoming request.
     *
     * Please comment the Auth::onceBasic closure to avoid this middleware
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     */
    public function handle($request, Closure $next)
    {
        if(Auth::onceBasic()){
            return response()->json(["message"=>"Auth Failed!"], 401);
        }else{
            return $next($request);
        }

    }
}
