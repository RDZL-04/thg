<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Response;
use App\Models\Application;
use Illuminate\Support\Facades\DB;

class SessionValidation
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
        
        if($request->session()->has('id')){
            
            return $next($request);
        }
        else{
            return redirect()->route('login_user');
        }
    }
}
