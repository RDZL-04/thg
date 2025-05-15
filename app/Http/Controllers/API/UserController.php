<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserSso;
use App\Models\HotelUser;
use App\Models\Registration;
use App\Models\OTP;
use App\Models\MasterRole;
use App\Models\OutletUsers;
use App\Models\OutletImages;
use App\Models\ForgotPassword;
use App\Models\ForgotPasswordCnt;
use App\Models\PersonalTokens;
use App\Models\Notification;
use App\Models\Reservation;
use App\Models\OrderDining;
use App\Mail\SendMail;
use App\Mail\VerifyEmail;
use App\Models\Msystem;
use App\Http\Controllers\LOG\ErorrLogController;
use App\Http\Controllers\Helpers\EncryptHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Validator;
use Crypt;
use Auth;
use LanguageSwitcher;
use Carbon\Carbon;
/*
|--------------------------------------------------------------------------
| User API Controller
|--------------------------------------------------------------------------
|
| Validate, Authorize user.
| This user controller will control user data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: November 20, 2020
*/


class UserController extends Controller
{
    

    // public function __construct(\Illuminate\Http\Request $request)
    // {
    //     $this->request = $request;
    // }
    public function __construct() {
        $this->LogController = new ErorrLogController;
        $this->EncryptHelper = new EncryptHelper;
    }

    /*
	 * Function: Login for web
	 * Param: 
	 *	$request	: array[email, password, device_name]
	 */
    public function login(Request $request)
    {
        //validasi request
        $validator = Validator::make($request->all(), [
            'email' => 'required|min:8',
            'password' => 'required|min:8|max:15|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',
            'device' => 'required',
        ]);
        if ($validator->fails()) {
            // return response gagal
            $error = json_decode($validator->errors());
            if(!empty($error->email)){
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => 'Email or username cannot be empty, Password must be more than 8 characters long',
                    'data' =>null,
                    
                ];
                return response()->json($response, 200);
            }
            $response = [
                'status' => false,
                'code' => 404,
                'message' => $validator->errors()->first(), 
                'data' => [],
            ];
            return response()->json($response, 200);
        }
        try{
        $user = User::where('email', $request->email)->first();
        $username = User::CheckUser($request->email);
        if(!empty($user)){
            $user = $user;
        }elseif(!empty($username)){
            $user = $username;
        }
        else{
            $response = [
                'status'   => false,
                'code' => 200,
                'message' => __('message.login_failed'),
                'data'     => null,
            ];
            return response()->json($response, 200);
        }
        
        $role = MasterRole::where('id', $user['id_role'])->first();
        $allRole = MasterRole::get_role();
        $allRole = json_decode($allRole);
            if (!$user|| ! Hash::check($request->password, $user->password)) {
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => __('message.login_failed'),
                    'data' => $request->email,
                ];
                return response()->json($response, 200);
            }    
        else if( (strpos(strtolower($role['role_nm']),'admin') !== false) || (strpos(strtolower($role['role_nm']),'hotel') !== false) ||(strpos(strtolower($role['role_nm']),'cashier') !== false) || (strpos(strtolower($role['role_nm']),'waiter') !== false) ) {
            //if user not null create token for login
        // Revoke all tokens...
        $user->tokens()->delete();
        // Revoke a specific token...
        $token = $user->createToken($request->device)->plainTextToken;
            if($token){
                $response = [
                    'status'   => true,
                    'code' => 200,
                    'message' => __('message.login_success'),
                    'data'     => [
                                'id'=> $user['id'],
                                'full_name' => $user['full_name'],
                                'phone' => $user['phone'],
                                'email' => $user['email'],
                                'id_role' => $user['id_role'],
                                'nm_role' => $role['role_nm'],
                                'image' => $user['image'],
                                'token' => $token
                            ],
                ];
                return response()->json($response, 200);
            }
            $response = [
                'status'   => false,
                'code' => 200,
                'message' => __('message.login_failed'),
                'data'     => [
                            'email' => $request->email
                        ],
            ];
            return response()->json($response, 200);
        
        }
        $response = [
            'status' => false,
            'code' => 404,
            'message' => __('message.login_failed'),
            'data' => $request->email,
        ];
        return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'login',
                'actions' => 'login_master_web',
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


    /*
	 * Function: Login for web
	 * Param: 
	 *	$request	: array[email, password, device_name]
	 */
    public function login_outlet(Request $request)
    {
        try{
         //validasi request
            $validator = Validator::make($request->all(), [
                'password' => 'required|min:8|max:15|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',
                'device' => 'required',
                'token' => 'required'
            ]);
            $validatorEmail = Validator::make($request->all(), [
                'email' => 'required|min:8',
                'password' => 'required|min:8|max:15|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',
                'device' => 'required',
                'token' => 'required'
            ]);
            $validatorUsername = Validator::make($request->all(), [
                'username' => 'required|min:8',
                'password' => 'required|min:8|max:15|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',
                'device' => 'required',
                'token' => 'required'
            ]);
            if(!empty($request->username)){
                if ($validatorUsername->fails()) {
                    $error = json_decode($validator->errors());
                    $response = [
                        'status' => false,
                        'code' => 400,
                        'message' => $validator->errors()->first(), 
                        'data' => [],
                    ];
                    return response()->json($response, 200);
                }
            }
            else if(!empty($request->email)){
                if ($validatorEmail->fails()) {
                    $error = json_decode($validator->errors());
                    $response = [
                        'status' => false,
                        'code' => 400,
                        'message' => $validator->errors()->first(), 
                        'data' => [],
                    ];
                    return response()->json($response, 200);
                }
            }
            else{
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => 'Email or username cannot be empty',
                    'data' =>null,
                    
                ];
                return response()->json($response, 200);
            }
            if ($validator->fails()) {
                // return response gagal
                $error = json_decode($validator->errors());
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(), 
                    'data' => [],
                ];
                return response()->json($response, 200);
            }
        $user = User::where('email', $request->email)->first();
        // dd($request->email);
        $username = User::CheckUser($request->username);
        // dd($username);
        if(!empty($user)){
            $user = $user;
            // dd($data_user);
        }elseif(!empty($username)){
            $user = $username;
        }
        else{
            $response = [
                'status'   => false,
                'code' => 400,
                'message' => __('message.login_failed'),
                'data'     => null,
            ];
            return response()->json($response, 200);
        }
        // dd($data_user);
        $role = MasterRole::where('id', $user['id_role'])->first();
        $allRole = MasterRole::get_role();
        $allRole = json_decode($allRole);
        // dd($user);
            if (!$user|| ! Hash::check($request->password, $user->password)) {
                $response = [
                    'status' => false,
                    'code' => 401,
                    'message' => __('message.login_failed'),
                    'data' => $request->email,
                ];
                return response()->json($response, 200);
            }
            
