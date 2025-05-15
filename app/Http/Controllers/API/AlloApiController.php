<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use \Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Helpers\AlloHelperController;
use App\Http\Controllers\Helpers\EncryptHelper;
use App\Http\Controllers\API\MemberController;
use App\Http\Controllers\LOG\ErorrLogController;
use Illuminate\Support\Facades\Redis;
use App\Models\Msystem;

/*
|--------------------------------------------------------------------------
| AlloApi Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process all ALLO API MPC
| 
| @author: rangga.muharam@arkamaya.co.id 
| @update: 4 Maret 2021
*/

class AlloApiController extends Controller
{
    private $url_redirect;
    private $url_redirect_mobile;
    private $AlloHelperController;
    private $EncryptHelper;
    private $MemberController;
    private $LogController;

    // Construct
    public function __construct()
    {
        // set API URL dari APP_ENV
        if (env('APP_ENV') == 'local') {
            $this->url_redirect = env('APP_REDIRECT_URL');
            $this->url_redirect_mobile = env('APP_URL');
        } elseif (env('APP_ENV') == 'dev') {
            $this->url_redirect = env('APP_REDIRECT_URL_DEV');
            $this->url_redirect_mobile = env('APP_URL_DEV');
        } elseif (env('APP_ENV') == 'prod') {
            $this->url_redirect = env('APP_REDIRECT_URL_PROD');
            $this->url_redirect_mobile = env('APP_URL_PROD');
        }
        $this->AlloHelperController = new AlloHelperController;
        $this->EncryptHelper = new EncryptHelper;
        $this->MemberController = new MemberController;
        $this->LogController = new ErorrLogController;
    }

