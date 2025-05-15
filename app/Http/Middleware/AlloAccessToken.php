<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AlloAccessToken
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    if (
      !$request->hasHeader('accessToken') &&
      !$request->hasHeader('refreshToken')
    ) return response()->json([
      'status' => false,
      'code' => 500,
      'message' => 'Access token or refresh token is required',
      'data' => $request->all(),
    ], 200);

    return $next($request);
  }
}