        // elseif($user['id_role'] == 1){
        else if( (strpos(strtolower($role['role_nm']),'cashier') !== false) || (strpos(strtolower($role['role_nm']),'waiter') !== false) ) {
            //if user not null create token for login
        // Revoke all tokens...
            $check_user_outlet = OutletUsers::get_outlet_user_id($user['id']);
            // dd($check_user_outlet);
            
            // dd($check_user_outlet);
            if(count($check_user_outlet) == 0){
                $response = [
                    'status' => false,
                    'code' => 401,
                    'message' => __('message.login_failed'),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }

        //cek token
        // dd($user['id']);
        // $check_token = PersonalTokens::Where('tokenable_id',$user['id'])->first();
        // // dd($check_token);
        // if($check_token != null){
        //     $response = [
        //         'status' => false,
        //         'code' => 401,
        //         'message' => __('message.account_used'),
        //         'data' => null,
        //     ];
        //     return response()->json($response, 200);
        // }
            // dd($user->tokens());
        $user->tokens()->delete();
        // Revoke a specific token...
        $user['token'] = $request['token'];
        // dd($user['id']);
        $update_token = User::update_token($user);
        // dd();
        $token = $user->createToken($request->device)->plainTextToken;
        $data_outlet=[];
        foreach ($check_user_outlet as $outlet_user){
            $outlet = $outlet_user;
            $image_outlet = OutletImages::Where('fboutlet_id',$outlet_user['fboutlet_id'])->first();
            if($image_outlet == null){
                $outlet['outlet_images'] = null;
            }else{
                $outlet['outlet_images'] = $image_outlet['filename'];
            }            
            array_push($data_outlet,$outlet);
        }
        // $image_outlet = OutletImages::Where('fboutlet_id',$check_user_outlet['fboutlet_id'])->get();
        // $check_user_outlet['outlet_images'] = $image_outlet;
        // array_push($check_user_outlet['outlet_images'], $image_outlet);
        // dd($image_outlet);
            if($token){
                $response = [
                    'status'   => true,
                    'code' => 200,
                    'message' => __('message.login_success'),
                    'data'     => [
                                'id'=> $user['id'],
                                'full_name' => $user['full_name'],
                                'phone' => $user['phone'],
                                'email' => $user['email'],
                                'id_role' => $user['id_role'],
                                'nm_role' => $role['role_nm'],
                                'date_of_birth' => $user['date_of_birth'],
                                'gender' => $user['gender'],
                                'address' => $user['address'],
                                'city' => $user['city'],
                                'country' => $user['country'],
                                'state_province' => $user['state_province'],
                                'postal_cd' => $user['postal_cd'],
                                'date_of_birth' => $user['date_of_birth'],
                                'image' => $user['image'], 
                                'outlet' => $data_outlet,
                                'token' => $token,
                                'token_firebase' => $user['token']
                            ],
                ];
                return response()->json($response, 200);
            }
            $response = [
                'status'   => false,
                'code' => 200,
                'message' => __('message.login_failed'),
                'data'     => [
                            'email' => $request->email
                        ],
            ];
            return response()->json($response, 200);
        
        }
        $response = [
            'status' => false,
            'code' => 404,
            'message' => __('message.login_failed'),
            'data' => $request->email,
        ];
        return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'login_outlet',
                'actions' => 'login_master_outlet',
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

    /*
	 * Function: logout account
	 * Param: 
	 * header:	token 
     * header : Authorization : Bearer
	 **/
    public function logout_user_outlet()
    {
        try{
            $user = request()->user(); //or Auth::user()
            $user['token'] = null;
            // dd($user['id']);
            $update_token = User::update_token($user);
            $unselectOutlet = OutletUsers::unactivated_outlet($user['id']);
            // Revoke current user token
            $user->tokens()->delete();
            return response()->json([
                    'status'    => true,
                    'message' => __('message.logout_success'),
                    'code' => 200,
                    'data' => null,                     
                ], 200);
        }
        catch (Throwable $e) {
            report($e);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    /*
	 * Function: Switch Language
	 * Param: 
	 *	$request	: array[language :]
	 */
    public function switchlang(Request $request)
    {        
            //validasi request
            $validator = Validator::make($request->all(), [
                'language' => 'required',
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(), 
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
        try{
            //switch language user
            $language = $request['language'];
            LanguageSwitcher::setLanguage($language);
            //input data to session
            // session()->put('language',$language);
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.switch_lang'),
                'data' => array('language'=>$language),
            ];
            return $response;      
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'switchlang',
                'actions' => 'switch lang',
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

    /*  
	 * Function: check phone number before login
	 * Param: 
	 *	$request	: array
	 */
    public function check_phone(Request $request)
    {
            //validasi request
            $validator = Validator::make($request->all(), [
                'phone' => 'required|numeric|digits_between:10,13'
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
        try{
            /*
            * check pnone number user from table user
            * if the phone number matches the user table send response true and data id, phone
            * if phone number not match send response false
            */
            $check_user = User::wherePhone($request->phone)->first();
            if($check_user!=null){
                $data = [
                        'phone' => $check_user['phone'],
                    ];
                $response = [
                            'status' => true,
                            'code' => 200,
                            'message' => __('message.phone_exist'),
                            'data' => [
                                'id' => $check_user['id'],
                                'phone' => $check_user['phone']
                            ],
                        ];
                    return response()->json($response, 200);
            }else{
            $response = [
                            'status' => false,
                            'code' => 200,
                            'message' => __('message.email_phone_not_exist'),
                            'data' => [
                                'id' => null,
                                'phone' => $request->phone, 
                            ],
                            
                        ];
                    return response()->json($response, 200);
            }
        }   
        catch (Throwable $e) {
            report($e);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    /*
	 * Function: check password user before login
	 * body: 
	 *	$request	: array[phone, password]
	 */
    public function check_password(Request $request)
    {
            //validation request
            $validator = Validator::make($request->all(), [
                'phone' => 'required|numeric|digits_between:10,13',
                'password' => 'required|min:8|max:15|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',   
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(), 
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
        try{
            /* check phone from table users
            *if phone number and password match table user then login success and create otp
            *sementara otp bikin makmnual dikirim via email
            */
            $user= User::where('phone', $request->phone)->first();
                if (!$user|| ! Hash::check($request->password, $user->password)) {
                    $response = [
                        'status' => false,
                        'code' => 409,
                        'message' => __('message.login_failed'),
                        'data' => null,
                    ];
                    return response()->json($response, 200);
                }
                $otp = rand(100000,999999);
                // $otp = 123456;
                $data = ['id' => $user['id'],
                        'otp' => $otp];
                /*
                *if user number and password match
                *insert otp number to tables otps for match login
                *call api otp
                */
                $create_otp = OTP::create_otp($data);
                if($create_otp)
                {
                    //send data user
                    $response = [
                                'status'   => true,
                                'code' => 200,
                                'message' => __('message.login_success'),
                                'data'     => [
                                            'id' => $user['id'],
                                            'full_name' => $user['full_name'],
                                            'phone' => $user['phone'],
                                            'email' => $user['email'],
                                        ],
                            ];
                            return response()->json($response, 200);
                }
        }
        catch (Throwable $e) {
            report($e);
            $response = [
                    'status' => false,
                    'message' => __('message.internal_erorr'),
                    'code' => 500,
                    'data' => null, 
                ];
                return response()->json($response, 500);
        }
    }

    /*
	 * Function: check otp login
	 * Param: 
	 *	$request	: array[email, password, device_name]
	 */
    public function check_otp(Request $request)
    {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'otp' => 'required|numeric',
                'device_name' => 'required',
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
        try{
            /*
            *check id user and OTP user
            *if user not null create token
            * if user null send response failed
            */
            $user= User::where('id', $request->id)->first();
            $otp = OTP::check_otp($request);
            if($otp!=null && $user != null){
                // Revoke all tokens...
                $user->tokens()->delete();
                // Revoke a specific token...   
                $token = $user->createToken($request->device_name)->plainTextToken;
                    if($token){
                        $user = json_decode(json_encode($user), true); 
                        $user = $user + ['token' => $token];
                        $response = [
                            'status'   => true,
                            'code' => 200,
                            'message' => __('message.login_success'),
                            // 'data'     => [
                            //             'token' => $token,
                            //             'id' => $user['id'],
                            //             'full_name' => $user['full_name'],
                            //             'phone' => $user['phone'],
                            //             'email' => $user['email'],
                            //         ],
                            'data' => $user
                        ];
                        return response()->json($response, 200);
                    }
            }
            //send respon if login failed
            $response = [
                'status' => false,
                'message' => __('message.login_failed'),
                'data' =>  [
                        'id' => $request->id
                    ],         
            ];
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    
    /*
	 * Function: save data to table registration
	 * body: 
	 *	$request	: array[full_name, email, phone, password, c_password]
	 */

    public function save_regist(Request $request)
    {
        
            //validasi data request
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|max:25|',
                'email' => 'required|email',
                'phone' => 'required|numeric|digits_between:10,13',
                'password' => 'required|min:8|max:15|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',
                'c_password' => 'required|same:password',
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
        try{
            //create random number for otp
            $otp = rand(100000,999999);
            // $otp =123456;
            $request['otp']= $otp;
            $request['phone_verified']= 0;

            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $email = $input['email'];
            $phone = $input['phone'];

            /*check email dan phone number
            if number not available in table user and table registration 
            the save data to table registraton
            */

            // $check_regist = Registration::check_user($phone);
            $check_user = User::check_user($phone);
            if($check_user!=null){
                $response = [
                            'status' => false,
                            'message'=> __('message.phone_exist'),
                            'code' => 409,
                            'data' => null,
                        ];
                        return response()->json($response, 200);
            }
            else{
                $update= Registration::update_user_regist($request->all());
                if($update){
                    $phone_verified = Registration::phone_verified($update->phone);
                            $response = [
                                'status' => true,
                                'message' => __('message.data_saved_success'),
                                'code' => 200,
                                'data' => [
                                    'id' => $update->id,
                                ],
                            ];
                            // //send email otp
                            // //sementara via email change call api otp
                            return response()->json($response, 200);   
                        }
                $response = [
                            'status' => false,
                            'message' => __('message.failed_save_data'),
                            'code' => 200,
                            'data' => null, 
                            ];
                return response()->json($response, 200);
            }
        }
        catch (Throwable $e) {
            report($e);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }   
    /*
	 * Function: save or update data to table User
	 * body: 
	 *	$request	: array[phone, otp]
	 */
    public function user_verification(Request $request)
    {
            //validasi data request
            $validator = Validator::make($request->all(), [
                'phone' => 'required|numeric|digits_between:10,13',
                'otp' => 'required|numeric|',
                'device_name' => 'required'
                ]);
                if ($validator->fails()) {
                    // return response gagal
                    $response = [
                        'status' => false,
                        'code' => 400 ,
                        'message' => $validator->errors()->first(), 
                        'data' => null,
                    ];
                    return response()->json($response, 200);
                }
        try{
            $input=$request->all();
            $phone = $input['phone'];
            $otp = $input['otp'];
            /*check phone number from table user 
             if exist then save data to table user and send email verification
             if not exist send respon save data failed
             */
            $check= User::where('phone', $phone)->first();
            if($check!=null){
                $response = [
                    'status' => false,
                    'code' => 200,
                    'message' => __('message.failed_save_data'),       
                    'data'     => [
                        'phone' => $request['phone'],
                        'otp' => $request['otp'],
                    ],    
                ];
                return response()->json($response, 200);
            }
            /*check phone number and otp to table registration
            *if match do next process
            *if not match send respon invalid
            */
            $check_regist = Registration::check_otp($phone, $otp);
            if($check_regist==null){
                $response = [
                    'status' => false,
                    'message' => __('message.otp_invalid'),
                    'code' => 400,
                    'data'    => [
                            'phone' => $request['phone'],
                            'otp' => $request['otp'],
                           ],

                ];
                return response()->json($response, 200);
            }            
            //insert data user from check regist to variable data
            $data = $check_regist[0];
            $password = $data->password;
            $data->id_role=1;   
            $data = json_decode(json_encode($data), true); 
            $data = $data+["password" => $password, 'email_verified' => 0];
            /*change data phone verified from table registration
            * insert data user from data regisrtation
            */
                $phone_verified = Registration::verified($phone);
                $insert = User::create($data);
                /*if phone_verified update & insert data success
                * create token for login home page
                */
                if($phone_verified && $insert){
                    //check user by phone if exist update verified email to 0
                    $check_id= User::where('phone', $phone)->first(); 
                    //update user verified to 0
                    // $verified = user::verified($insert->phone);
                    //create token user for login homepage
                    $token = $check_id->createToken($request->device_name)->plainTextToken;
                    //create token if success next step to home page
                    if($token){
                        $check_id = json_decode(json_encode($check_id), true); 
                        $check_id = $check_id + ['token' => $token];
                            $response = [
                                'status'   => true,
                                'code' => 200,
                                'message' => __('message.data_saved_success'),
                                // 'data'  => [
                                //         'id' => $check_id['id'],
                                //         'full_name' => $check_id['full_name'],
                                //         'email' => $check_id['email'],
                                //         'phone' => $check_id['phone'],
                                //         'token' => $token
                                    'data' => $check_id,
                                    
                            ];
                            return response()->json($response, 200);
                        }
                    //send response if token failed to create
                    $response = [
                                'status' => false,
                                'code' => 200,
                                'message' => __('message.token_failed'),
                                'data' => [
                                    'phone' => $request['phone'],
                                    'otp' => $request['otp'],
                                ],
                            ];
                            return response()->json($response, 200);
                }
                //send response if registration failed to create
                $response = [
                    'status' => false,
                    'code' => 200,
                    'message' => __('message.failed_save_data'),
                    'data' => null,
                ];
                return response()->json($response, 200);    
        }
        catch (Throwable $e) {
            report($e);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    /*
	 * Function: register akun with sso
	 * body: 
	 *	$request	: array[id_sso_user, id_sso_type,email,phone,fullname]
	 */

    public function save_user_sso(Request $request)
    {
        //validasi data request
        $validator = Validator::make($request->all(), [
            'id_sso_user' => 'required',
            'id_sso_type' => 'required',
            'email' => 'required|email',
            'full_name' => 'required|max:25',
            'phone' => 'required|numeric|digits_between:10,14',
        ]);
        if ($validator->fails()) {
            // return response gagal
            $response = [
                'status' => false,
                'code' => 400 ,
                'message' => $validator->errors()->first(), 
                'data' =>null,
            ];
            return response()->json($response, 200);
        }
        /*check id sso user from table user sso
        * if id user sso not null call api login
        * 
        */
        $check_id= UserSso::where('user_id_sso', $request->id_sso_user)->first(); 
        if($check_id != null)
        {
            $response = [
                'status' => false,
                'code' => 200,
                'message' => __('message.user_exists'),
                'data' => [
                    'id: '=> $check_id['id'],
                    'phone: '=>  $request->phone,
                    'email: '=>  $request->email,
                ],
            ];
            return response()->json($response, 200);
        }
        //if data not exist in table user sso
        else{
            //check phone and email user in table user
            $check_user = User::check_user($request->phone);
            //if check user not null insert data to table user sso
            if($check_user !=null){
                // if user exist then insert data to table  UserSso
                $get_id = $check_user[0];
                $data = [
                    'user_id'  => $get_id->id,
                    'user_id_sso' => $request->id_sso_user,
                    'sso_type_id' => $request->id_sso_type,        
                ];
            //insert data to table User SSO
                $insert = UserSso::create($data);
                if($insert)
                {
                    $response = [
                        'status' => true,
                        'code' => 200,
                        'message' => __('message.data_saved_success'),
                        'data' => [
                            'id_user: '=> $get_id->id,
                            'id_user_sso: '=>  $request->id_sso_user,
                        ],
                    ];
                    return response()->json($response, 200);
                }
                $response = [
                    'status' => false,
                    'code' => 200,
                    'message' => __('message.failed_save_data'),
                    'data' => [
                        'id_user: '=> $get_id->id,
                        'id_user_sso: '=>  $request->id_sso_user,
                    ],
                ];
                return response()->json($response, 200);
            }
            //if data not exist in table user
            else{
                //check phone and email user in table registration
                $check_regist = Registration::check_user($request->phone);
                // if data exist in table registration then call function resend otp register
                if($check_regist != null){
                    $response = [
                        'status' => false,
                        'code' => 200,
                        'message' => __('message.data_exist'),
                        'data' => [
                            'id: '=> $check_regist[0]->id,
                            'id_user_sso: '=>  $request->id_sso_user,
                        ],
                    ];
                    return response()->json($response, 200);
                }
                //if data not exist then redirect to register user
                else
                {
                    $response = [
                        'status' => false,
                        'code' => 200,
                        'message' => __('message.data_not_exist'),
                        'data' => [
                            'id: '=> $check_regist[0]->id,
                            'id_user_sso: '=>  $request->id_sso_user,
                        ],
                    ];
                    return response()->json($response, 200);
                }
            }
        }  
    }

    /*
	 * Function: otp to email
	 * body: 
	 *	$request	: string id
	 */
    public function send_otp($id)
    {
        try{
                $user = Registration::whereId($id)->first();
            if($user!= null){
                $otp = rand(100000,999999);
                // $otp =123456;
                // change user status to "activated"
                $user->otp = $otp;
                $data = [
                        'full_name'  => $user->full_name,
                        'otp'   => $otp,
                    ];
                $user->save();
            
                //send otp code to email user
                $kirim = Mail::to($user->email)->send(new SendMail($data));
                unset($data['otp']);
                $response = [
                    'status' => True,
                    'code' => 200,
                    'message' => __('message.otp_success_send' ),
                    'data' => $data
                    
                ];
                return response()->json($response, 200);
            }
            else{
                $response = [
                    'status' => false,
                    'code' => 200,
                    'message' => __('message.otp_failed_send' ),
                    'data' => $user,
                    
                ];
                return response()->json($response, 200);
            }
        }
        catch (Throwable $e) {
            report($e);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    /*
	 * Function: resend otp to email
	 * body: 
	 *	$request	: array[id]
	 */
    public function resend_otp(Request $request)
    {
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                        'status' => false,
                        'code' => 400,
                        'message' => $validator->errors()->first(), 
                        'data' => null,
                    ];
                return response()->json($response, 200);
                }
        try{
            $user = Registration::whereId($request->id)->first();
            if($user!= null){
                $otp = rand(100000,999999);
                // $otp =123456;
                //change otp value from tabel registration
                $user->otp = $otp;
                $data = [
                        'full_name'  => $user->full_name,
                        'otp'   => $otp,
                    ];
                $user->save();
                
                //send respon
                $response = [
                    'status' => True,
                    'code' => 200,
                    'message' => __('message.otp_success_send' ),
                    'data' => [
                        'id' => $user['id'],
                        'full_name' => $user['full_name'],
                        'phone' => $user['phone'],
                        'email' => $user['email']
                    ]
                    
                ];
                $kirim = Mail::to($user->email)->send(new SendMail($data));
                return response()->json($response, 200);
            }
            else{
                $response = [
                    'status' => false,
                    'code' => 200,
                    'message' => __('message.otp_failed_send' ),
                    'data' =>null                    
                ];
                return response()->json($response, 200);
            }
        }
        catch (Throwable $e) {
            report($e);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    /*
	 * Function: resend otp login to email
	 * body: 
	 *	$request	: array[id]
	 */
    public function resend_otp_login(Request $request)
    {
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                        'status' => false,
                        'code' => 400,
                        'message' => $validator->errors()->first(), 
                        'data' => null,
                    ];
                return response()->json($response, 200);
                }
        try{
            /*
            *check user if user exist create otp code and send by email
            *if user not exist send response failed
            */
            $user = User::whereId($request->id)->first();
            if($user!= null){
                $otp = rand(100000,999999);
                // $otp =123456;
                //create otp data
                $data = $request->all();
                $data = $data+['otp' => $otp];
                $create_otp = OTP::create_otp($data);
                if($create_otp)
                {
                    $data = $data+['full_name'  => $user->full_name];
                    $response = [
                    'status' => True,
                    'code' => 200,
                    'message' => __('message.otp_success_send' ),
                    'data' => [
                        'id' => $user['id'],
                        'full_name' => $user['full_name'],
                        'phone' => $user['phone'],
                        'email' => $user['email']
                    ]
                    
                ];
                //send otp code to email
                $kirim = Mail::to($user->email)->send(new SendMail($data));
                return response()->json($response, 200);
                }

                $response = [
                    'status' => false,
                    'code' => 200,
                    'message' => __('message.otp_failed_send' ),
                    'data' =>null                    
                ];
                return response()->json($response, 200);
                
            }
            else{
                $response = [
                    'status' => false,
                    'code' => 200,
                    'message' => __('message.otp_failed_send' ),
                    'data' =>null
                    
                ];
                return response()->json($response, 200);
            }
        }
        catch (Throwable $e) {
            report($e);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    /*
	 * Function: send email verifikasi
	 * body: 
	 *	$request	: string email
	 */
    public function send_verify($email)
    {
        try{
            if($email == null){
                $response = [
                    'status' => false,
                    'code' => 200,
                    'message' => __('message.email_send_failed'),
                    'data' => ['email: '=> $email],
                ];
                return response()->json($response, 200);
            }
            //send verification email to user
            $send_mail = Mail::to($email)->send(new VerifyEmail($email));
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.email_send_success'),
                    'data' => ['email: '=> $email],
                ];
                return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $response = [
                    'status' => false,
                    'message' => __('message.internal_erorr'),
                    'code' => 500,
                    'data' => null, 
                ];
            return response()->json($response, 500);
        }
    }

    /*
	 * Function: resend email verifikasi
	 * body: 
	 *	$request	: array[email]
	 */
    public function resend_verify(Request $request)
    {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(), 
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
        try{
            $email = $request['email'];
            //send email verification
            $send_mail = Mail::to($email)->send(new VerifyEmail($email));
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.email_send_success'),
                    'data' => ['email: '=> $email],
                ];
                return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }
    /*
	 * Function: verifikasi email
	 * body: 
	 *	$request	: String hash email as token
	 */
    public function verify()
    {
        try{
            if (empty(request('token'))) {
                // if token is not provided
                $response = [
                    'status' => false,
                    'code' => 200,
                    'message' => __('message.User_verified_failed'),
                ];
                return response()->json($response, 200);
            }
                //decryp token
                $decrypted = Crypt::decryptString(request('token'));
                //check field
            $user = User::whereEmail($decrypted)->first();
            if($user!= null){
                if ($user->email_verified == '1') {
                    $response = [
                        'status' => false,
                        'code' => 200,
                        'message' => __('message.User_verified'),
                        
                    ];
                    return response()->json($response, 200);
                }
                // change user status to "activated"
                $user->email_verified = '1';
                $user->save();
            
                $response = [
                    'status' => True,
                    'code' => 200,
                    'message' => __('message.User_verified_success'),
                    
                ];
                return response()->json($response, 200);
            }
            else{
                $response = [
                    'status' => false,
                    'code' => 200,
                    'message' => __('message.User_verified_failed'),
                    'data' => $user,
                    
                ];
                return response()->json($response, 200);
            }
        }
        catch (Throwable $e) {
            report($e);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
        
    }

    /*
	 * Function: get data User 
	 * body: 
	 *	$request	: string name
	 **/
    public function get_user_all()
    {
        try {      
            $data = User::get_user_all();
            if($data == null){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $data,
                    
                ];
                return response()->json($response, 200);
        } 
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_user_all',
                'actions' => 'get data user',
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
	
	/*
	 * Function: get data User Hotel
	 * body: 
	 *	$request	: string name
	 **/
    public function get_user_hotel()
    {
        try {      
            $data = User::get_user_hotel();
            if($data == null){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $data,
                    
                ];
                return response()->json($response, 200);
        } 
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_user_all',
                'actions' => 'get data user',
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
	 * Function: add data User
	 * body: data user
	 *	$request	: 
	*/
    
    public function add_user(Request $request)
    {
        try{
        //validasi data request
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|max:25|',
                'email' => 'required|email',
                'phone' => 'required|numeric|digits_between:10,15',
                // 'user_name' => 'required|min:8|alpha_dash',
                'user_name' => 'required|min:8|max:25|regex:/^[0-9A-Za-z.-_]+$/',
                'created_by' => 'required',
                'id_role' => 'required',
            ]);
            $validatorPassword = Validator::make($request->all(), [
                'password' => 'required|min:8|max:15|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',
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
            if ($validatorPassword->fails()){
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validatorPassword->errors()->first(), 
                    'data' =>null,
                    
                ];
                return response()->json($response, 200);
            }

            $check_email = User::where('email',$request->email)->first();
            $check_username = User::where('user_name',$request->user_name)->first();
            if($check_email != null){
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => "Email has been taken", 
                    'data' =>null,
                    
                ];
                return response()->json($response, 200);
            }
            if($check_username != null){
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => "Username has been taken", 
                    'data' =>null,
                    
                ];
                return response()->json($response, 200);
            }
            // dd($check_username);
            // dd($request->all());
                $save= User::add_user($request->all());
                if($save){
                        $response = [
                            'status' => true,
                            'message' => __('message.data_saved_success'),
                            'code' => 200,
                            'data' =>$save,
                            ];
                            return response()->json($response, 200);   
                        }
                else{
                $response = [
                            'status' => false,
                            'message' => __('message.failed_save_data'),
                            'code' => 200,
                            'data' => null, 
                            ];
                return response()->json($response, 200);
            }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'add_user',
                'actions' => 'save data user',
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

    public function edit_user(Request $request)
    {
        try{
        //validasi data request
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|max:25|',
                'email' => 'required|email',
                'user_name' => 'required|min:8',
                'phone' => 'required|numeric|digits_between:10,15',
                'id_role' => 'required',
                'id' =>'required',
                'updated_by' => 'required',
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 200,
                    'message' => $validator->errors()->first(), 
                    'data' =>null,
                    
                ];
                return response()->json($response, 200);
            }
            $check_username = User::where('id', $request->id)->first();
            $check_all_username = User::where('user_name', $request->user_name)
                                        ->where('id','!=', $request->id)
                                        ->where('deleted_at','=', null)
                                        ->first();
            $check_all_email = User::where('email', $request->email)
                                        ->where('id','!=', $request->id)
                                        ->where('deleted_at','=', null)
                                        ->first();                                        
            // dd($check_all_email);
            if($check_all_username != null || $check_all_email != null){
                
                if($check_all_username != null){
                    $response = [
                        'status' => false,
                        'message' => __('The user name has already been taken.'),
                        'code' => 200,
                        'data' => null, 
                        ];
                    return response()->json($response, 200);
                }
                $response = [
                    'status' => false,
                    'message' => __('The email has already been taken.'),
                    'code' => 200,
                    'data' => null, 
                    ];
                return response()->json($response, 200);
            }
                $save= User::edit_user($request->all());
            
            // dd();
                // $save= User::edit_user($request->all());
                if($save){
                    $data = User::Where('id',$request->id)->first();
                    // dd($data);
                        $response = [
                            'status' => true,
                            'message' => __('message.data_update_succcess'),
                            'code' => 200,
                            'data' =>$data,
                            ];
                            return response()->json($response, 200);   
                        }
                else{
                $response = [
                            'status' => false,
                            'message' => __('message.data_update_failed'),
                            'code' => 200,
                            'data' => null, 
                            ];
                return response()->json($response, 200);
            }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'edit_user',
                'actions' => 'Edit data user',
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
	 * Function: delete data user
	 * body: param[id]
	 *	$request	: 
	*/
    
    public function delete_user(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required',]);
            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            $checkUser = HotelUser::where('user_id', $request->id)->get();
            if(count($checkUser) != 0){
                $response = [
                    'status' => false,
                    'message' => __('message.failed_save_data'),
                    'code' => 200,
                    'data' => $request->all(), 
                    ];
                return response()->json($response, 200);
            }else{
                $deletedRows = User::where('id', $request->id)->delete();
                if($deletedRows){
                          $response = [
                                'status' => true,
                                'message' => __('message.data_deleted_success'),
                                'code' => 200,
                                'data' => null,
                            ];
                            return response()->json($response, 200);   
                        }
                else{
                $response = [
                            'status' => false,
                            'message' => __('message.failed_save_data'),
                            'code' => 200,
                            'data' => $request->all(), 
                            ];
                return response()->json($response, 200);
                }
            }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'delete_user',
                'actions' => 'Delete data user',
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
    

    public function get_user(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required',
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
            /**
            *check user by id
            *if user not null send data profil
            */
            $user = User::get_user_id($request['id'])->first();
            // dd($user);
            if($user !=null)
            {
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_exist'),
                    'data' => $user,
                    
                ];
                return response()->json($response, 200);
            }
            //respon if user not found
            $response = [
                'status' => false,
                'code' => 200 ,
                'message' => __('message.data_not_exist'),
                'data' => null,
            ];
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_user',
                'actions' => 'get data user',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => [], 
            ];
            return response()->json($response, 500);
        }
    }

    /**
	 * Function: Forgot_Password data user Cashier FnB
	 * body: email
	 *	$request	: 
	*/
    public function forgot_password_user_outlet(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'email' => 'required',]);
            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            $checkUser = User::where('email', $request->email)->first();
            // dd(json_decode($checkUser,true));
            if($checkUser != null){
                // Create random OTP
                // $otp = rand(100000,999999);
                // $otp = 123456; // Dev OTP
                // $data = [
                //     'user_id' => $checkUser['id'],
                //     'email' => $checkUser['email'],
                //     'full_name' => $checkUser['full_name'],
                //     'otp' => $otp,
                //     'otp_verified' => 0
                // ];
                // edit : arka.moharifrifai 2022-04-22
                $otp = rand(100000,999999);
                $data = [
                    'user_id' => $checkUser['id'],
                    'email' => $checkUser['email'],
                    'full_name' => $checkUser['full_name'],
                    'otp' => $otp,
                    'otp_verified' => 0
                ];
                $checkPass = ForgotPassword::where('email', $request->email)->first();
                
                
                // edit : arka.moharifrifai 2022-06-03
                $forgot_pass_cnt_valid = true;
                $check_forgot_pass_cnt = ForgotPasswordCnt::where('user_id', $data['user_id'])
                    ->where('req_dt','=', Carbon::now()->format('Y-m-d'))
                    ->first();  
                $check_system = Msystem::where('system_type','forgot_password')
                    ->where('system_cd','max_cnt')
                    ->first();
                $max_cnt = $check_system['system_value'];
                $check_system = Msystem::where('system_type','forgot_password')
                    ->where('system_cd','next_send')
                    ->first();
                $next_send = $check_system['system_value'];

                    
                if($check_forgot_pass_cnt != null){
                    $time = Carbon::now();
        
                    $last_req = Carbon::createFromFormat('Y-m-d H:i:s', $check_forgot_pass_cnt['updated_at']);
                    $next_time = $last_req->addSeconds($next_send);
                    $result = $time->lte($next_time);
                    if($result){
                        $forgot_pass_cnt_valid = false;
                        $next_send = $time->diffInSeconds($next_time);
                        $data['next_send'] = $next_send; 
                        $response = [
                            'status' => true,
                            'code' => 200 ,
                            'message' => '',
                            'data' => $data
                        ];
                    }else{
                        if($check_forgot_pass_cnt['cnt'] >= $max_cnt){
                            $response = [
                                'status' => false,
                                'code' => 500 ,
                                'message' => __('message.forgot_password_max_cnt'),
                                'data' => null,
                            ];
                            $forgot_pass_cnt_valid = false;
                        }else{
                            $result = $time->lte($next_time);
                            if($result){
                                $response = [
                                    'status' => false,
                                    'code' => 500 ,
                                    'message' => __('message.forgot_password_next_send'),
                                    'data' => null,
                                ];
                                $forgot_pass_cnt_valid = false;
                            }else{
                                // update forgot pass cnt
                                $update_forgot_password_cnt = ForgotPasswordCnt::update_forgot_password_cnt(
                                    [
                                        "user_id" => $data['user_id']
                                        , "cnt" => $check_forgot_pass_cnt["cnt"] + 1
                                    ]
                                );
                            }
                        }
                    }
                }else{
                    $addForgotPasswordCnt = ForgotPasswordCnt::add_forgot_password_cnt(
                        [
                            "user_id" => $data['user_id']
                            , "cnt" => 1
                        ]
                    );
                }
                
                // edit : arka.moharifrifai 2022-06-03
                if($forgot_pass_cnt_valid){
                    if($checkPass != null){
                        $checkUser = ForgotPassword::reset_otp_verified($data);
                        if($checkUser){
                            //send otp code to email user
                            // $kirim = Mail::to($request->email)->send(new SendMail($data));
                        } else {
                            $response = [
                                'status' => false,
                                'code' => 500 ,
                                'message' => __('message.otp_failed_send'),
                                'data' => null,
                            ];
                        }
                    } else {
                        $addUserForgot = ForgotPassword::add_forgot_password($data);
                        $data = $addUserForgot;
                        $data['full_name'] = $checkUser['full_name'];
                        // $kirim = Mail::to($request->email)->send(new SendMail($data));
                    }
                    unset($data['otp']);
                    $data['next_send'] = $next_send;
                    // edit : arka.moharifrifai 2022-04-22
                    
                    $response = [
                        'status' => true,
                        'code' => 200 ,
                        'message' => __('message.otp_success_send'),
                        'data' => $data
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'code' => 200 ,
                    'message' => __('message.email-not-exist'),
                    'data' => null,
                ];
               
            }
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'forgot_password_user_outlet',
                'actions' => 'Forgot Password for User Outlet',
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
	 * Function: Verified OTP data user Cashier FnB
	 * body: email, OTP dan Hash
	 *	$request	: 
	*/
    public function otp_verifiy_user_outlet(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'otp' => 'required|numeric'
                ]);
            $checkUser = ForgotPassword::where('email', $request->email)->first();
            // dd(json_decode($checkUser,true));
            
            if($checkUser != null){
                // edit : arka.moharifrifai 2022-06-03
                $check_system = Msystem::where('system_type','forgot_password')
                    ->where('system_cd','next_send')
                    ->first();
                $next_send = $check_system['system_value'];
                $check_forgot_pass_cnt = ForgotPasswordCnt::where('user_id', $checkUser['user_id'])
                    ->where('req_dt','=', Carbon::now()->format('Y-m-d'))
                    ->first();  
                $time = Carbon::now();
    
                $last_req = Carbon::createFromFormat('Y-m-d H:i:s', $check_forgot_pass_cnt['updated_at']);
                $next_time = $last_req->addSeconds($next_send);
    
                $result = $time->lte($next_time);
                if($result){
                    $next_send = $time->diffInSeconds($next_time);
                }else{
                    $next_send = 0;
                }
                // edit : arka.moharifrifai 2022-06-03
                // Check OTP di tabel ForgotPassword
                if ($validator->fails()) {
                    $response = [
                        'status' => false,
                        'code' => 400 ,
                        'message' => $validator->errors()->first(),
                        'data' => [
                            'email' => $request->email,
                            'next_send' => $next_send
                        ]
                    ];
                    return response()->json($response, 200);
                }else{
                    if($next_send == 0){
                        $checkUser['next_send'] = $next_send;
                        $response = [
                            'status' => false,
                            'code' => 200,
                            'message' => __('message.otp_expired'),
                            'data' => $checkUser
                        ];
                    }else{
                        if($checkUser['otp'] == $request->otp){
                            // create hashing utk nanti dilempar ke Front End sbg token ganti Password
                            $randomHash = $this->EncryptHelper->create_verifier();
                            $otpVerified = ForgotPassword::otp_verified($request->email);
                            $hashSalt = ForgotPassword::update_token($request->email,$randomHash);
                            $checkUser['token'] = $randomHash;
                            $checkUser['next_send'] = $next_send;
                            $response = [
                                'status' => true,
                                'code' => 200,
                                'message' => __('message.User_verified_success'),
                                'data' => $checkUser
                            ];
                        } else {
                            $checkUser['next_send'] = $next_send;
                            $response = [
                                'status' => false,
                                'code' => 200,
                                'message' => __('message.otp_invalid'),
                                'data' => $checkUser
                            ];
                        }
                    }
                }
            } else {
                $response = [
                    'status' => false,
                    'code' => 200 ,
                    'message' => __('message.data_not_exist'),
                    'data' => null,
                ];
               
            }
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'otp_verifiy_user_outlet',
                'actions' => 'OTP Verify for User Outlet',
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
	 * Function: Update Password data user Cashier FnB
	 * body: email, OTP dan Hash
	 *	$request	: 
	*/
    public function update_password_user_outlet(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'token' => 'required',
                'password' => 'required|min:8|max:15|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/'
                ]);
            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            $checkUser = ForgotPassword::where('email', $request->email)->first();
            // dd(json_decode($checkUser,true));
            if($checkUser != null){
                // Check Token Hashing di tabel ForgotPassword
                if($checkUser['token'] == $request->token){
                    // create hashing utk nanti dilempar ke Front End sbg token ganti Password
                    $randomHash = $this->EncryptHelper->create_verifier();
                    $checkUser['token'] = $randomHash;
                    $checkUser->save();
                    // update tabel User set Password baru
                    $password = bcrypt($request->password);
                    $changePass = User::where('email', $request->email)->first();
                    $changePass['password'] = $password;
                    $changePass->save();
                    $response = [
                        'status' => true,
                        'code' => 200,
                        'message' => __('message.password-change-success'),
                        'data' => $changePass
                    ];
                } else {
                    $response = [
                        'status' => false,
                        'code' => 200,
                        'message' => __('message.invalid_token'),
                        'data' => $checkUser
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'code' => 200 ,
                    'message' => __('message.password-change-failed'),
                    'data' => null,
                ];
               
            }
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'otp_verifiy_user_outlet',
                'actions' => 'OTP Verify for User Outlet',
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
	 * Function: Change Password data user Cashier FnB from dashboard
	 * body: id, old_password dan password
	 *	$request	: 
	*/
    public function change_password_user_outlet(Request $request)
    {
        
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'old_password' => 'required|min:8|max:15|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',
                'password' => 'required|min:8|max:15|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',
                'device' => 'required',
            ]);
            if ($validator->fails()) {
                // return response gagal
                $error = json_decode($validator->errors());
                $response = [
                    'status' => false,
                    'code' => 404,
                    'message' => $validator->errors()->first(), 
                    'data' => [],
                ];
                return response()->json($response, 200);
            }
        $user = User::where('id', $request->id)->first();
        if(empty($user)){
            $response = [
                'status'   => false,
                'code' => 200,
                'message' => __('message.password-change-failed'),
                'data'     => null,
            ];
        }
            if (!$user|| ! Hash::check($request->old_password, $user->password)) {
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => __('message.old_pass_not_match'),
                    'data' => $request->email,
                ];
            }else{
                // dd('ubah password');
                $password = bcrypt($request->password);
                    $changePass = User::where('id', $request->id)->first();
                    $changePass['password'] = $password;
                    $changePass->save();
                    $user->tokens()->delete();
                    // Revoke a specific token...
                    $token = $user->createToken($request->device)->plainTextToken;
                        if($token){
                            $response = [
                                'status'   => true,
                                'code' => 200,
                                'message' => __('message.password-change-success'),
                                'data'     => [
                                            'id'=> $user['id'],
                                            'full_name' => $user['full_name'],
                                            'phone' => $user['phone'],
                                            'email' => $user['email'],
                                            'id_role' => $user['id_role'],
                                            'image' => $user['image'],
                                            'token' => $token
                                        ],
                                    ];
                        }
            }    
        return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'login',
                'actions' => 'login_master_web',
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
	 * Function: add token firebase mobile
	 * body: device_id,token
	 *	$request	: 
	*/
    public function add_token_firebase_device(Request $request)
    {
        
        try{
            $validator = Validator::make($request->all(), [
                'device_id' => 'required',
                'token' => 'required',
            ]);
            if ($validator->fails()) {
                // return response gagal
                $error = json_decode($validator->errors());
                $response = [
                    'status' => false,
                    'code' => 404,
                    'message' => $validator->errors()->first(), 
                    'data' => [],
                ];
                return response()->json($response, 200);
            }
            // dd($request->all());
            $save_token= Notification::save_token($request->all());
            if($save_token){
                $response = [
                      'status' => true,
                      'message' => __('message.data_saved_success'),
                      'code' => 200,
                      'data' => $save_token,
                  ];
                  return response()->json($response, 200);   
              }
                $response = [
                            'status' => false,
                            'message' => __('message.failed_save_data'),
                            'code' => 200,
                            'data' => null, 
                            ];
            
        return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'add token firebase',
                'actions' => 'save token firebase',
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

    public function get_user_total_notification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_id' => 'required',
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

            $total_notif_reservation = Reservation::get_total_notif_stay($request->all());
            $total_notif_dining = OrderDining::get_total_notif_dining_all($request->all());
            
            
            
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_found' ),
                'total_notif' => $total_notif_reservation + $total_notif_dining,
            ];
            return response()->json($response, 200);
                    
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_user_total_notification',
                'actions' => 'get_user_total_notification',
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

}