    /**
     * Function: Create Code Challenge
     * body: 
     *	$request	: 
     */
    public function create_code_challenge(Request $request)
    {
        try {
            $verifier = $this->EncryptHelper->create_verifier();
            $codeChallenge = $this->EncryptHelper->hashingVerifier($verifier);
            $response = [
                'status' => true,
                'message' => 'Successfull',
                'code' => 200,
                'data' => $codeChallenge,
            ];
            return response()->json($response, 200);
        } catch (\Throwable $e) {
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: TC Header
     * body: 
     *	$request	: 
     */
    public function create_header_allo(Request $request)
    {
        try {
            $system_cd = 'appIDWeb';
            $system_cd_app_secret = 'appSecretWeb';

            // Cek jika osType ada
            if (!empty($request->requestData['osType'])) {
                $system_cd = $request->requestData['osType'] === 'WEB' ? 'appIDWeb' : 'appIDMobile';
                $system_cd_app_secret = $request->requestData['osType'] === 'WEB' ? 'appSecretWeb' : 'appSecretMobile';
            }

            $body = $request->all();

            $getAppId = Msystem::where('system_type', '=', 'allo')
                ->where('system_cd', '=', $system_cd)
                ->first('system_value');

            $getAppSecret = Msystem::where('system_type', '=', 'allo')
                ->where('system_cd', '=', $system_cd_app_secret)
                ->first('system_value');

            $now = round(microtime(true) * 1000);
            $appId = $getAppId->system_value;
            $appSecret = $getAppSecret->system_value;
            $nonce = strval(floor($this->EncryptHelper->random_0_1() * 100000000));

            // $arr = [$header['appId'], $header['nonce'], $header['timestamp'], $response_get_app_secret['data'][0]['system_value'], $body_str];
            $arr = [
                $appId,
                $nonce,
                $now,
                $appSecret,
                json_encode($body)
            ];

            // Sorting sesuai ASCII
            asort($arr, 2);
            // Concat array
            $data = join('', $arr);

            // Create Hashing sha256 dan convert Hex to Bin
            $hashDatas = $this->EncryptHelper->hashing($data);
            $strDatas = $this->EncryptHelper->to_str($hashDatas);

            // Load Private Key file
            $path = storage_path('app/key/private.key');
            $kh = openssl_pkey_get_private(file_get_contents($path));

            // Encrypt Object Data using private key
            $encrypted = openssl_private_encrypt($strDatas, $crypttext, $kh);
            if (!$encrypted)
                throw new \Exception('Unsuccessfull', 200);

            // add sign key to Header array
            $sign = $this->EncryptHelper->to_hex($crypttext);

            $response = [
                'status' => true,
                'message' => 'Successfull Encrypted',
                'code' => 200,
                'data' => [
                    'Content-Type' => 'application/json',
                    'appId' => $appId,
                    'nonce' => $nonce,
                    'sign' => $sign,
                    'timestamp' => $now,
                ]
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'code' => $e->getCode() ?? 500,
                'data' => null,
            ], 500);
        }
    }

    /**
     * Function: Decrypt Header
     * body: 
     *	$request	: 
     */
    public function decrypt_header(Request $request)
    {
        try {
            // Get Public Key
            $path = storage_path('app/key/public.crt');
            $kh = openssl_pkey_get_public(file_get_contents($path));

            // Convert Hex to Str
            $data = $this->EncryptHelper->to_str($request->requestData['sign']);

            // Decrypt data buffer with Public Key
            $decrypted = openssl_public_decrypt($data, $decryptedData, $kh);

            if ($decrypted)
                $response = [
                    'status' => true,
                    'message' => 'Successfull Decrypted',
                    'code' => 200,
                    'data' => $this->EncryptHelper->to_hex($decryptedData),
                ];

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Helper untuk Construct new accessToken dan refreshToken
     * body: 
     *	$request	: 
     */
    public function refresh_token_helper($request)
    {
        try {
            // Construct dataBody untuk Hit api Refresh Token
            $dataBodyRefreshToken = [
                'accessToken' => $request['accessToken'],
                'refreshToken' => $request['refreshToken']
            ];
            $request_refresh_token = new Request();
            $request_refresh_token->merge($dataBodyRefreshToken);
            //HIT Allo API Resfresh Token utk mendapatkan Pair accessToken dan refreshToken yg baru
            // $response_refresh_token = (array)json_decode($this->refresh_token($request_refresh_token)->getContent());
            return (array)json_decode($this->refresh_token($request_refresh_token)->getContent());
        } catch (\Throwable $e) {
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Helper untuk Generate Factor, dimana nanti Response Factor 
     *           dipakai untuk kebutuhan Change Password
     *           Password = RSAEncryptWithPublicKey(passwordInputByUser + '\t' + factor)
     * body: 
     *	$request	: 
     */
    public function generate_factor_helper(Request $request)
    {
        try {
            $trxNo = $this->AlloHelperController->create_transaction_no();
            // Check Request Validation
            $rules = [
                'phoneNo' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }
            // Construct dataBody untuk Hit Generate Factor
            $dataBody = [
                'transactionNo' => $trxNo,
                'requestData' => [
                    'phoneNo' => $request['phoneNo']
                ]
            ];
            // create Request
            $request_header = new Request();
            $request_header->merge($dataBody);
            // Call fungsi utk create Header
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());
            if ($response_create_header->status) {
                $data_header = $response_create_header->data;
                // Get url MPC Generate Factor URL
                // create Request
                $req_url_mpc = new Request();
                $req_url_mpc->merge(['system_type' => 'mpc_url', 'system_cd' => 'generate_factor_v2']);
                $res_url_mpc = $this->AlloHelperController->get_allo_value($req_url_mpc);
                if ($res_url_mpc['status']) {
                    // Cek jika data tidak ada di db
                    if (count($res_url_mpc['data']) > 0) {
                        $url_mpc = $res_url_mpc['data'][0]['system_value'];
                        // HIT MPC API AUTH PAGE
                        // create Request
                        $req_hit_allo = new Request();
                        $req_hit_allo->merge(['mpc_url' => $url_mpc, 'dataHeader' => $data_header, 'dataBody' => $dataBody]);
                        $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
                        if ($resp_hit_allo['message'] == 'Success') {
                            $response = [
                                'status' => true,
                                'message' => $resp_hit_allo['message'],
                                'code' => 200,
                                'data' => $resp_hit_allo
                            ];
                        } else {
                            $error = [
                                'modul' => 'generate_factor_helper',
                                'actions' => 'Hit Allo MPC',
                                'error_log' => $resp_hit_allo,
                                'device' => "0"
                            ];
                            $report = $this->LogController->error_log($error);
                            $response = [
                                'status' => false,
                                'message' => $resp_hit_allo['message'],
                                'code' => 500,
                                'data' => $resp_hit_allo
                            ];
                        }
                    } else {
                        $response = [
                            'status' => false,
                            'message' => 'Data empty',
                            'code' => 500,
                            'data' => $res_url_mpc
                        ];
                    }
                } else {
                    $error = [
                        'modul' => 'generate_factor_helper',
                        'actions' => 'Hit db THG',
                        'error_log' => $res_url_mpc,
                        'device' => "0"
                    ];
                    $report = $this->LogController->error_log($error);
                    $response = [
                        'status' => false,
                        'message' => $res_url_mpc['message'],
                        'code' => 500,
                        'data' => $res_url_mpc
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 500,
                    'data' => $request_header
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Allo Auth-Page untuk Register atau Login
     * body: 
     * return url untuk nanti diload di webview atau location.href
     *	$request	: 
     */
    public function allo_auth_page(Request $request)
    {
        try {
            $authSession = $request->cookie('authSession');
            // return  response()->json($request->cookie('authSession'), 200);
            $trxNo = $this->AlloHelperController->create_transaction_no();
            $verifier = $this->EncryptHelper->create_verifier();
            $codeChallenge = $this->EncryptHelper->hashingVerifier($verifier);
            // Check Request Validation
            $rules = [
                'redirectPageType' => 'required',
                'deviceId' => 'required'
            ];
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }

            // Construct default body param - default utk WEB
            $dataBody = [
                'transactionNo' => $trxNo,
                'requestData' => [
                    'responseType' => 'CODE',
                    'codeChallengeMethod' => 'SHA256',
                    'codeChallenge' => $codeChallenge,
                    'authorizationPageType' => strtoupper($request->redirectPageType),
                    "osType" => $request->osType ?? "WEB",
                    "deviceId" => $request->deviceId,
                ]
            ];

            $res_url_mpc = Msystem::where('system_type', '=', 'mpc_url')
                ->where('system_cd', '=', 'auth_page_v2')
                ->first();

            if ($res_url_mpc === null) {
                $error = [
                    'modul' => 'allo_auth_page',
                    'actions' => 'Hit db THG',
                    'error_log' => $res_url_mpc,
                    'device' => "0"
                ];

                $this->LogController->error_log($error);

                $response = [
                    'status' => false,
                    'message' => "not found record",
                    'code' => 500,
                    'data' => $res_url_mpc
                ];

                return response()->json($response, 200);
            }

            // create Request
            $request_header = new Request();
            $request_header->merge($dataBody); // tdk bisa
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());

            if (!$response_create_header->status) {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 500,
                    'data' => $request_header
                ];
                return response()->json($response, 200);
            }

            $data_header = $response_create_header->data; // Nonce bisa diambil dari sini $data_header->nonce
            $tmpHeader = (array) $data_header;
            array_walk($tmpHeader, function (&$a, $b) {
                $a = "$b: $a";
            });
            $data_header = array_values($tmpHeader);

            // create Request
            $req_hit_allo = new Request();
            $req_hit_allo->merge([
                'mpc_url' => $res_url_mpc->system_value,
                'dataHeader' => $data_header,
                'dataBody' => $dataBody
            ]);

            $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
            // Cek response Success
            if ($resp_hit_allo['message'] == 'Success') {
                // passing PAGE key ke Front End
                $callback = Msystem::where('system_type', '=', 'callback')
                    ->where('system_cd', '=', 'allo_login')
                    ->first();
                $resp_hit_allo['url_page'] = $resp_hit_allo['responseData']['authorizationPageUri'] . '&callback=' . $callback->system_value . '&language=en';
                // passing code_verifier untuk nanti digunakan FrontEnd sebagai param HIT Request Token
                $resp_hit_allo['codeVerifier'] = $verifier;
                // Decode AuthorizationPageUri, untuk mengamnbil equipmentId (Web) atau osType dan deviceId (Mobile)
                $url_components = parse_url($resp_hit_allo['url_page']);
                parse_str($url_components['fragment'], $params);

                if (!empty($params['equipmentId'])) {

                    $resp_hit_allo['equipmentId'] = $params['equipmentId'];
                    $redis = Redis::connection();
                    $redis->set($authSession, json_encode([
                        'verifier' =>  $verifier,
                        'equipmentId' => $params['equipmentId'],
                        'deviceId' => $request->deviceId,
                        'redirectPageType' => strtoupper($request->redirectPageType)
                    ]));
                } elseif (!empty($params['osType'])) {

                    $resp_hit_allo['osType'] = $params['osType'];
                    $resp_hit_allo['deviceId'] = $params['deviceId'];
                    $redis = Redis::connection();
                    $redis->set($authSession, json_encode([
                        'verifier' =>  $verifier,
                        'osType' => $params['osType'],
                        'deviceId' => $params['deviceId'],
                        'redirectPageType' => strtoupper($request->redirectPageType)
                    ]));
                }

                $response = [
                    'status' => true,
                    'message' => $resp_hit_allo['message'],
                    'code' => 200,
                    'data' => $resp_hit_allo
                ];
            } else {
                $error = [
                    'modul' => 'allo_auth_page',
                    'actions' => 'Hit Allo MPC',
                    'error_log' => $resp_hit_allo,
                    'device' => "0"
                ];
                $report = $this->LogController->error_log($error);
                $response = [
                    'status' => false,
                    'message' => $resp_hit_allo['message'],
                    'code' => 500,
                    'data' => $resp_hit_allo
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'allo_auth_page',
                'actions' => 'Hit Allo Auth Page',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Allo_AUTH_TOKEN
     * body: idToken dari Login/Register CAS, equipmentId dari fungsi allo_auth_page() => WEB
     *       osType dan deviceId untuk Mobile Apps
     *	$request	: 
     */
    public function allo_auth_token(Request $request)
    {
        try {
            // Check Request Validation
            $rules = [
                'code' => 'required',
                'codeVerifier' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }
            // Construct default body param
            $dataBody = [
                "transactionNo" =>  $this->AlloHelperController->create_transaction_no(),
                "requestData" => [
                    "code" => $request['code'],
                    "codeVerifier" => $request['codeVerifier'],
                    "grantType" => "AUTHORIZATION_CODE"
                ]
            ];
            if (!empty($request->equipmentId)) {
                $rules = [
                    'equipmentId' => 'required'
                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    // return response gagal
                    $response = [
                        'status' => false,
                        'code' => 400,
                        'message' => $validator->errors()->first(),
                        'data' => $request->all(),

                    ];
                    return response()->json($response, 200);
                }
                // Ini dataBody utk Mobile, overrride yang default value
                $dataBody['requestData']['equipmentId'] = $request->equipmentId;
            } else if (!empty($request->osType)) {
                $rules = [
                    'osType' => 'required',
                    'deviceId' => 'required'
                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    // return response gagal
                    $response = [
                        'status' => false,
                        'code' => 400,
                        'message' => $validator->errors()->first(),
                        'data' => $request->all(),

                    ];
                    return response()->json($response, 200);
                }
                // Ini dataBody utk Mobile, overrride yang default value
                $dataBody['requestData']['osType'] = $request->osType;
                $dataBody['requestData']['deviceId'] = $request->deviceId;
            }

            // HIT Create_Header Function
            // create Request
            $request_header = new Request();
            $request_header->merge($dataBody);
            // Call fungsi utk create Header
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());

            if (!$response_create_header->status)
                return response()->json([
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 400,
                    'data' => $request_header
                ], 200);

            $data_header = $response_create_header->data;
            $res_url_mpc = json_decode(Msystem::where('system_type', '=', 'mpc_url')
                ->where('system_cd', '=', 'request_token_v2')
                ->first());

            if (!$res_url_mpc) {
                $this->LogController->error_log([
                    'modul' => 'allo_auth_token',
                    'actions' => 'Hit THG DB',
                    'error_log' => $res_url_mpc,
                    'device' => "0"
                ]);

                return response()->json([
                    'status' => false,
                    'message' => $res_url_mpc['message'],
                    'code' => 400,
                    'data' => $res_url_mpc
                ], 200);
            }

            // url auth token MPC
            $url_mpc = $res_url_mpc->system_value;
            // HIT MPC API AUTH PAGE
            // create Request
            $req_hit_allo = new Request();
            $req_hit_allo->merge(['mpc_url' => $url_mpc, 'dataHeader' => $data_header, 'dataBody' => $dataBody]);
            $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
            // Cek response success
            if (strtoupper($resp_hit_allo['message']) != 'SUCCESS') {
                $this->LogController->error_log([
                    'modul' => 'allo_auth_token',
                    'actions' => 'Hit Allo Auth Token API MPC',
                    'error_log' => json_encode($resp_hit_allo),
                    'device' => "0"
                ]);

                return response()->json([
                    'status' => false,
                    'message' => $resp_hit_allo['message'],
                    'code' => 400,
                    'data' => $resp_hit_allo
                ], 200);
            }

            // Get AccessToken lalu HIT Member Profile Query utk menyimpan data Member
            $accessToken =  $resp_hit_allo['responseData']['accessToken'];
            $refreshToken = $resp_hit_allo['responseData']['refreshToken'];
            $req_hit_profile = new Request();
            $req_hit_profile->merge(['accessToken' => $resp_hit_allo['responseData']['accessToken'], 'refreshToken' => $resp_hit_allo['responseData']['refreshToken'], 'deviceId' => $request->deviceId]);
            // Passing data into Func Member Profile Query, sudah sekalian di save ke table Member
            $responseMember = json_decode($this->allo_member_profile($req_hit_profile)->getContent());

            if (!$responseMember->status)
                return response()->json([
                    'status' => false,
                    'message' => $responseMember->message,
                    'code' => 400,
                    'data' => ''
                ], 200);

            $respAllo['responseAllo'] = (array)$responseMember->data->responseAllo;
            $resp_hit_allo['data'] = (array)$responseMember->data;
            $resp_hit_allo['data']['responseAllo'] = $respAllo['responseAllo'];

            // Get Coupon Instance List, passing ke res_hit_allo['coupon'] jumlah coupon
            // jumlah coupon dari responseData Allo di key page->totalCount
            $req_hit_coupon = new Request();
            $req_hit_coupon->merge(['accessToken' => $resp_hit_allo['data']['responseAllo']['accessToken'], 'refreshToken' => $resp_hit_allo['data']['responseAllo']['refreshToken'], 'status' => 'AVAILABLE']);
            $responseCoupon = json_decode($this->coupon_instance_list($req_hit_coupon)->getContent());

            if (!$responseCoupon->status)
                return response()->json([
                    'status' => false,
                    'message' => $responseCoupon->message,
                    'code' => 500,
                    'data' => $responseCoupon->data
                ], 200);

            // Construct coupon ke $resp_hit_allo
            $resp_hit_allo['data']['responseAllo']['coupon'] = $responseCoupon->data->responseData->page->totalCount;

            // Get Point Balance for user
            // jumlah coupon dari responseData Allo di key page->totalCount
            $req_hit_point = new Request();
            $req_hit_point->merge(['accessToken' => $resp_hit_allo['data']['responseAllo']['accessToken'], 'refreshToken' => $resp_hit_allo['data']['responseAllo']['refreshToken']]);
            $responsePoint = json_decode($this->allo_point_balance($req_hit_point)->getContent());

            if (!$responsePoint->status)
                return response()->json([
                    'status' => false,
                    'message' => $responsePoint->message,
                    'code' => 500,
                    'data' => $responsePoint->data
                ], 200);

            // Construct new accessToken, refreshToken dan pointBalance untuk $resp_hit_allo['data']['responseAllo']
            $resp_hit_allo['data']['responseAllo']['pointBalance'] = $responsePoint->data->responseData->balance;
            $resp_hit_allo['data']['responseAllo']['accessToken'] = $responsePoint->data->accessToken;
            $resp_hit_allo['data']['responseAllo']['refreshToken'] = $responsePoint->data->refreshToken;

            return response()->json([
                'status' => true,
                'message' => $resp_hit_allo['message'],
                'code' => 200,
                'data' => $resp_hit_allo['data']
            ], 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'allo_auth_token',
                'actions' => 'Hit Allo Auth Token API',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Refresh Token untuk mendapatkan accessToken yg baru
     * body: 
     *	$request	: 
     */
    public function refresh_token(Request $request)
    {
        try {
            $rules = [
                'accessToken' => 'required',
                'refreshToken' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 500,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }
            // Decode JWT token, utk check expired time
            $token = $request['accessToken'];
            $tokenParts = explode(".", $token);
            $tokenHeader = base64_decode($tokenParts[0]);
            $tokenPayload = base64_decode($tokenParts[1]);
            $jwtHeader = json_decode($tokenHeader);
            $jwtPayload = json_decode($tokenPayload);
            // dd($jwtPayload->exp);

            // Date Now
            $dateNow = time();
            if ($jwtPayload->exp > $dateNow) {
                $response = $request->all();
                // jangan refresh jika belum expired
                $response = [
                    'status' => true,
                    'message' => 'Token still valid',
                    'code' => 200,
                    'data' => $response
                ];
                return response()->json($response, 200);
            }

            // Construct dataBody
            $dataBody = [
                'requestData' => [
                    'refreshToken' => $request->refreshToken,
                    'grantType' => 'REFRESH_TOKEN'
                ]
            ];
            // get trxNo
            $trxNo = $this->AlloHelperController->create_transaction_no();
            $dataBody['transactionNo'] = $trxNo;
            // Construct Header untuk di passing ke Create_Header_Allo function, nanti response header with sign key
            $header = $dataBody;
            // create Request
            $request_header = new Request();
            $request_header->merge($header);
            // Call fungsi utk create Header
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());
            if ($response_create_header->status) {
                $data_header = $response_create_header->data;

                // Get url MPC Refresh_Token from table msystems
                // create Request
                // $req_url_mpc = new Request();
                // $req_url_mpc->merge(['system_type' => 'mpc_url' , 'system_cd' => 'refresh_token_v2']);
                // $res_url_mpc = $this->AlloHelperController->get_allo_value($req_url_mpc);
                $res_url_mpc = json_decode(Msystem::where('system_type', '=', 'mpc_url')
                    ->where('system_cd', '=', 'refresh_token_v2')
                    ->first());
                if ($res_url_mpc->id != null) {
                    // Cek jika data url tidak ada di db
                    //    if(count($res_url_mpc['data']) > 0){
                    $url_mpc = $res_url_mpc->system_value;
                    // HIT MPC API Refresh Token
                    // create Request
                    $req_hit_allo = new Request();
                    $req_hit_allo->merge(['mpc_url' => $url_mpc, 'dataHeader' => $data_header, 'dataBody' => $dataBody]);
                    $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
                    // Cek response success
                    if (strtoupper($resp_hit_allo['message']) == 'SUCCESS') {
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    } else {
                        $error = [
                            'modul' => 'refresh_token',
                            'actions' => 'Hit Allo Refresh Token API',
                            'error_log' => json_encode($resp_hit_allo),
                            'device' => "0"
                        ];
                        $report = $this->LogController->error_log($error);
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    }
                    //    } else {
                    //         $response = [
                    //             'status' => false,
                    //             'message' => 'Data empty',
                    //             'code' => 500,
                    //             'data' => $res_url_mpc 
                    //         ];
                    //    }
                } else {
                    $response = [
                        'status' => false,
                        'message' => "Not Found record",
                        'code' => 500,
                        'data' => $res_url_mpc
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 400,
                    'data' => $request_header
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'refresh_token',
                'actions' => 'Hit Allo Refresh Token API',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Fetch Member Allo Profile ALLO HIT API
     * body: 
     *	$request	: 
     */
    public function allo_member_profile(Request $request)
    {
        try {
            $rules = [
                'accessToken' => 'required',
                'refreshToken' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 500,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }

            // create new fresh accessToken dan refreshToken
            $response_refresh_token = $this->refresh_token_helper($request->all());
            //  dd($response_refresh_token);
            if ($response_refresh_token['status'] && strtoupper($response_refresh_token['message']) === 'SUCCESS') {
                $newAccessToken = $response_refresh_token['data']->responseData->accessToken;
                $newRefreshToken = $response_refresh_token['data']->responseData->refreshToken;
            } else {
                $newAccessToken = $request['accessToken'];
                $newRefreshToken = $request['refreshToken'];
            }

            // Construct dataBody untuk Hit api Member Profile
            $dataBody = [
                'requestData' => [
                    'accessToken' => $newAccessToken
                ]
            ];
            // Construct Header untuk di passing ke Create_Header_Allo function, nanti response header with sign key
            $header = $dataBody;
            // create Request
            $request_header = new Request();
            $request_header->merge($header);
            // Call fungsi utk create Header
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());
            if ($response_create_header->status) {
                $data_header = $response_create_header->data;

                // Get url MPC Member_Existence from table msystems
                // create Request
                // $req_url_mpc = new Request();
                // $req_url_mpc->merge(['system_type' => 'mpc_url' , 'system_cd' => 'member_profile_v2']);
                // $res_url_mpc = $this->AlloHelperController->get_allo_value($req_url_mpc);
                $res_url_mpc = json_decode(Msystem::where('system_type', '=', 'mpc_url')
                    ->where('system_cd', '=', 'member_profile_v2')
                    ->first());
                if ($res_url_mpc->id != null) {
                    // Cek jika data url tidak ada di db
                    $url_mpc = $res_url_mpc->system_value;
                    // HIT MPC API Member Existence
                    // create Request
                    $req_hit_allo = new Request();
                    $req_hit_allo->merge(['mpc_url' => $url_mpc, 'dataHeader' => $data_header, 'dataBody' => $dataBody]);
                    $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
                    // Cek response success
                    if (strtoupper($resp_hit_allo['message']) == 'SUCCESS') {
                        $resp_hit_allo = (array)$resp_hit_allo['responseData'];
                        // Check Phone Number untuk '0', karena response dari Allo tidak ada angka 0
                        if ($resp_hit_allo['phoneNo'] != '0') {
                            $resp_hit_allo['phoneNo'] = '0' . $resp_hit_allo['phoneNo'];
                        }
                        // Insert to db sesuai Mapping 
                        // create request untuk panggil fungsi save_member di MemberController

                        $req_save_member = new Request();
                        $req_save_member->merge(['full_name' => $resp_hit_allo['name'], 'phone' => $resp_hit_allo['phoneNo'], 'email' => $resp_hit_allo['email'], 'mdcid' => $resp_hit_allo['mdcId'], 'deviceId' => $request->deviceId]);
                        $res_save_member = (array)json_decode($this->MemberController->save_member($req_save_member)->getContent());

                        if ($res_save_member['status']) {
                            $resp_hit_allo['accessToken'] = $newAccessToken;
                            $resp_hit_allo['refreshToken'] = $newRefreshToken;
                            $res_save_member['data'] = $res_save_member['data'][0];
                            $res_save_member['data']->responseAllo = $resp_hit_allo;
                            $response = [
                                'status' => true,
                                'message' => $res_save_member['message'],
                                'code' => 200,
                                'data' => $res_save_member['data']
                            ];
                        } else {
                            $response = [
                                'status' => false,
                                'message' => $res_save_member['message'],
                                'code' => 400,
                                'data' => $res_save_member
                            ];
                        }
                    } else {
                        $error = [
                            'modul' => 'allo_member_profile',
                            'actions' => 'Hit Allo Member Profile API',
                            'error_log' => json_encode($resp_hit_allo),
                            'device' => "0"
                        ];
                        $report = $this->LogController->error_log($error);
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    }
                    //    } else {
                    //         $response = [
                    //             'status' => false,
                    //             'message' => 'Data empty',
                    //             'code' => 500,
                    //             'data' => $res_url_mpc 
                    //         ];
                    //    }
                } else {
                    $response = [
                        'status' => false,
                        'message' => "Data Empty",
                        'code' => 500,
                        'data' => $res_url_mpc
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 400,
                    'data' => $request_header
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'allo_member_profile',
                'actions' => 'Hit Allo Member Profile API',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Change Email Allo Member
     * body: 
     *	$request	: 
     */
    public function allo_member_change_email(Request $request)
    {
        try {
            $rules = [
                'accessToken' => 'required',
                'refreshToken' => 'required',
                'newEmail' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 500,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }

            // create new fresh accessToken dan refreshToken
            $response_refresh_token = $this->refresh_token_helper($request->all());
            //  dd($response_refresh_token);
            if ($response_refresh_token['status'] && strtoupper($response_refresh_token['message']) === 'SUCCESS') {
                $newAccessToken = $response_refresh_token['data']->responseData->accessToken;
                $newRefreshToken = $response_refresh_token['data']->responseData->refreshToken;
            } else {
                $newAccessToken = $request['accessToken'];
                $newRefreshToken = $request['refreshToken'];
            }

            // Construct dataBody untuk Hit api Member Profile
            $dataBody = [
                'requestData' => [
                    'accessToken' => $newAccessToken,
                    'newEmail' => $request['newEmail']
                ]
            ];
            // Construct Header untuk di passing ke Create_Header_Allo function, nanti response header with sign key
            $header = $dataBody;
            // create Request
            $request_header = new Request();
            $request_header->merge($header);
            // Call fungsi utk create Header
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());
            if ($response_create_header->status) {
                $data_header = $response_create_header->data;

                // Get url MPC Member_Existence from table msystems
                // create Request
                $req_url_mpc = new Request();
                $req_url_mpc->merge(['system_type' => 'mpc_url', 'system_cd' => 'change_email_v2']);
                $res_url_mpc = $this->AlloHelperController->get_allo_value($req_url_mpc);
                if ($res_url_mpc['status']) {
                    // Cek jika data url tidak ada di db
                    if (count($res_url_mpc['data']) > 0) {
                        $url_mpc = $res_url_mpc['data'][0]['system_value'];
                        // HIT MPC API Member Existence
                        // create Request
                        $req_hit_allo = new Request();
                        $req_hit_allo->merge(['mpc_url' => $url_mpc, 'dataHeader' => $data_header, 'dataBody' => $dataBody]);
                        $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
                        $resp_hit_allo['accessToken'] = $newAccessToken;
                        $resp_hit_allo['refreshToken'] = $newRefreshToken;
                        // Cek response success
                        if (strtoupper($resp_hit_allo['message']) == 'SUCCESS') {
                            $response = [
                                'status' => true,
                                'message' => $resp_hit_allo['message'],
                                'code' => 200,
                                'data' => $resp_hit_allo
                            ];
                        } else {
                            $error = [
                                'modul' => 'allo_member_change_email',
                                'actions' => 'Hit Allo Change Email API',
                                'error_log' => json_encode($resp_hit_allo),
                                'device' => "0"
                            ];
                            $report = $this->LogController->error_log($error);
                            $response = [
                                'status' => true,
                                'message' => $resp_hit_allo['message'],
                                'code' => 200,
                                'data' => $resp_hit_allo
                            ];
                        }
                    } else {
                        $response = [
                            'status' => false,
                            'message' => 'Data empty',
                            'code' => 500,
                            'data' => $res_url_mpc
                        ];
                    }
                } else {
                    $response = [
                        'status' => false,
                        'message' => $res_url_mpc['message'],
                        'code' => 500,
                        'data' => $res_url_mpc
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 400,
                    'data' => $request_header
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'allo_member_change_email',
                'actions' => 'Hit Allo Change Email API',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Change Password Allo Member
     * body: 
     *	$request	: 
     */
    public function allo_member_change_password(Request $request)
    {
        try {
            $rules = [
                'phoneNo' => 'required',
                'accessToken' => 'required',
                'refreshToken' => 'required',
                'oldPassword' => 'required',
                'newPassword' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 500,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }

            // create new fresh accessToken dan refreshToken
            $response_refresh_token = $this->refresh_token_helper($request->all());
            //  dd($response_refresh_token);
            if ($response_refresh_token['status'] && strtoupper($response_refresh_token['message']) === 'SUCCESS') {
                $newAccessToken = $response_refresh_token['data']->responseData->accessToken;
                $newRefreshToken = $response_refresh_token['data']->responseData->refreshToken;
            } else {
                $newAccessToken = $request['accessToken'];
                $newRefreshToken = $request['refreshToken'];
            }

            // Load Public Key
            $path = storage_path('app/key/public.crt');
            $kh = openssl_pkey_get_public(file_get_contents($path));
            //  $path = storage_path('app/key/private.key');
            //  $kh = openssl_pkey_get_private(file_get_contents($path));

            // oldPassword Section
            // create OldFactor lalu encrypt Password dengan public key dan Factor
            $data_oldFactor = ['phoneNo' => $request['phoneNo']];
            $request_factor = new Request();
            $request_factor->merge($data_oldFactor);
            $oldFactorResponse = json_decode($this->generate_factor_helper($request_factor)->getContent());
            if ($oldFactorResponse->status) {
                $oldFactor = $oldFactorResponse->data->responseData->factor;
            } else {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 500,
                    'message' => 'Cannot create Factor',
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }
            //  dd($oldFactor);
            // Rumus password = RSAEncryptWithPublicKey(passwordInputByUser + '\t' + factor)
            // Encrypt Object Data using public key
            $obj = $request['oldPassword'] . '\t' . $oldFactor;
            $encrypted = openssl_public_encrypt($obj, $crypttext, $kh, OPENSSL_PKCS1_PADDING);

            // Encrypt Object Data using private key
            //  $encrypted = openssl_private_encrypt($obj,$crypttext,$kh);      
            if ($encrypted) {
                // $oldPassword = $crypttext;
                $oldPassword = $this->EncryptHelper->to_hex($crypttext);
                //    $oldPassword = base64_encode($crypttext);
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Unsuccessfull Encrypt',
                    'code' => 500,
                    'data' => $request->all()
                ];
                return response()->json($response, 200);
            }
            // oldPassword Section

            // New Password Section
            // create OldFactor lalu encrypt Password dengan public key dan Factor
            $data_newFactor = ['phoneNo' => $request['phoneNo']];
            $request_factor = new Request();
            $request_factor->merge($data_newFactor);
            $newFactorResponse = json_decode($this->generate_factor_helper($request_factor)->getContent());
            if ($newFactorResponse->status) {
                $newFactor = $newFactorResponse->data->responseData->factor;
            } else {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 500,
                    'message' => 'Cannot create Factor',
                    'data' => $request->all()
                ];
                return response()->json($response, 200);
            }
            //  dd($oldFactor);
            // Rumus password = RSAEncryptWithPublicKey(passwordInputByUser + '\t' + factor)
            // Encrypt Object Data using public key
            $obj = $request['newPassword'] . '\t' . $newFactor;
            $encrypted = openssl_public_encrypt($obj, $crypttext, $kh, OPENSSL_PKCS1_PADDING);

            // Encrypt Object Data using private key
            //  $encrypted = openssl_private_encrypt($obj,$crypttext,$kh);       
            if ($encrypted) {
                //  $newPassword = $crypttext;
                $newPassword = $this->EncryptHelper->to_hex($crypttext);
                //  $newPassword = base64_encode($crypttext);
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Unsuccessfull Encrypt',
                    'code' => 500,
                    'data' => $request->all()
                ];
                return response()->json($response, 200);
            }
            // New Password Section
            // Construct dataBody untuk Hit api Member Profile
            $dataBody = [
                'transactionNo' => $this->AlloHelperController->create_transaction_no(),
                'requestData' => [
                    'accessToken' => $newAccessToken,
                    'password' => $oldPassword,
                    'factor' => $oldFactor,
                    'newPassword' => $newPassword,
                    'newFactor' => $newFactor
                ]
            ];
            // Construct Header untuk di passing ke Create_Header_Allo function, nanti response header with sign key
            $header = $dataBody;
            // create Request
            $request_header = new Request();
            $request_header->merge($header);
            // Call fungsi utk create Header
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());
            if ($response_create_header->status) {
                $data_header = $response_create_header->data;

                // Get url MPC Member_Existence from table msystems
                // create Request
                $req_url_mpc = new Request();
                $req_url_mpc->merge(['system_type' => 'mpc_url', 'system_cd' => 'change_email_v2']);
                $res_url_mpc = $this->AlloHelperController->get_allo_value($req_url_mpc);
                if ($res_url_mpc['status']) {
                    // Cek jika data url tidak ada di db
                    if (count($res_url_mpc['data']) > 0) {
                        $url_mpc = $res_url_mpc['data'][0]['system_value'];
                        // HIT MPC API Member Existence
                        // create Request
                        $req_hit_allo = new Request();
                        $req_hit_allo->merge(['mpc_url' => $url_mpc, 'dataHeader' => $data_header, 'dataBody' => $dataBody]);
                        // dd($req_hit_allo);
                        $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
                        $resp_hit_allo['accessToken'] = $newAccessToken;
                        $resp_hit_allo['refreshToken'] = $newRefreshToken;
                        // Cek response success
                        if (strtoupper($resp_hit_allo['message']) == 'SUCCESS') {
                            $response = [
                                'status' => true,
                                'message' => $resp_hit_allo['message'],
                                'code' => 200,
                                'data' => $resp_hit_allo
                            ];
                        } else {
                            $error = [
                                'modul' => 'allo_member_change_password',
                                'actions' => 'Hit Allo Change Password API',
                                'error_log' => json_encode($resp_hit_allo),
                                'device' => "0"
                            ];
                            $report = $this->LogController->error_log($error);
                            $response = [
                                'status' => true,
                                'message' => $resp_hit_allo['message'],
                                'code' => 200,
                                'data' => $resp_hit_allo
                            ];
                        }
                    } else {
                        $response = [
                            'status' => false,
                            'message' => 'Data empty',
                            'code' => 500,
                            'data' => $res_url_mpc
                        ];
                    }
                } else {
                    $response = [
                        'status' => false,
                        'message' => $res_url_mpc['message'],
                        'code' => 500,
                        'data' => $res_url_mpc
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 400,
                    'data' => $request_header
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'allo_member_change_password',
                'actions' => 'Hit Allo Change Password API',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Get Point Balance Member ALLO HIT API
     * body: 
     *	$request	: 
     */
    public function allo_point_balance(Request $request)
    {
        try {
            $rules = [
                'accessToken' => 'required',
                'refreshToken' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 500,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }

            // create new fresh accessToken dan refreshToken
            $response_refresh_token = $this->refresh_token_helper($request->all());
            //  dd($response_refresh_token);
            if ($response_refresh_token['status'] && strtoupper($response_refresh_token['message']) === 'SUCCESS') {
                $newAccessToken = $response_refresh_token['data']->responseData->accessToken;
                $newRefreshToken = $response_refresh_token['data']->responseData->refreshToken;
            } else {
                $newAccessToken = $request['accessToken'];
                $newRefreshToken = $request['refreshToken'];
            }

            // Construct dataBody untuk Hit api MPC
            $dataBody = [
                'requestData' => [
                    'accessToken' => $newAccessToken
                ]
            ];
            // get trxNo
            $trxNo = $this->AlloHelperController->create_transaction_no();
            $dataBody['transactionNo'] = $trxNo;
            // Construct Header untuk di passing ke Create_Header_Allo function, nanti response header with sign key
            $header = $dataBody;
            // create Request
            $request_header = new Request();
            $request_header->merge($header);
            // Call fungsi utk create Header
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());
            if ($response_create_header->status) {
                $data_header = $response_create_header->data;

                // Get url MPC Member_Existence from table msystems
                // create Request
                // $req_url_mpc = new Request();
                // $req_url_mpc->merge(['system_type' => 'mpc_url' , 'system_cd' => 'point_balance_v2']);
                // $res_url_mpc = $this->AlloHelperController->get_allo_value($req_url_mpc);
                $res_url_mpc = json_decode(Msystem::where('system_type', '=', 'mpc_url')
                    ->where('system_cd', '=', 'point_balance_v2')
                    ->first());
                if ($res_url_mpc->id != null) {
                    // Cek jika data url tidak ada di db
                    //    if(count($res_url_mpc['data']) > 0){
                    $url_mpc = $res_url_mpc->system_value;
                    // HIT MPC API Member Existence
                    // create Request
                    $req_hit_allo = new Request();
                    $req_hit_allo->merge(['mpc_url' => $url_mpc, 'dataHeader' => $data_header, 'dataBody' => $dataBody]);
                    $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
                    $resp_hit_allo['accessToken'] = $newAccessToken;
                    $resp_hit_allo['refreshToken'] = $newRefreshToken;
                    // Cek response success
                    if (strtoupper($resp_hit_allo['message']) == 'SUCCESS') {
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    } else {
                        $error = [
                            'modul' => 'allo_point_balance',
                            'actions' => 'Hit Allo Point Balance API',
                            'error_log' => json_encode($resp_hit_allo),
                            'device' => "0"
                        ];
                        $report = $this->LogController->error_log($error);
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    }
                    //    } else {
                    //         $response = [
                    //             'status' => false,
                    //             'message' => 'Data empty',
                    //             'code' => 500,
                    //             'data' => $res_url_mpc 
                    //         ];
                    //    }
                } else {
                    $response = [
                        'status' => false,
                        'message' => "Data Empty",
                        'code' => 500,
                        'data' => $res_url_mpc
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 400,
                    'data' => $request_header
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'allo_point_balance',
                'actions' => 'Hit Allo Point Balance API',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Get Point History Member ALLO HIT API
     * body: 
     *	$request	: 
     */
    public function allo_point_history(Request $request)
    {
        try {
            $rules = [
                'accessToken' => 'required',
                'refreshToken' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 500,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }

            // create new fresh accessToken dan refreshToken
            $response_refresh_token = $this->refresh_token_helper($request->all());
            //  dd($response_refresh_token);
            if ($response_refresh_token['status'] && strtoupper($response_refresh_token['message']) === 'SUCCESS') {
                $newAccessToken = $response_refresh_token['data']->responseData->accessToken;
                $newRefreshToken = $response_refresh_token['data']->responseData->refreshToken;
            } else {
                $newAccessToken = $request['accessToken'];
                $newRefreshToken = $request['refreshToken'];
            }

            // Construct dataBody untuk Hit api MPC
            $dataBody = [
                'requestData' => [
                    'accessToken' => $newAccessToken
                ]
            ];

            // Check if startTime and endTime pass from request
            if ($request['startTime'] !== null || $request['endTime'] !== null) {
                $rules = [
                    'startTime' => 'required',
                    'endTime' => 'required',
                    'page' => 'required',
                    'type' => 'required'
                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    // return response gagal
                    $response = [
                        'status' => false,
                        'code' => 500,
                        'message' => $validator->errors()->first(),
                        'data' => $request->all(),

                    ];
                    return response()->json($response, 200);
                }

                $dataBody['requestData']['startTime'] = $request['startTime'];
                $dataBody['requestData']['endTime'] = $request['endTime'];
                if ($request['page'] !== null)
                    $dataBody['requestData']['page']['currentPage'] = (int)$request['page'];
                else
                    $dataBody['requestData']['page']['currentPage'] = 1;

                $dataBody['requestData']['page']['pageSize'] = 10;

                $dataBody['requestData']['changeType'] = strtoupper($request['type']);
            }

            // get trxNo
            $trxNo = $this->AlloHelperController->create_transaction_no();
            $dataBody['transactionNo'] = $trxNo;
            // Construct Header untuk di passing ke Create_Header_Allo function, nanti response header with sign key
            $header = $dataBody;
            // create Request
            $request_header = new Request();
            $request_header->merge($header);
            // Call fungsi utk create Header
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());
            if ($response_create_header->status) {
                $data_header = $response_create_header->data;

                // Get url MPC Member_Existence from table msystems
                // create Request
                // $req_url_mpc = new Request();
                // $req_url_mpc->merge(['system_type' => 'mpc_url' , 'system_cd' => 'point_balance_v2']);
                // $res_url_mpc = $this->AlloHelperController->get_allo_value($req_url_mpc);
                $res_url_mpc = json_decode(Msystem::where('system_type', '=', 'mpc_url')
                    ->where('system_cd', '=', 'point_history_v2')
                    ->first());
                if ($res_url_mpc->id != null) {
                    // Cek jika data url tidak ada di db
                    if (count($res_url_mpc['data']) > 0) {
                        $url_mpc = $res_url_mpc->system_value;
                        // HIT MPC API Member Existence
                        // create Request
                        $req_hit_allo = new Request();
                        $req_hit_allo->merge(['mpc_url' => $url_mpc, 'dataHeader' => $data_header, 'dataBody' => $dataBody]);
                        $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
                        $resp_hit_allo['accessToken'] = $newAccessToken;
                        $resp_hit_allo['refreshToken'] = $newRefreshToken;
                        // Cek response success
                        if (strtoupper($resp_hit_allo['message']) == 'SUCCESS') {
                            $response = [
                                'status' => true,
                                'message' => $resp_hit_allo['message'],
                                'code' => 200,
                                'data' => $resp_hit_allo
                            ];
                        } else {
                            $error = [
                                'modul' => 'allo_point_history',
                                'actions' => 'Hit Allo Point History',
                                'error_log' => json_encode($resp_hit_allo),
                                'device' => "0"
                            ];
                            $report = $this->LogController->error_log($error);
                            $response = [
                                'status' => true,
                                'message' => $resp_hit_allo['message'],
                                'code' => 200,
                                'data' => $resp_hit_allo
                            ];
                        }
                    } else {
                        $response = [
                            'status' => false,
                            'message' => 'Data empty',
                            'code' => 500,
                            'data' => $res_url_mpc
                        ];
                    }
                } else {
                    $response = [
                        'status' => false,
                        'message' => $res_url_mpc['message'],
                        'code' => 500,
                        'data' => $res_url_mpc
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 400,
                    'data' => $request_header
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'allo_point_history',
                'actions' => 'Hit Allo Point History',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Get URL Page untuk Edit Member Profile ALLO
     * body: 
     *	$request	: 
     */
    public function allo_member_edit_profile(Request $request)
    {
        try {
            $rules = [
                'accessToken' => 'required',
                'refreshToken' => 'required',
                'osType' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 500,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }
            // Validasi osType utk menentukan callback response ke Allo
            // osType 'web' utk web langsung ke laman web diambil dari .env('APP_REDIRECT_URL')
            // osType 'android' / 'ios' response dilempar untuk get_member_allo ke view mpccallback
            if (strtoupper($request['osType']) == 'WEB') {
                // WEB callback
                $callback =  $this->url_redirect . 'my_profile';
            } else if ((strtoupper($request['osType']) == 'IOS') || (strtoupper($request['osType']) == 'ANDROID')) {
                // Mobile callback
                $callback = $this->url_redirect_mobile . 'editprofilecallback';
            }

            // create new fresh accessToken dan refreshToken
            $response_refresh_token = $this->refresh_token_helper($request->all());
            //  dd($response_refresh_token);
            if ($response_refresh_token['status'] && strtoupper($response_refresh_token['message']) === 'SUCCESS') {
                $newAccessToken = $response_refresh_token['data']->responseData->accessToken;
                $newRefreshToken = $response_refresh_token['data']->responseData->refreshToken;
            } else {
                $newAccessToken = $request['accessToken'];
                $newRefreshToken = $request['refreshToken'];
            }

            // Construct dataBody untuk Hit api MPC
            $dataBody = [
                'requestData' => [
                    'accessToken' => $newAccessToken
                ]
            ];
            // get trxNo
            $trxNo = $this->AlloHelperController->create_transaction_no();
            $dataBody['transactionNo'] = $trxNo;
            // Construct Header untuk di passing ke Create_Header_Allo function, nanti response header with sign key
            $header = $dataBody;
            // create Request
            $request_header = new Request();
            $request_header->merge($header);
            // Call fungsi utk create Header
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());
            if ($response_create_header->status) {
                $data_header = $response_create_header->data;

                // Get url MPC Member_Existence from table msystems
                // create Request
                // $req_url_mpc = new Request();
                // $req_url_mpc->merge(['system_type' => 'mpc_url' , 'system_cd' => 'edit_member_profile_v2']);
                // $res_url_mpc = $this->AlloHelperController->get_allo_value($req_url_mpc);
                $res_url_mpc = json_decode(Msystem::where('system_type', '=', 'mpc_url')
                    ->where('system_cd', '=', 'edit_member_profile_v2')
                    ->first());
                if ($res_url_mpc->id != null) {
                    // Cek jika data url tidak ada di db
                    //    if(count($res_url_mpc['data']) > 0){
                    $url_mpc = $res_url_mpc->system_value;
                    // HIT MPC API Member Existence
                    // create Request
                    $req_hit_allo = new Request();
                    $req_hit_allo->merge(['mpc_url' => $url_mpc, 'dataHeader' => $data_header, 'dataBody' => $dataBody]);
                    $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
                    $resp_hit_allo['accessToken'] = $newAccessToken;
                    $resp_hit_allo['refreshToken'] = $newRefreshToken;
                    // Tambahan callback untuk nanti dipassing ke Laman Edit Profile Allo
                    $resp_hit_allo['responseData']['updateProfilePageUri'] = $resp_hit_allo['responseData']['updateProfilePageUri'] . '&callback=' . $callback;
                    // Cek response success
                    if (strtoupper($resp_hit_allo['message']) == 'SUCCESS') {
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    } else {
                        $error = [
                            'modul' => 'allo_member_edit_profile',
                            'actions' => 'Hit Allo get URL for Edit Member Profile',
                            'error_log' => json_encode($resp_hit_allo),
                            'device' => "0"
                        ];
                        $report = $this->LogController->error_log($error);
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    }
                    //    } else {
                    //         $response = [
                    //             'status' => false,
                    //             'message' => 'Data empty',
                    //             'code' => 500,
                    //             'data' => $res_url_mpc 
                    //         ];
                    //    }
                } else {
                    $response = [
                        'status' => false,
                        'message' => "Data Empty",
                        'code' => 500,
                        'data' => $res_url_mpc
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 400,
                    'data' => $request_header
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'allo_member_edit_profile',
                'actions' => 'Hit Allo get URL for Edit Member Profile',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Add Point Member ALLO HIT API
     * body: 
     *	$request	: 
     */
    public function allo_point_add(Request $request)
    {
        try {
            $rules = [
                'accessToken' => 'required',
                'refreshToken' => 'required',
                'amount' => 'required|numeric'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 500,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }

            // create new fresh accessToken dan refreshToken
            $response_refresh_token = $this->refresh_token_helper($request->all());
            //  dd($response_refresh_token);
            if ($response_refresh_token['status'] && strtoupper($response_refresh_token['message']) === 'SUCCESS') {
                $newAccessToken = $response_refresh_token['data']->responseData->accessToken;
                $newRefreshToken = $response_refresh_token['data']->responseData->refreshToken;
            } else {
                $newAccessToken = $request['accessToken'];
                $newRefreshToken = $request['refreshToken'];
            }

            // Get url Merchant ID for THG from table msystems
            // create Request
            // $req_merch_id = new Request();
            // $req_merch_id->merge(['system_type' => 'allo' , 'system_cd' => 'merchantID']);
            // $res_merch_id = $this->AlloHelperController->get_allo_value($req_merch_id);
            $res_merch_id = json_decode(Msystem::where('system_type', '=', 'allo')
                ->where('system_cd', '=', 'merchantID')
                ->first());
            if ($res_merch_id != null) {
                $res_merch_id = $res_merch_id->system_value;
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Data empty',
                    'code' => 500,
                    'data' => $res_merch_id
                ];
                return response()->json($response, 200);
            }

            // Construct data
            $dataBody = [
                'requestData' => [
                    'accessToken' => $newAccessToken,
                    'amount' => $request['amount'],
                    'merchantId' => $res_merch_id
                ]
            ];
            // get trxNo
            $trxNo = $this->AlloHelperController->create_transaction_no();
            $dataBody['transactionNo'] = $trxNo;
            // Construct Header untuk di passing ke Create_Header_Allo function, nanti response header with sign key
            $header = $dataBody;
            // create Request
            $request_header = new Request();
            $request_header->merge($header);
            // Call fungsi utk create Header
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());
            if ($response_create_header->status) {
                $data_header = $response_create_header->data;

                // Get url MPC Allo Point from table msystems
                // create Request
                // $req_url_mpc = new Request();
                // $req_url_mpc->merge(['system_type' => 'mpc_url' , 'system_cd' => 'point_add_v2']);
                // $res_url_mpc = $this->AlloHelperController->get_allo_value($req_url_mpc);
                $res_url_mpc = json_decode(Msystem::where('system_type', '=', 'mpc_url')
                    ->where('system_cd', '=', 'point_add_v2')
                    ->first());
                if ($res_url_mpc->id != null) {
                    // Cek jika data url tidak ada di db
                    //if(count($res_url_mpc['data']) > 0){
                    $url_mpc = $res_url_mpc->system_value;
                    // HIT MPC API Point Add
                    // create Request
                    $req_hit_allo = new Request();
                    $req_hit_allo->merge(['mpc_url' => $url_mpc, 'dataHeader' => $data_header, 'dataBody' => $dataBody]);
                    $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
                    $resp_hit_allo['accessToken'] = $newAccessToken;
                    $resp_hit_allo['refreshToken'] = $newRefreshToken;
                    // Cek response success
                    if (strtoupper($resp_hit_allo['message']) == 'SUCCESS') {
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    } else {
                        $error = [
                            'modul' => 'allo_point_add',
                            'actions' => 'Hit Allo Point ADD',
                            'error_log' => json_encode($resp_hit_allo),
                            'device' => "0"
                        ];
                        $report = $this->LogController->error_log($error);
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    }
                    //} else {
                    //     $response = [
                    //         'status' => false,
                    //         'message' => 'Data empty',
                    //         'code' => 500,
                    //         'data' => $res_url_mpc 
                    //     ];
                    //}
                } else {
                    $response = [
                        'status' => false,
                        'message' => $res_url_mpc['message'],
                        'code' => 500,
                        'data' => $res_url_mpc
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 400,
                    'data' => $request_header
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'allo_point_add',
                'actions' => 'Hit Allo Point ADD',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Consume Point Member ALLO HIT API
     * body: 
     *	$request	: 
     */
    public function allo_point_consume(Request $request)
    {
        try {
            $rules = [
                'accessToken' => 'required',
                'refreshToken' => 'required',
                'amount' => 'required|numeric',
                'phone' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 500,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }

            // create new fresh accessToken dan refreshToken
            $response_refresh_token = $this->refresh_token_helper($request->all());
            //  dd($response_refresh_token);
            if ($response_refresh_token['status'] && strtoupper($response_refresh_token['message']) === 'SUCCESS') {
                $newAccessToken = $response_refresh_token['data']->responseData->accessToken;
                $newRefreshToken = $response_refresh_token['data']->responseData->refreshToken;
            } else {
                $newAccessToken = $request['accessToken'];
                $newRefreshToken = $request['refreshToken'];
            }

            // Get url Merchant ID for THG from table msystems
            // create Request
            // $req_merch_id = new Request();
            // $req_merch_id->merge(['system_type' => 'allo' , 'system_cd' => 'merchantID']);
            // $res_merch_id = $this->AlloHelperController->get_allo_value($req_merch_id);
            $res_merch_id = json_decode(Msystem::where('system_type', '=', 'allo')
                ->where('system_cd', '=', 'merchantID')
                ->first());
            if ($res_merch_id->id != null) {
                $res_merch_id = $res_merch_id->system_value;
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Data empty',
                    'code' => 500,
                    'data' => $res_merch_id
                ];
                return response()->json($response, 200);
            }

            $orderNo = $this->AlloHelperController->create_transaction_no();

            // Construct data
            $dataBody = [
                'requestData' => [
                    'amount' => $request['amount'],
                    'phoneNo' => $request['phone'],
                    'externalMerchantId' => $res_merch_id,
                    'externalMerchantName' => "Trans Hotel Group - TLH",
                    'accessToken' => $newAccessToken,
                    'orderNo' => $orderNo,
                    'acquirer' => 'OTHER'
                ]
            ];
            // get trxNo
            $trxNo = $this->AlloHelperController->create_transaction_no();
            $dataBody['transactionNo'] = $trxNo;
            // Construct Header untuk di passing ke Create_Header_Allo function, nanti response header with sign key
            $header = $dataBody;
            // create Request
            $request_header = new Request();
            $request_header->merge($header);
            // Call fungsi utk create Header
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());
            if ($response_create_header->status) {
                $data_header = $response_create_header->data;

                // Get url MPC Allo Point from table msystems
                // create Request
                // $req_url_mpc = new Request();
                // $req_url_mpc->merge(['system_type' => 'mpc_url' , 'system_cd' => 'point_consume_v2']);
                // $res_url_mpc = $this->AlloHelperController->get_allo_value($req_url_mpc);
                $res_url_mpc = json_decode(Msystem::where('system_type', '=', 'mpc_url')
                    ->where('system_cd', '=', 'point_consume_v2')
                    ->first());
                if ($res_url_mpc != null) {
                    // Cek jika data url tidak ada di db
                    //    if(count($res_url_mpc['data']) > 0){
                    $url_mpc = $res_url_mpc->system_value;
                    // HIT MPC API Point Add
                    // create Request
                    $req_hit_allo = new Request();
                    $req_hit_allo->merge(['mpc_url' => $url_mpc, 'dataHeader' => $data_header, 'dataBody' => $dataBody]);
                    $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
                    $resp_hit_allo['accessToken'] = $newAccessToken;
                    $resp_hit_allo['refreshToken'] = $newRefreshToken;
                    $resp_hit_allo['orderNo'] = $orderNo;
                    // Cek response success
                    if (strtoupper($resp_hit_allo['message']) == 'SUCCESS') {
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    } else {
                        $error = [
                            'modul' => 'allo_point_consume',
                            'actions' => 'Hit Allo Point Consume',
                            'error_log' => json_encode($resp_hit_allo),
                            'device' => "0"
                        ];
                        $report = $this->LogController->error_log($error);
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    }
                    //    } else {
                    //         $response = [
                    //             'status' => false,
                    //             'message' => 'Data empty',
                    //             'code' => 500,
                    //             'data' => $res_url_mpc 
                    //         ];
                    //    }
                } else {
                    $response = [
                        'status' => false,
                        'message' => "Data Empty",
                        'code' => 500,
                        'data' => $res_url_mpc
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 400,
                    'data' => $request_header
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'allo_point_consume',
                'actions' => 'Hit Allo Point Consume',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Query/Get Coupon Instance LIST
     * body: status : AVAILABLE, USED or EXPIRED
     *	$request	: 
     */
    public function coupon_instance_list(Request $request)
    {
        try {
            $rules = [
                'accessToken' => 'required',
                'refreshToken' => 'required',
                'status' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 500,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }
            // STATUS for API Coupon instance
            $status_instance = strtoupper($request['status']);

            // Construct data
            $dataBody = [
                'requestData' => [
                    'accessToken' => $request['accessToken'],
                    'status' => $status_instance
                ],
                'transactionNo' => $this->AlloHelperController->create_transaction_no()
            ];

            // Construct Header untuk di passing ke Create_Header_Allo function, nanti response header with sign key
            $header = $dataBody;
            // create Request
            $request_header = new Request();
            $request_header->merge($header);
            // Call fungsi utk create Header
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());

            if ($response_create_header->status) {

                $data_header = $response_create_header->data;
                // Get url MPC Allo Point from table msystems
                $res_url_mpc = json_decode(Msystem::where('system_type', '=', 'mpc_url')
                    ->where('system_cd', '=', 'coupon_instance_list_v2')
                    ->first());

                if ($res_url_mpc->id != null) {

                    // Cek jika data url tidak ada di db
                    $url_mpc = $res_url_mpc->system_value;
                    // HIT MPC API Point Add

                    // create Request
                    $req_hit_allo = new Request();
                    $req_hit_allo->merge(['mpc_url' => $url_mpc, 'dataHeader' => $data_header, 'dataBody' => $dataBody]);
                    $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
                    $resp_hit_allo['accessToken'] = $request['accessToken'];
                    $resp_hit_allo['refreshToken'] = $request['refreshToken'];

                    // Cek response success
                    if (strtoupper($resp_hit_allo['message']) == 'SUCCESS') {
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    } else {
                        $this->LogController->error_log([
                            'modul' => 'coupon_instance_list',
                            'actions' => 'Hit Allo Coupon Instance List',
                            'error_log' => json_encode($resp_hit_allo),
                            'device' => "0"
                        ]);
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    }
                } else {
                    $response = [
                        'status' => false,
                        'message' => "Data Empty",
                        'code' => 500,
                        'data' => $res_url_mpc
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 400,
                    'data' => $request_header
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'coupon_instance_list',
                'actions' => 'Hit Allo Coupon Instance List',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Query/Get Coupon LIST
     * body: 
     *	$request	: 
     */
    public function coupon_query_list(Request $request)
    {
        try {
            $rules = [
                'accessToken' => 'required',
                'refreshToken' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 500,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }

            // create new fresh accessToken dan refreshToken
            //  // $response_refresh_token = $this->refresh_token_helper($request->all());
            //  dd($response_refresh_token);

            //  if($response_refresh_token['status'] && strtoupper($response_refresh_token['message']) === 'SUCCESS')
            //  { 
            //     $newAccessToken = $response_refresh_token['data']->responseData->accessToken;
            //     $newRefreshToken = $response_refresh_token['data']->responseData->refreshToken;
            //  } else {
            //     $newAccessToken = $request['accessToken'];
            //     $newRefreshToken = $request['refreshToken'];
            //  }


            // Construct data
            $dataBody = [
                'requestData' => [
                    // 'accessToken' => $newAccessToken,
                    'accessToken' => $request['accessToken']
                ]
            ];

            // Validate request['page']
            if (isset($request['page'])) {
                // Page untuk query jika page > 1
                $dataBody['requestData']['page']['currentPage'] = $request['page'];
                $dataBody['requestData']['page']['pageSize'] = 10;
            }

            // get trxNo
            $trxNo = $this->AlloHelperController->create_transaction_no();
            $dataBody['transactionNo'] = $trxNo;

            // Construct Header untuk di passing ke Create_Header_Allo function, nanti response header with sign key
            $header = $dataBody;
            // create Request
            $request_header = new Request();
            $request_header->merge($header);
            // Call fungsi utk create Header
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());
            if ($response_create_header->status) {
                $data_header = $response_create_header->data;

                // Get url MPC Allo Point from table msystems
                // create Request
                // $req_url_mpc = new Request();
                // $req_url_mpc->merge(['system_type' => 'mpc_url' , 'system_cd' => 'coupon_query_list_v2']);
                // $res_url_mpc = $this->AlloHelperController->get_allo_value($req_url_mpc);
                $res_url_mpc = json_decode(Msystem::where('system_type', '=', 'mpc_url')
                    ->where('system_cd', '=', 'coupon_query_list_v2')
                    ->first());
                if ($res_url_mpc->id != null) {
                    // Cek jika data url tidak ada di db
                    //    if(count($res_url_mpc['data']) > 0){
                    $url_mpc = $res_url_mpc->system_value;
                    // HIT MPC API Point Add
                    // create Request
                    $req_hit_allo = new Request();
                    $req_hit_allo->merge(['mpc_url' => $url_mpc, 'dataHeader' => $data_header, 'dataBody' => $dataBody]);
                    $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
                    $resp_hit_allo['accessToken'] = $request['accessToken'];
                    $resp_hit_allo['refreshToken'] = $request['refreshToken'];
                    // $resp_hit_allo['accessToken'] = $newAccessToken;
                    // $resp_hit_allo['refreshToken'] = $newRefreshToken;
                    // Cek response success
                    if (strtoupper($resp_hit_allo['message']) == 'SUCCESS') {
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    } else {
                        $error = [
                            'modul' => 'coupon_query_list',
                            'actions' => 'Hit Allo Coupon Query List',
                            'error_log' => json_encode($resp_hit_allo),
                            'device' => "0"
                        ];
                        $report = $this->LogController->error_log($error);
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    }
                    //    } else {
                    //         $response = [
                    //             'status' => false,
                    //             'message' => 'Data empty',
                    //             'code' => 500,
                    //             'data' => $res_url_mpc 
                    //         ];
                    //    }
                } else {
                    $response = [
                        'status' => false,
                        'message' => "Data Empty",
                        'code' => 500,
                        'data' => $res_url_mpc
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 400,
                    'data' => $request_header
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'coupon_query_list',
                'actions' => 'Hit Allo Query List',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Query/Get Coupon Instance Detail
     * body: 
     *	$request	: 
     */
    public function coupon_instance_detail_query(Request $request)
    {
        try {
            $rules = [
                'accessToken' => 'required',
                'refreshToken' => 'required',
                'couponId' => 'required',
                'couponNo' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 500,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }

            // create new fresh accessToken dan refreshToken
            //  // $response_refresh_token = $this->refresh_token_helper($request->all());
            //  dd($response_refresh_token);

            //  if($response_refresh_token['status'] && strtoupper($response_refresh_token['message']) === 'SUCCESS')
            //  { 
            //     $newAccessToken = $response_refresh_token['data']->responseData->accessToken;
            //     $newRefreshToken = $response_refresh_token['data']->responseData->refreshToken;
            //  } else {
            //     $newAccessToken = $request['accessToken'];
            //     $newRefreshToken = $request['refreshToken'];
            //  }


            // Construct data
            $dataBody = [
                'requestData' => [
                    // 'accessToken' => $newAccessToken,
                    'accessToken' => $request['accessToken'],
                    'couponId' => $request['couponId'],
                    'couponNo' => $request['couponNo']
                ]
            ];

            // get trxNo
            $trxNo = $this->AlloHelperController->create_transaction_no();
            $dataBody['transactionNo'] = $trxNo;

            // Construct Header untuk di passing ke Create_Header_Allo function, nanti response header with sign key
            $header = $dataBody;
            // create Request
            $request_header = new Request();
            $request_header->merge($header);
            // Call fungsi utk create Header
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());
            if ($response_create_header->status) {
                $data_header = $response_create_header->data;

                // Get url MPC Allo Point from table msystems
                // create Request
                // $req_url_mpc = new Request();
                // $req_url_mpc->merge(['system_type' => 'mpc_url' , 'system_cd' => 'coupon_instance_detail_query_v2']);
                // $res_url_mpc = $this->AlloHelperController->get_allo_value($req_url_mpc);
                $res_url_mpc = json_decode(Msystem::where('system_type', '=', 'mpc_url')
                    ->where('system_cd', '=', 'coupon_instance_detail_query_v2')
                    ->first());
                if ($res_url_mpc->id != null) {
                    // Cek jika data url tidak ada di db
                    //    if(count($res_url_mpc['data']) > 0){
                    $url_mpc = $res_url_mpc->system_value;
                    // HIT MPC API Point Add
                    // create Request
                    $req_hit_allo = new Request();
                    $req_hit_allo->merge(['mpc_url' => $url_mpc, 'dataHeader' => $data_header, 'dataBody' => $dataBody]);
                    $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
                    $resp_hit_allo['accessToken'] = $request['accessToken'];
                    $resp_hit_allo['refreshToken'] = $request['refreshToken'];
                    // $resp_hit_allo['accessToken'] = $newAccessToken;
                    // $resp_hit_allo['refreshToken'] = $newRefreshToken;
                    // Cek response success
                    if (strtoupper($resp_hit_allo['message']) == 'SUCCESS') {
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    } else {
                        $error = [
                            'modul' => 'coupon_instance_detail_query',
                            'actions' => 'Hit Allo Coupon Instance Detail Query',
                            'error_log' => json_encode($resp_hit_allo),
                            'device' => "0"
                        ];
                        $report = $this->LogController->error_log($error);
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    }
                    //    } else {
                    //         $response = [
                    //             'status' => false,
                    //             'message' => 'Data empty',
                    //             'code' => 500,
                    //             'data' => $res_url_mpc 
                    //         ];
                    //    }
                } else {
                    $response = [
                        'status' => false,
                        'message' => "Data Empty",
                        'code' => 500,
                        'data' => $res_url_mpc
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 400,
                    'data' => $request_header
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'coupon_instance_detail_query',
                'actions' => 'Hit Allo Coupon Instance Detail Query',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Wallet Registration / Activation
     * body: 
     *	$request	: 
     */
    public function wallet_registration(Request $request)
    {
        try {
            $rules = [
                'accessToken' => 'required',
                'refreshToken' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 500,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }

            // create new fresh accessToken dan refreshToken
            //  // $response_refresh_token = $this->refresh_token_helper($request->all());
            //  dd($response_refresh_token);

            //  if($response_refresh_token['status'] && strtoupper($response_refresh_token['message']) === 'SUCCESS')
            //  { 
            //     $newAccessToken = $response_refresh_token['data']->responseData->accessToken;
            //     $newRefreshToken = $response_refresh_token['data']->responseData->refreshToken;
            //  } else {
            //     $newAccessToken = $request['accessToken'];
            //     $newRefreshToken = $request['refreshToken'];
            //  }


            // Construct data
            $dataBody = [
                'requestData' => [
                    // 'accessToken' => $newAccessToken,
                    'accessToken' => $request['accessToken']
                ]
            ];

            // get trxNo
            $trxNo = $this->AlloHelperController->create_transaction_no();
            $dataBody['transactionNo'] = $trxNo;

            // Construct Header untuk di passing ke Create_Header_Allo function, nanti response header with sign key
            $header = $dataBody;
            // create Request
            $request_header = new Request();
            $request_header->merge($header);
            // Call fungsi utk create Header
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());
            if ($response_create_header->status) {
                $data_header = $response_create_header->data;

                // Get url Wallet Registration from table msystems
                // create Request
                // $req_url_mpc = new Request();
                // $req_url_mpc->merge(['system_type' => 'mpc_url' , 'system_cd' => 'wallet_registration_v2']);
                // $res_url_mpc = $this->AlloHelperController->get_allo_value($req_url_mpc);
                $res_url_mpc = json_decode(Msystem::where('system_type', '=', 'mpc_url')
                    ->where('system_cd', '=', 'wallet_registration_v2')
                    ->first());
                if ($res_url_mpc->id != null) {
                    // Cek jika data url tidak ada di db
                    //    if(count($res_url_mpc['data']) > 0){
                    $url_mpc = $res_url_mpc->system_value;
                    // HIT MPC API Point Add
                    // create Request
                    $req_hit_allo = new Request();
                    $req_hit_allo->merge(['mpc_url' => $url_mpc, 'dataHeader' => $data_header, 'dataBody' => $dataBody]);
                    $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
                    $resp_hit_allo['accessToken'] = $request['accessToken'];
                    $resp_hit_allo['refreshToken'] = $request['refreshToken'];
                    // $resp_hit_allo['accessToken'] = $newAccessToken;
                    // $resp_hit_allo['refreshToken'] = $newRefreshToken;
                    // Cek response success
                    if (strtoupper($resp_hit_allo['message']) == 'SUCCESS') {
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    } else {
                        $error = [
                            'modul' => 'wallet_registration',
                            'actions' => 'Hit Allo Wallet Registration',
                            'error_log' => json_encode($resp_hit_allo),
                            'device' => "0"
                        ];
                        $report = $this->LogController->error_log($error);
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    }
                    //    } else {
                    //         $response = [
                    //             'status' => false,
                    //             'message' => 'Data empty',
                    //             'code' => 500,
                    //             'data' => $res_url_mpc 
                    //         ];
                    //    }
                } else {
                    $response = [
                        'status' => false,
                        'message' => "Data Empty",
                        'code' => 500,
                        'data' => $res_url_mpc
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 400,
                    'data' => $request_header
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'wallet_registration',
                'actions' => 'Hit Allo Wallet Registration',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Wallet Query Info
     * body: 
     *	$request	: 
     */
    public function wallet_query_info(Request $request)
    {
        try {
            $rules = [
                'accessToken' => 'required',
                'refreshToken' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 500,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }

            // create new fresh accessToken dan refreshToken
            //  // $response_refresh_token = $this->refresh_token_helper($request->all());
            //  dd($response_refresh_token);

            //  if($response_refresh_token['status'] && strtoupper($response_refresh_token['message']) === 'SUCCESS')
            //  { 
            //     $newAccessToken = $response_refresh_token['data']->responseData->accessToken;
            //     $newRefreshToken = $response_refresh_token['data']->responseData->refreshToken;
            //  } else {
            //     $newAccessToken = $request['accessToken'];
            //     $newRefreshToken = $request['refreshToken'];
            //  }


            // Construct data
            $dataBody = [
                'requestData' => [
                    // 'accessToken' => $newAccessToken,
                    'accessToken' => $request['accessToken']
                ]
            ];

            // get trxNo
            $trxNo = $this->AlloHelperController->create_transaction_no();
            $dataBody['transactionNo'] = $trxNo;

            // Construct Header untuk di passing ke Create_Header_Allo function, nanti response header with sign key
            $header = $dataBody;
            // create Request
            $request_header = new Request();
            $request_header->merge($header);
            // Call fungsi utk create Header
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());
            if ($response_create_header->status) {
                $data_header = $response_create_header->data;

                // Get url Wallet Registration from table msystems
                // create Request
                // $req_url_mpc = new Request();
                // $req_url_mpc->merge(['system_type' => 'mpc_url' , 'system_cd' => 'wallet_query_info_v2']);
                // $res_url_mpc = $this->AlloHelperController->get_allo_value($req_url_mpc);
                $res_url_mpc = json_decode(Msystem::where('system_type', '=', 'mpc_url')
                    ->where('system_cd', '=', 'wallet_query_info_v2')
                    ->first());
                if ($res_url_mpc->id != null) {
                    // Cek jika data url tidak ada di db
                    //    if(count($res_url_mpc['data']) > 0){
                    $url_mpc = $res_url_mpc->system_value;
                    // HIT MPC API Point Add
                    // create Request
                    $req_hit_allo = new Request();
                    $req_hit_allo->merge(['mpc_url' => $url_mpc, 'dataHeader' => $data_header, 'dataBody' => $dataBody]);
                    $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
                    $resp_hit_allo['accessToken'] = $request['accessToken'];
                    $resp_hit_allo['refreshToken'] = $request['refreshToken'];
                    // $resp_hit_allo['accessToken'] = $newAccessToken;
                    // $resp_hit_allo['refreshToken'] = $newRefreshToken;
                    // Cek response success
                    if (strtoupper($resp_hit_allo['message']) == 'SUCCESS') {
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    } else {
                        $error = [
                            'modul' => 'wallet_query_info',
                            'actions' => 'Hit Allo Wallet Query Info',
                            'error_log' => json_encode($resp_hit_allo),
                            'device' => "0"
                        ];
                        $report = $this->LogController->error_log($error);
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    }
                    //    } else {
                    //         $response = [
                    //             'status' => false,
                    //             'message' => 'Data empty',
                    //             'code' => 500,
                    //             'data' => $res_url_mpc 
                    //         ];
                    //    }
                } else {
                    $response = [
                        'status' => false,
                        'message' => "Data Empty",
                        'code' => 500,
                        'data' => $res_url_mpc
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 400,
                    'data' => $request_header
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'wallet_query_info',
                'actions' => 'Hit Allo Wallet Query Info',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }
    /**
     * Function: wallet registration status
     * body: 
     *	$request	: 
     */
    public function wallet_registration_status(Request $request)
    {
        try {
            $rules = [
                'accessToken' => 'required',
                'refreshToken' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 500,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }

            // create new fresh accessToken dan refreshToken
            //  // $response_refresh_token = $this->refresh_token_helper($request->all());
            //  dd($response_refresh_token);

            //  if($response_refresh_token['status'] && strtoupper($response_refresh_token['message']) === 'SUCCESS')
            //  { 
            //     $newAccessToken = $response_refresh_token['data']->responseData->accessToken;
            //     $newRefreshToken = $response_refresh_token['data']->responseData->refreshToken;
            //  } else {
            //     $newAccessToken = $request['accessToken'];
            //     $newRefreshToken = $request['refreshToken'];
            //  }


            // Construct data
            $dataBody = [
                'requestData' => [
                    // 'accessToken' => $newAccessToken,
                    'accessToken' => $request['accessToken']
                ]
            ];

            // get trxNo
            $trxNo = $this->AlloHelperController->create_transaction_no();
            $dataBody['transactionNo'] = $trxNo;

            // Construct Header untuk di passing ke Create_Header_Allo function, nanti response header with sign key
            $header = $dataBody;
            // create Request
            $request_header = new Request();
            $request_header->merge($header);
            // Call fungsi utk create Header
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());
            if ($response_create_header->status) {
                $data_header = $response_create_header->data;

                // Get url Wallet Registration from table msystems
                // create Request
                // $req_url_mpc = new Request();
                // $req_url_mpc->merge(['system_type' => 'mpc_url' , 'system_cd' => 'wallet_registration_status_v2']);
                // $res_url_mpc = $this->AlloHelperController->get_allo_value($req_url_mpc);
                $res_url_mpc = json_decode(Msystem::where('system_type', '=', 'mpc_url')
                    ->where('system_cd', '=', 'wallet_registration_status_v2')
                    ->first());
                if ($res_url_mpc->id != null) {
                    // Cek jika data url tidak ada di db
                    //    if(count($res_url_mpc['data']) > 0){
                    $url_mpc = $res_url_mpc->system_value;
                    // HIT MPC API Point Add
                    // create Request
                    $req_hit_allo = new Request();
                    $req_hit_allo->merge(['mpc_url' => $url_mpc, 'dataHeader' => $data_header, 'dataBody' => $dataBody]);
                    $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
                    $resp_hit_allo['accessToken'] = $request['accessToken'];
                    $resp_hit_allo['refreshToken'] = $request['refreshToken'];
                    // $resp_hit_allo['accessToken'] = $newAccessToken;
                    // $resp_hit_allo['refreshToken'] = $newRefreshToken;
                    // Cek response success
                    if (strtoupper($resp_hit_allo['message']) == 'SUCCESS') {
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    } else {
                        $error = [
                            'modul' => 'wallet_registration_status',
                            'actions' => 'Hit Wallet Registration Status',
                            'error_log' => json_encode($resp_hit_allo),
                            'device' => "0"
                        ];
                        $report = $this->LogController->error_log($error);
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    }
                    //    } else {
                    //         $response = [
                    //             'status' => false,
                    //             'message' => 'Data empty',
                    //             'code' => 500,
                    //             'data' => $res_url_mpc 
                    //         ];
                    //    }
                } else {
                    $response = [
                        'status' => false,
                        'message' => "Data Empty",
                        'code' => 500,
                        'data' => $res_url_mpc
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 400,
                    'data' => $request_header
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'wallet_registration_status',
                'actions' => 'Hit Allo Wallet Registration Status',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Function: Auth By Token, for cross BU JUMP, 
     *  Metode Pembayaran memakai Allo Pay
     * body: 
     *	$request	: 
     */
    public function auth_by_token(Request $request)
    {
        try {
            $trxNo = $this->AlloHelperController->create_transaction_no();
            $verifier = $this->EncryptHelper->create_verifier();
            $codeChallenge = $this->EncryptHelper->hashingVerifier($verifier);

            // Check Request Validation
            $rules = [
                'accessToken' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }

            // Construct default body param - default utk WEB
            $dataBody = [
                'transactionNo' => $trxNo,
                'requestData' => [
                    'responseType' => 'CODE',
                    'codeChallengeMethod' => 'SHA256',
                    'codeChallenge' => $codeChallenge,
                    'accessToken' => $request->accessToken,
                    'targetAppId' => ''
                ]
            ];

            // create Request
            $request_header = new Request();
            $request_header->merge($dataBody);

            // Call fungsi utk create Header
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());
            if ($response_create_header->status) {
                $data_header = $response_create_header->data; // Nonce bisa diambil dari sini $data_header->nonce

                $res_url_mpc = json_decode(Msystem::where('system_type', '=', 'mpc_url')
                    ->where('system_cd', '=', 'auth_token_v2')
                    ->first());
                if ($res_url_mpc->id != null) {
                    $url_mpc = $res_url_mpc->system_value;
                    // HIT MPC API AUTH PAGE
                    // create Request
                    $req_hit_allo = new Request();
                    $req_hit_allo->merge(['mpc_url' => $url_mpc, 'dataHeader' => $data_header, 'dataBody' => $dataBody]);
                    // dd($req_hit_allo);
                    $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
                    // dd($resp_hit_allo);
                    // Cek response Success
                    if ($resp_hit_allo['message'] == 'Success') {
                        // dd($resp_hit_allo);
                        $resp_hit_allo['alloCodeVerifier'] = $verifier;
                        //$resp_hit_allo['alloCodeVerifier'] = $codeChallenge;
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    } else {
                        $error = [
                            'modul' => 'allo_auth_page',
                            'actions' => 'Hit Allo MPC',
                            'error_log' => json_encode($resp_hit_allo),
                            'device' => "0"
                        ];
                        $report = $this->LogController->error_log($error);
                        $response = [
                            'status' => false,
                            'message' => $resp_hit_allo['message'],
                            'code' => 500,
                            'data' => $resp_hit_allo
                        ];
                    }
                } else {
                    $error = [
                        'modul' => 'allo_auth_page',
                        'actions' => 'Hit db THG',
                        'error_log' => $res_url_mpc,
                        'device' => "0"
                    ];
                    $report = $this->LogController->error_log($error);
                    $response = [
                        'status' => false,
                        'message' => "not found record",
                        'code' => 500,
                        'data' => $res_url_mpc
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 500,
                    'data' => $request_header
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'auth_by_token',
                'actions' => 'Hit Allo Auth By Token, Cross BU Jump',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    function decryptData($data)
    {
        $password = 'C3p0tD3wal4';
        $method = 'aes-256-cbc';
        $password = substr(hash('sha256', $password, true), 0, 32);
        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);

        $decrypted = openssl_decrypt(base64_decode("$data"), $method, $password, OPENSSL_RAW_DATA, $iv);

        return $decrypted;
    }

    function encryptData($data)
    {
        $password = 'C3p0tD3wal4';
        $method = 'aes-256-cbc';
        $password = substr(hash('sha256', $password, true), 0, 32);
        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);

        $encrypted = base64_encode(openssl_encrypt(("$data"), $method, $password, OPENSSL_RAW_DATA, $iv));

        return $encrypted;
    }


    /**
     * Function: Test
     * body: 
     *	$request	: 
     */
    public function test(Request $request)
    {
        try {
            $req_hit_wallet_status = new Request();
            $req_hit_wallet_status->merge(['accessToken' => $request['accessToken'], 'refreshToken' => $request['refreshToken']]);
            $req_hit_wallet_status = json_decode($this->wallet_registration_status($req_hit_wallet_status)->getContent());
            if ($req_hit_wallet_status->status) {
                // Construct new accessToken, refreshToken dan pointBalance untuk $resp_hit_allo['data']['responseAllo']
                $resp_hit_allo['data']['responseAllo']['walletStatus'] = $req_hit_wallet_status->data->responseData->registered;
                if ($resp_hit_allo['data']['responseAllo']['walletStatus']) {
                    $req_hit_wallet_bal = new Request();
                    $req_hit_wallet_bal->merge(['accessToken' => $request['accessToken'], 'refreshToken' => $request['refreshToken']]);
                    $req_hit_wallet_bal = json_decode($this->wallet_query_info($req_hit_wallet_bal)->getContent());
                    if ($req_hit_wallet_bal->status) {
                        // Construct new accessToken, refreshToken dan pointBalance untuk $resp_hit_allo['data']['responseAllo']
                        $resp_hit_allo['data']['responseAllo']['walletBalance'] = $req_hit_wallet_bal->data->responseData->balance;
                        $response = [
                            'status' => true,
                            'message' => 'message',
                            'code' => 200,
                            'data' => $resp_hit_allo['data']
                        ];
                    } else {
                        $response = [
                            'status' => false,
                            'message' => $req_hit_wallet_status->message,
                            'code' => 500,
                            'data' => $req_hit_wallet_status->data
                        ];
                    }
                }
                $response = [
                    'status' => true,
                    'message' => 'message',
                    'code' => 200,
                    'data' => $resp_hit_allo['data']
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => $req_hit_wallet_status->message,
                    'code' => 500,
                    'data' => $req_hit_wallet_status->data
                ];
            }
            return response()->json($response, 200);
            $rules = [
                'accessToken' => 'required',
                'refreshToken' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 500,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }

            // create new fresh accessToken dan refreshToken
            //  // $response_refresh_token = $this->refresh_token_helper($request->all());
            //  dd($response_refresh_token);

            //  if($response_refresh_token['status'] && strtoupper($response_refresh_token['message']) === 'SUCCESS')
            //  { 
            //     $newAccessToken = $response_refresh_token['data']->responseData->accessToken;
            //     $newRefreshToken = $response_refresh_token['data']->responseData->refreshToken;
            //  } else {
            //     $newAccessToken = $request['accessToken'];
            //     $newRefreshToken = $request['refreshToken'];
            //  }


            // Construct data
            $dataBody = [
                'requestData' => [
                    // 'accessToken' => $newAccessToken,
                    'accessToken' => $request['accessToken']
                ]
            ];

            // get trxNo
            $trxNo = $this->AlloHelperController->create_transaction_no();
            $dataBody['transactionNo'] = $trxNo;

            // Construct Header untuk di passing ke Create_Header_Allo function, nanti response header with sign key
            $header = $dataBody;
            // create Request
            $request_header = new Request();
            $request_header->merge($header);
            // Call fungsi utk create Header
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());
            if ($response_create_header->status) {
                $data_header = $response_create_header->data;

                // Get url Wallet Registration from table msystems
                // create Request
                // $req_url_mpc = new Request();
                // $req_url_mpc->merge(['system_type' => 'mpc_url' , 'system_cd' => 'wallet_query_info_v2']);
                // $res_url_mpc = $this->AlloHelperController->get_allo_value($req_url_mpc);
                $res_url_mpc = json_decode(Msystem::where('system_type', '=', 'mpc_url')
                    ->where('system_cd', '=', 'wallet_query_info_v2')
                    ->first());
                if ($res_url_mpc->id != null) {
                    // Cek jika data url tidak ada di db
                    //    if(count($res_url_mpc['data']) > 0){
                    $url_mpc = $res_url_mpc->system_value;
                    // HIT MPC API Point Add
                    // create Request
                    $req_hit_allo = new Request();
                    $req_hit_allo->merge(['mpc_url' => $url_mpc, 'dataHeader' => $data_header, 'dataBody' => $dataBody]);
                    $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
                    $resp_hit_allo['accessToken'] = $request['accessToken'];
                    $resp_hit_allo['refreshToken'] = $request['refreshToken'];
                    // $resp_hit_allo['accessToken'] = $newAccessToken;
                    // $resp_hit_allo['refreshToken'] = $newRefreshToken;
                    // Cek response success
                    if (strtoupper($resp_hit_allo['message']) == 'SUCCESS') {
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    } else {
                        $error = [
                            'modul' => 'wallet_query_info',
                            'actions' => 'Hit Allo Wallet Query Info',
                            'error_log' => json_encode($resp_hit_allo),
                            'device' => "0"
                        ];
                        $report = $this->LogController->error_log($error);
                        $response = [
                            'status' => true,
                            'message' => $resp_hit_allo['message'],
                            'code' => 200,
                            'data' => $resp_hit_allo
                        ];
                    }
                    //    } else {
                    //         $response = [
                    //             'status' => false,
                    //             'message' => 'Data empty',
                    //             'code' => 500,
                    //             'data' => $res_url_mpc 
                    //         ];
                    //    }
                } else {
                    $response = [
                        'status' => false,
                        'message' => "Data Empty",
                        'code' => 500,
                        'data' => $res_url_mpc
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 400,
                    'data' => $request_header
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'wallet_query_info',
                'actions' => 'Hit Allo Wallet Query Info',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }

    public function get_allo_explorer_url(Request $request)
    {
        try {
            $trxNo = $this->AlloHelperController->create_transaction_no();
            $verifier = $this->EncryptHelper->create_verifier();
            $codeChallenge = $this->EncryptHelper->hashingVerifier($verifier);
            // Check Request Validation
            $rules = [
                'deviceId' => 'required',
                'accessToken' => 'required'
            ];
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(),
                    'data' => $request->all(),

                ];
                return response()->json($response, 200);
            }

            // Construct default body param - default utk WEB
            $dataBody = [
                'transactionNo' => $trxNo,
                'requestData' => [
                    'grantType' => 'CODE',
                    'accessToken' => $request->accessToken,
                    'codeChallenge' => $codeChallenge,
                    'codeChallengeMethod' => 'SHA256',
                    "osType" => $request->osType ?? "WEB",
                    "deviceId" => $request->deviceId,
                ]
            ];

            // create Request
            $request_header = new Request();
            $request_header->merge($dataBody);
            $response_create_header = json_decode($this->create_header_allo($request_header)->getContent());

            if (!$response_create_header->status) {
                $response = [
                    'status' => false,
                    'message' => $response_create_header->message,
                    'code' => 500,
                    'data' => $request_header
                ];
                return response()->json($response, 200);
            }

            $data_header = $response_create_header->data; // Nonce bisa diambil dari sini $data_header->nonce
            $tmpHeader = (array) $data_header;
            array_walk($tmpHeader, function (&$a, $b) {
                $a = "$b: $a";
            });
            $data_header = array_values($tmpHeader);

            // create Request
            $req_hit_allo = new Request();
            $res_url_mpc = json_decode(Msystem::where('system_type', '=', 'mpc_url')
                ->where('system_cd', '=', 'get_allo_explorer_url')
                ->first());

            $url_mpc = $res_url_mpc->system_value;
            $req_hit_allo->merge([
                'dataHeader' => $data_header,
                'dataBody' => $dataBody,
                'mpc_url' => $url_mpc,
            ]);

            $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
            $resp_hit_allo['responseData']['alloExplorerUrl'] .= "&codeVerifier=" . $verifier;
            // Cek response Success
            if ($resp_hit_allo['message'] == 'Success') {
                $response = [
                    'status' => true,
                    'message' => $resp_hit_allo['message'],
                    'code' => 200,
                    'data' => $resp_hit_allo
                ];
            } else {
                $error = [
                    'modul' => 'allo_auth_page',
                    'actions' => 'Hit Allo MPC',
                    'error_log' => $resp_hit_allo,
                    'device' => "0"
                ];
                $report = $this->LogController->error_log($error);
                $response = [
                    'status' => false,
                    'message' => $resp_hit_allo['message'],
                    'code' => 500,
                    'data' => $resp_hit_allo
                ];
            }

            return response()->json($response, 200);
        } catch (\Throwable $e) {
            $error = [
                'modul' => 'allo_auth_page',
                'actions' => 'Hit Allo Auth Page',
                'error_log' => $e,
                'device' => "0"
            ];
            $report = $this->LogController->error_log($error);
            report($e);
            $response = [
                'status' => false,
                'message' => 'Error',
                'code' => 500,
                'data' => null,
            ];
            return response()->json($response, 500);
        }
    }
}
