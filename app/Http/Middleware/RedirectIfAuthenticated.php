<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
            /*
            * response json
            *bila d aktifkan saat web belum login keluanrnya json
            */

            else{
                $response = [
                'success'   => false,
                'code' => 400,
                'message' =>  __('message.invalid_token'),
                'data'     => null,
            ];
            return response()->json($response, 200);
            }
        }

        return $next($request);
    }
}
