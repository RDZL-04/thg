<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AlloHelperController extends Controller
{
    private $headers;
    private $tc_headers;
    private $url_api;

    public function __construct() {
        // Set header for API Request
        $this->headers = ['x-api-key' => env('API_KEY')];
        $this->tc_headers = ['Authorization' => env('TC_AUTH')];

        // set API URL dari APP_ENV
        if(env('APP_ENV') == 'local'){
            $this->url_api = env('API_URL_LOCAL');
        } elseif(env('APP_ENV') == 'dev') {
            $this->url_api = env('API_URL_DEV');
        } elseif(env('APP_ENV') == 'prod') {
            $this->url_api = env('API_URL_PROD');
        }
    }
    
    /**
     * Function: Get Allo APP_ID and KEY dan Get Allo URL AUTH PAGE
     * body: system_type = allo ; system_cd = appIDWeb / appIDMobile => AppId and appSecret
     * body: system_type = mpc_url ; system_cd = auth_page => Auth Page
     * $request	: search
    */
    public function get_allo_value(Request $request)
    {
        try {
            $client = new Client(); //GuzzleHttp\Client
            $response = $client->request('GET', $this->url_api.'/msytem/get_system_type_cd?system_type='.$request->system_type.'&system_cd='.$request->system_cd,[
                'verify' => false,
                'headers' => $this->headers,
                '/delay/5',
                ['connect_timeout' => 3.14]
            ]);
            
            $body = $response->getBody();
            $response = json_decode($body, true);
            return $response;
        } catch (\Throwable $e) {
            report($e);
            $response = [
                'status' => false,
                'message' => $response['message'],
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Generate Transaction No
     * body: 
     * $request	: search
    */
    public function create_transaction_no()
    {
        try {
            $date = date('ymd');
            $middleNo = 'ARKTHG' . mt_rand(100000000000,999999999999) . mt_rand(10000000,99999999);;
            $data = $date . $middleNo;
            return $data;
        } catch (\Throwable $e) {
            report($e);
            return $e;
        }
    }

    /**
     * Function: HIT ALLO MPC API, common function
     * body: 
     * $request	: search
    */
    public function mpc_hit_api(Request $request)
    {
        try {
            $dataHeader = $request->dataHeader;
            if (gettype($dataHeader) === 'object') {
                $tmpHeader = (array) $dataHeader;
                array_walk($tmpHeader, function(&$a, $b) { $a = "$b: $a"; });
                $dataHeader = array_values($tmpHeader);
            }

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $request->mpc_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($request->dataBody),
                CURLOPT_HTTPHEADER => (array)$dataHeader,
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            return json_decode($response, 1);

        } catch (\Throwable $e) {
            report($e);
            $response = [
                'status' => false,
                'message' => $e->getMessage(),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

}
