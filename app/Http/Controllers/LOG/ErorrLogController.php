<?php

namespace App\Http\Controllers\LOG;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ErorrLog;
/*
|--------------------------------------------------------------------------
| Errorr Log Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will saved log error
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: february 19 2021
*/
class ErorrLogController extends Controller
{
    public function error_log($request){
        $ip='::1';
       if(empty($request['ip_address'])){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        // return $ip;
                        
                    }
                }
            }
        }
        // dd($request);
        $data = ['ip_address' => $ip]+$request;
        $save = ErorrLog::save_log($data);
        }
        else{
            $save = ErorrLog::save_log($request);
        }
        return;
    }

    public function error_log_api(Request $request){
        $data = json_decode($request->getContent(), true);
        // dd(count($data['error_log']));
         $save = ErorrLog::save_log($data);
         if($save){
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_saved_success' ),
                'data' => $save
            ];
         }else{
            foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
                if (array_key_exists($key, $_SERVER) === true){
                    foreach (explode(',', $_SERVER[$key]) as $ip){
                        $ip = trim($ip); // just to be safe
                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                            // return $ip;
                            
                        }
                    }
                }
            }
            $dataError = [
                'ipAddress' => $ip,
                'userName' => 'thg-web-app',
                'modul' => 'thg-web-app',
                'actions' => 'thg-web-app',
                'errorLog' => __('message.internal_erorr' ),
                'device' => '0',
            ];
             $save = ErrorLog::save_log($dataError);
            $response = [
                'status' => false,
                'code' => 400,
                'message' => __('message.internal_erorr' ),
                'data' => null
            ];
         }
         
        return response()->json($response, 200);
     }
}
