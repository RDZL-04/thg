<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Members;
use App\Models\Msystem;
use File;
use Image;
use App\Http\Controllers\Helpers\AlloHelperController;
use App\Http\Controllers\Helpers\EncryptHelper;
use App\Http\Controllers\LOG\ErorrLogController;
use Illuminate\Support\Facades\Redis;

class MemberController extends Controller
{
    public function __construct() {
        // set API URL dari APP_ENV
        if(env('APP_ENV') == 'local'){
            $this->url_redirect = env('APP_REDIRECT_URL');
            $this->url_redirect_mobile = env('APP_URL');
        } elseif(env('APP_ENV') == 'dev') {
            $this->url_redirect = env('APP_REDIRECT_URL_DEV');
            $this->url_redirect_mobile = env('APP_URL_DEV');
        } elseif(env('APP_ENV') == 'prod') {
            $this->url_redirect = env('APP_REDIRECT_URL_PROD');
            $this->url_redirect_mobile = env('APP_URL_PROD');
        }
        $this->AlloHelperController = new AlloHelperController;
        $this->EncryptHelper = new EncryptHelper;
        $this->LogController = new ErorrLogController;
    }

    /**
	 * Function: add data Member from Allo response after Auth_token or query_profile
	 * body: data member
	 *	$request	: 
	*/
    public function save_member(Request $request)
    {
        try{
            //validasi data request
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|max:100|',
                'email' => 'required|email',
                'phone' => 'required|numeric|digits_between:10,13',
                'mdcid' => 'required',
                'deviceId' => 'required'
            ]);

            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(), 
                    'data' =>null,
                    
                ];
                return response()->json($response, 200);
            }
            $check_member = Members::where('mdcid', '=', $request->mdcid)
                ->withTrashed()
                ->get();
                
            if(count($check_member) > 0) {
                // Update Member
                $save = Members::save_member($request->all());
            } else {
                // Add New Member
                $save = Members::add_member($request->all());
            }
            if($save) {
                $data = Members::where('phone', '=', $request->phone)
                    ->where('email', '=', $request->email)
                    ->where('mdcid', '=', $request->mdcid)
                    ->withTrashed()
                    ->get();

                $response = [
                    'status' => true,
                    'message' => __('message.data_saved_success'),
                    'code' => 200,
                    'data' =>$data,
                ];
            } else {
                $response = [
                        'status' => false,
                        'message' => __('message.failed_save_data'),
                        'code' => 200,
                        'data' => null, 
                ];
                
            }
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'add_member',
                'actions' => 'save data member',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    /**
	 * Function: get Data Member dari db thg
	 * body: data member
	 *	$request	: 
	*/
    public function get_member(Request $request)
    {
        try{
            //validasi data request
            $validator = Validator::make($request->all(), [
                // 'email' => 'required|email|unique:users,deleted_at,null'
                'phone' => 'required|numeric|digits_between:10,13'
            ]);

            if ($validator->fails()) {
                // return validasi gagal
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(), 
                    'data' =>null,
                    
                ];
                return response()->json($response, 200);
            }
            $check_member = Members::where('phone', '=', $request->phone)
                                    ->get();
            if(count($check_member) > 0) {
                // Update Member
                $response = [
                    'status' => true,
                    'message' => __('message.data_found'),
                    'code' => 200,
                    'data' =>$check_member,
                    ];
            }
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_member',
                'actions' => 'get Data Members',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }


    /**
	 * Function: get Data Member dari db thg
	 * body: data member
	 *	$request	: 
	*/
    public function get_member_id(Request $request)
    {
        try{
            //validasi data request
            $validator = Validator::make($request->all(), [
                // 'email' => 'required|email|unique:users,deleted_at,null'
                'id' => 'required'
            ]);

            if ($validator->fails()) {
                // return validasi gagal
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(), 
                    'data' =>null,
                    
                ];
                return response()->json($response, 200);
            }
            // $check_member = Members::get_profile($request->id);
            $check_member = Members::where('id', $request->id)
                                    ->first();
            // dd($check_member);
            if($check_member != null) {
                // if($check_member['gender'] != null){
                //     $list_gender = Msystem::get_gender();
                //     foreach($list_gender as $gender){
                //         if($gender['system_cd'] == $check_member['gender']){
                //             $check_member['gender'] = $gender['system_value'];
                //         }
                //     }
                // }
                // Update Member
                $response = [
                    'status' => true,
                    'message' => __('message.data_found'),
                    'code' => 200,
                    'data' =>$check_member,
                    ];
            }
            else{
                $response = [
                    'status' => false,
                    'message' => __('message.data_not_found'),
                    'code' => 400,
                    'data' =>null,
                    ];
            }
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_member',
                'actions' => 'get Data Members',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    /**
	 * Function: Edit data Member 
	 * body: data member
	 *	$request	: 
	*/
    public function edit_member(Request $request)
    {
        try{
            //validasi data request
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                // 'date_of_birth' => 'date',
                'id_type' => 'max:1',
                'id_no' => 'max:25',
                'gender' => 'max:1',
                'city' => 'max:100',
                'country' => 'max:3',
                'state_province' => 'max:100',
                'postal_cd' => 'max:5',
                'address' => 'max:250',
                'accessToken' => 'required',
                'refreshToken' => 'required'
             ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(), 
                    'data' =>null,
                    
                ];
                return response()->json($response, 200);
            }
            // edit : arka.moharifrifai 2022-06-03
            $mdcid = null;
            $accessToken = $request->accessToken;
            $refreshToken = $request->refreshToken;
            $response_refresh_token = $this->get_mdcid($request);
            // Cek response success
            if(strtoupper($response_refresh_token['message']) == 'SUCCESS') {
                $response_refresh_token = (array)$response_refresh_token['data'];
                $mdcid = $response_refresh_token['mdcId'];
                $accessToken = $response_refresh_token['accessToken'];
                $refreshToken = $response_refresh_token['refreshToken'];
            }  else {
                $error = [
                    'modul' => 'edit_member',
                    'actions' => 'Hit Edit Member Profile API',
                    'error_log' => json_encode($response_refresh_token),
                    'device' => "0" 
                    ];
                $report = $this->LogController->error_log($error);
                $response = [
                    'status' => false,
                    'message' => $response_refresh_token['message'],
                    'code' => 400,
                    'data' => $response_refresh_token
                ];
                return response()->json($response, 400);
            }
            // edit : arka.moharifrifai 2022-06-03
            if(!empty($request->date_of_birth) || $request->date_of_birth != null){
                $validator = Validator::make($request->all(), [
                    'date_of_birth' => 'date',
                 ]);
    
                if ($validator->fails()) {
                    // return response gagal
                    $response = [
                        'status' => false,
                        'code' => 400,
                        'message' => $validator->errors()->first(), 
                        'data' =>null,
                        
                    ];
                    return response()->json($response, 200);
                }
            }
            $check_member = Members::where('id', '=', $request->id)
                                    ->where('mdcid', '=', $mdcid)
                                    ->first();
            if($check_member != null) {
                $data = $request->all();
                if(!empty($request->id_no)){
                    if(empty($request->id_type)){
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => 'id_type cannot be null', 
                            'data' =>null,
                            
                        ];
                        return response()->json($response, 200);
                    }
                }
                if(!empty($request->image)){
                    $validator = Validator::make($request->all(), [
                        'image' => 'image|mimes:jpeg,jpg,png|max:2048',
                     ]);
        
                    if ($validator->fails()) {
                        $path   = $request->file('image');
                        $images = Image::make($path)->fit(400)->encode('jpeg', 30);
                        // dd($images);
                        $fileName = time().'.jpg';
                        $loc = public_path('user-images');
                        // dd($loc.'\\'.$fileName);
                        $loct = $images->save($loc.'\\'.$fileName);
                        $data['image'] = 'user-images/'.$fileName;
                        if($check_member['image']!= null){
                            $file_path = public_path($check_member['image']); 
                            //if file found delete image
                            if(File::exists($file_path)) File::delete($file_path);
                        }
                        // return response gagal
                        // $response = [
                        //     'status' => false,
                        //     'code' => 400,
                        //     'message' => $validator->errors()->first(), 
                        //     'data' =>null,
                            
                        // ];
                        // return response()->json($response, 200);
                    } else {
                        if($check_member['image']!= null){
                            $file_path = public_path($check_member['image']); 
                            //if file found delete image
                            if(File::exists($file_path)) File::delete($file_path);
                        }
                        $images = $request->file('image');
                        $fileName = time().'.'.$images->extension();  
                        $loc = public_path('user-images');
                        $loct = $images->move($loc, $fileName);
                        $data['image'] = 'user-images/'.$fileName;
                    }
                }
                
                
                    
                if(empty($data['date_of_birth'])){
                    $data['date_of_birth'] = null;
                }
                if(empty($data['id_type'])){
                    $data['id_type'] = null;
                }
                if(empty($data['id_no'])){
                    $data['id_no'] = null;
                }
                if(empty($data['gender'])){
                    $data['gender'] = null;
                }
                if(empty($data['image']) || $data['image'] == null){
                    $data['image'] = $check_member['image'];
                }
                if(empty($data['city'])){
                    $data['city'] = null;
                }
                if(empty($data['country'])){
                    $data['country'] = null;
                }
                if(empty($data['state_province'])){
                    $data['state_province'] = null;
                }
                if(empty($data['postal_cd'])){
                    $data['postal_cd'] = null;
                }
                if(empty($data['address'])){
                    $data['address'] = null;
                }
                // dd($data);
                // Update Member
                $save = Members::edit_member($data);
                if($save){
                    $data = Members::where('id', '=', $request->id)
                    ->first();
                    // $data = Members::get_profile($request->id);
                    $data['accessToken'] = $accessToken;
                    $data['refreshToken'] = $refreshToken;
                    $response = [
                        'status' => true,
                        'message' => __('message.data_saved_success'),
                        'code' => 200,
                        'data' =>$data,
                        ];
                }
                else {
                    $data['accessToken'] = $accessToken;
                    $data['refreshToken'] = $refreshToken;
                    $response = [
                        'status' => false,
                        'message' => __('message.failed_save_data'),
                        'code' => 200,
                        'data' =>$data,
                    ];
                }
            } else {
                $data['accessToken'] = $accessToken;
                $data['refreshToken'] = $refreshToken;
                $response = [
                    'status' => false,
                    'message' => __('message.failed_save_data'),
                    'code' => 200,
                    'data' =>$data,
                ];
            }
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'add_member',
                'actions' => 'save data member',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $data['accessToken'] = $accessToken;
            $data['refreshToken'] = $refreshToken;
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' =>$data,
            ];
            return response()->json($response, 500);
        }
    }

    public function update_status_notif(Request $request){
        try
        {
            $validator = Validator::make($request->all(), [
                'transaction_no' => 'required',
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            $reservation = Reservation::get_reservation_email($request->all());
            if($reservation == null){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }else{
                $guest = Guest::where('id', $reservation['customer_id'])
                                ->first();
                $faicility = HotelFacility::get_facility($reservation['hotel_id']);
                $faicility_hotel = ['hotel_facility' => $faicility];
                if(count($faicility)==0){
                    $faicility_hotel = ['hotel_facility' => null];
                }  
                // dd($guest);
                // dd($faicility_hotel);
                $subjectPic= Msystem::Where('system_type','email_notification')
                                 ->Where('system_cd','hotel')
                                    ->first();
                $subjectGuest= Msystem::Where('system_type','email_notification')
                                 ->Where('system_cd','guest')
                                    ->first();
                // dd($subjectPic['system_value']);
                // dd();
                $reservation = json_decode(json_encode($reservation), true); 
                $reservation = $reservation + $faicility_hotel;
                $subjectHotel = str_replace("confirmation_no",$reservation['be_uniqueId'],$subjectPic['system_value']);
                $data = ['reservation' => $reservation,
                'guest' => $guest,
                'subjectpic' => $subjectHotel,
                'subjectGuest' => $subjectGuest['system_value']
                ];
                // dd($reservation['email_notification']);
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.email_success' ),
                    'data' => $data,
                ];
                $SendMailUser = Mail::to($guest->email)->send(new SendMailReservation($data));
                $SendMailPic = Mail::to($reservation['email_notification'])->send(new SendMailReservationPic($data));
                return response()->json($response, 200);    
                
            }
        }
        catch (Throwable $e) 
        {
            report($e);
            $error = ['modul' => 'send_mail_reservation',
                'actions' => 'send data mail reservation',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
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
        
    public function get_mdcid($request)
    {
        // create new fresh accessToken dan refreshToken
        $response_refresh_token = $this->allo_refresh_token_helper($request);
        if($response_refresh_token['status'] && strtoupper($response_refresh_token['message']) === 'SUCCESS')
        { 
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
        $response_create_header = json_decode($this->allo_create_header_allo($request_header)->getContent());
        if($response_create_header->status){
            $data_header = $response_create_header->data;

            // Get url MPC Member_Existence from table msystems
            // create Request
            // $req_url_mpc = new Request();
            // $req_url_mpc->merge(['system_type' => 'mpc_url' , 'system_cd' => 'member_profile_v2']);
            // $res_url_mpc = $this->AlloHelperController->get_allo_value($req_url_mpc);
            $res_url_mpc = json_decode(Msystem::where('system_type','=','mpc_url')
                            ->where('system_cd', '=', 'member_profile_v2')
                            ->first());
            if($res_url_mpc->id != null){
                // Cek jika data url tidak ada di db
                //    if(count($res_url_mpc['data']) > 0){
                    $url_mpc = $res_url_mpc->system_value;
                    // HIT MPC API Member Existence
                    // create Request
                    $req_hit_allo = new Request();
                    $req_hit_allo->merge(['mpc_url' => $url_mpc,'dataHeader' => $data_header , 'dataBody' => $dataBody]);
                    $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
            
                    // Cek response success
                    if(strtoupper($resp_hit_allo['message']) == 'SUCCESS') {
                        $resp_hit_allo = (array)$resp_hit_allo['responseData'];
                        $response = [
                            'status' => true,
                            'message' => 'SUCCESS',
                            'code' => 200,
                            'data' => [
                                "mdcId" => $resp_hit_allo['mdcId']
                                , 'accessToken' => $newAccessToken
                                , 'refreshToken' => $newRefreshToken
                            ]
                        ];
                    }  else {
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
        return $response;
    }
    public function allo_create_header_allo(Request $request)
    {
        try{
            // Cek jika osType ada
            if(!empty($request->requestData['osType'])){
                $system_cd = 'appIDMobile';
                $system_cd_app_secret = 'appSecretMobile';
            } else {
                $system_cd = 'appIDWeb';
                $system_cd_app_secret = 'appSecretWeb';
            }
            $body = $request->all();
            // Get Allo APP_ID dan APP_SECRET
            // $request = new Request();
            // $request->merge(['system_type' => 'allo', 'system_cd' => $system_cd]);
            // $response_get_app_id = $this->AlloHelperController->get_allo_value($request);
            $response_get_app_id = json_decode(Msystem::where('system_type','=','allo')
                                    ->where('system_cd', '=', $system_cd)
                                    ->first());
            // dd($response_get_app_id->system_value);
            // $request = new Request();
            // $request->merge(['system_type' => 'allo', 'system_cd' => 'appSecret']);
            // $request->merge(['system_type' => 'allo', 'system_cd' => $system_cd_app_secret]);
            // $response_get_app_secret = $this->AlloHelperController->get_allo_value($request);
            $response_get_app_secret = json_decode(Msystem::where('system_type','=','allo')
            ->where('system_cd', '=', $system_cd_app_secret)
            ->first());
            // dd($response_get_app_secret->system_value);
            $now = round(microtime(true) * 1000);
            $header = [
                'nonce' => strval(floor($this->EncryptHelper->random_0_1() * 100000000)), 
                'timestamp'=> $now,
                'Content-Type'=> 'application/json', 
                // 'appId'=> $response_get_app_id['data'][0]['system_value']
                'appId'=> $response_get_app_id->system_value
            ];

            $body_str = json_encode($body);
            // construct Array
            // $arr = [$header['appId'], $header['nonce'], $header['timestamp'], $response_get_app_secret['data'][0]['system_value'], $body_str];
            $arr = [$header['appId'], $header['nonce'], $header['timestamp'], $response_get_app_secret->system_value , $body_str];
            // Sorting sesuai ASCII
            asort($arr,2);
            // Concat array
            $data = join('', $arr);

            // Create Hashing sha256 dan convert Hex to Bin
            $obj = $this->EncryptHelper->hashing($data);
            $obj = $this->EncryptHelper->to_str($obj);

            // Load Private Key file
            $path = storage_path('app/key/private.key');
            $kh = openssl_pkey_get_private(file_get_contents($path));

            // Encrypt Object Data using private key
            $encrypted = openssl_private_encrypt($obj,$crypttext,$kh);          
            if($encrypted){
                // add sign key to Header array
                $header['sign'] = $this->EncryptHelper->to_hex($crypttext);
                $response = [
                    'status' => true,
                    'message' => 'Successfull Encrypted',
                    'code' => 200,
                    'data' => $header
                    ];
                
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Unsuccessfull',
                    'code' => 200,
                    'data' => $header,
                ];
                
            }
            return response()->json($response, 200);
        }
            catch (Throwable $e) {
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

    public function allo_refresh_token_helper($request)
    {
        try{
            // Construct dataBody untuk Hit api Refresh Token
            $dataBodyRefreshToken = [
                'accessToken' => $request['accessToken'],
                'refreshToken' => $request['refreshToken']
            ];
            $request_refresh_token = new Request();
            $request_refresh_token->merge($dataBodyRefreshToken);
            //HIT Allo API Resfresh Token utk mendapatkan Pair accessToken dan refreshToken yg baru
            // $response_refresh_token = (array)json_decode($this->refresh_token($request_refresh_token)->getContent());
            return (array)json_decode($this->allo_refresh_token($request_refresh_token)->getContent());

            }
            catch (Throwable $e) {
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
    
    public function allo_refresh_token(Request $request)
    {
        try {
            $rules = [
                'accessToken' => 'required',
                'refreshToken' => 'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) 
            {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 500,
                    'message' => $validator->errors()->first(), 
                    'data' =>$request->all(),
                    
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
            if($jwtPayload->exp > $dateNow) {
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
             $response_create_header = json_decode($this->allo_create_header_allo($request_header)->getContent());
             if($response_create_header->status){
                $data_header = $response_create_header->data;

                // Get url MPC Refresh_Token from table msystems
                // create Request
                // $req_url_mpc = new Request();
                // $req_url_mpc->merge(['system_type' => 'mpc_url' , 'system_cd' => 'refresh_token_v2']);
                // $res_url_mpc = $this->AlloHelperController->get_allo_value($req_url_mpc);
                $res_url_mpc = json_decode(Msystem::where('system_type','=','mpc_url')
                                ->where('system_cd', '=', 'refresh_token_v2')
                                ->first());
                if($res_url_mpc->id != null){
                       // Cek jika data url tidak ada di db
                    //    if(count($res_url_mpc['data']) > 0){
                            $url_mpc = $res_url_mpc->system_value;
                            // HIT MPC API Refresh Token
                            // create Request
                            $req_hit_allo = new Request();
                            $req_hit_allo->merge(['mpc_url' => $url_mpc,'dataHeader' => $data_header , 'dataBody' => $dataBody]);
                            $resp_hit_allo = $this->AlloHelperController->mpc_hit_api($req_hit_allo);
                            // Cek response success
                            if(strtoupper($resp_hit_allo['message']) == 'SUCCESS') { 
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
        } catch(Throwable $e) {
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
     * Function: Check Member Device ID
     * body: 
     *	$request	: 
    */
        
    public function check_member_device_id(Request $request)
    {
        //validasi data request
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'deviceId' => 'required'
        ]);

        if ($validator->fails()) {
            // return response gagal
            $response = [
                'status' => false,
                'code' => 400,
                'message' => $validator->errors()->first(), 
                'data' =>null,
                
            ];
            return response()->json($response, 200);
        }

        $check_member = Members::where('id', '=', $request->id_user)
            ->where('device_id', '=', $request->deviceId)
            ->withTrashed()
            ->get();
            
        if(count($check_member) > 0) {
            // Update Member
            $response = [
                'status' => true,
                'message' => __('message.data_found'),
                'code' => 200,
                'data' =>$check_member,
            ];
        } else {
            $response = [
                'status' => false,
                'message' => __('message.data_not_found'),
                'code' => 200,
                'data' => null, 
            ];
        }
        return response()->json($response, 200);
    }

}
