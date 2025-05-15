<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Msystem;

class LogRoute
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
        //return $next($request);
		$response = $next($request);
		
		if (app()->environment('local')) {
			
			$config = Msystem::where('system_type', 'system_logs')->where('system_cd', 'logs_enabled')->first();
			
			if($config->system_value == 'true') {
				$log = [
					'DATETIME' => date('Y-m-d H:i:s'),
					'URI' => $request->getUri(),
					'METHOD' => $request->getMethod(),
					'REQUEST_BODY' => $request->all(),
					'RESPONSE' => $response->getContent()
				];

				Log::info(json_encode($log));
			}
        }

        return $response;
    }
}
