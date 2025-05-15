<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Response;
use App\Models\Application;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LOG\ErorrLogController;

class apikeyvalidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function __construct() {
        $this->LogController = new ErorrLogController;
    }
    public function handle(Request $request, Closure $next)
    {
        $user_ip_address=$request->ip(); 
        if(!isset($_SERVER['HTTP_X_API_KEY'])){  
            $error = ['modul' => 'api key validation',
                'actions' => 'API Key authorization',
                'error_log' => __('message.api_key'),
                'device' => "0",
                'ip_address' => $user_ip_address ];
            $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.api_key'),
            ];
            return response()->json($response, 404);
        }   
        $api = $_SERVER['HTTP_X_API_KEY'];
        
        // dd($user_ip_address);
        $header= Application::cek_api_key($api);
        if($header==null){
            $error = ['modul' => 'api key validation',
                'actions' => 'API Key authorization',
                'error_log' => __('message.api_key'),
                'device' => "0",
                'ip_address' => $user_ip_address ];
            $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.api_key'),
            ];
            return response()->json($response, 404);
        }
            $result = $header[0];
        if($result->ip_address_whitelist != '*'){
            if($result->ip_address_whitelist != $user_ip_address){
                $error = ['modul' => 'api key validation',
                'actions' => 'API Key authorization',
                'error_log' => __('message.api_key'),
                'device' => "0",
                'ip_address' => $user_ip_address ];
                $report = $this->LogController->error_log($error);
                $response = [
                    'status' => false,
                    'message' => __('message.api_key'),
                ];
                return response()->json($response, 404);
            }
            return $next($request);
        }
        return $next($request);
    }
}
