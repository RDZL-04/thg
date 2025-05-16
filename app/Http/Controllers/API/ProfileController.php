<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Support\Facades\Hash;
use Validator;
use Auth;
use Crypt;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\LOG\ErorrLogController;
use File;
use GuzzleHttp\Client;

/*
|--------------------------------------------------------------------------
| Profile API Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process Profile data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: December 2020
*/

class ProfileController extends Controller
{

    public function __construct()
    {
        $this->LogController = new ErorrLogController;
    }

    /**
     * Function: get profil
     * Param: 
     *	$request	: array[id:]
     * header : Authorization : Bearer
     */
    public function get_profile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            /**
             *check user by id
             *if user not null send data profil
             */
            $user = User::whereId($request['id'])->first();

            if ($user != null) {
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
                'code' => 200,
                'message' => __('message.data_not_exist'),
                'data' => null,
            ];
            return response()->json($response, 200);
        } catch (Throwable $e) {
            report($e);
            $error = [
                'modul' => 'get_profile',
                'actions' => 'get data user',
                'error_log' => $e,
                'device' => "0"
            ];
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

    /*
	 * Function: edit profil
	 * Param: 
     *	$request	: array[id,full_name,jk,image,address,city,country,state_provice,postal_cd]
     * header : Authorization : Bearer
	 **/
    public function edit_profile(Request $request)
    {
        try {
            // return $request->all();
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'full_name' => 'required',
                'gender' => 'max:1',
                'address' => 'max:250',
                'city' => 'max:100',
                'country' => 'max:3',
                'state_province' => 'max:5',
                'postal_cd' => 'max:5',
                'phone' => 'required|numeric|digits_between:10,15',
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            if (!empty($request->date_of_birth) || $request->date_of_birth != null) {
                $validator = Validator::make($request->all(), [
                    'date_of_birth' => 'date',
                ]);
                if (empty($request->date_of_birth)) {
                    $request['date_of_birth'] = null;
                }

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
            }
            /*
        *check user by id
        *if user not null send data profil
        **/
            $user = User::whereId($request['id'])->first();
            if ($user != null) {
                $update = User::update_user($request->all());
                if ($update) {
                    $user = User::get_user_id($request['id'])->first();
                    $response = [
                        'status' => true,
                        'code' => 200,
                        'message' => __('message.data_update_succcess'),
                        'data' => $user,

                    ];
                    return response()->json($response, 200);
                }
                $response = [
                    'status' => false,
                    'code' => 200,
                    'message' => __('message.data_update_failed'),
                    'data' => null,

                ];
                return response()->json($response, 200);
            }
            //respon if user not found
            $response = [
                'status' => false,
                'code' => 200,
                'message' => __('message.data_not_exist'),
                'data' => null,
            ];
            return response()->json($response, 200);
        } catch (Throwable $e) {
            report($e);
            $error = [
                'modul' => 'edit_profile',
                'actions' => 'edit data user',
                'error_log' => $e,
                'device' => "0"
            ];
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

    /*
	 * Function: change password
	 * Param: 
     *	$request	: array[id,password_old,password]
     * header : Authorization : Bearer
	 **/
    public function change_password(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'password_old' => 'required',
                'password' => 'required|min:8|max:15|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/',
                'c_password' => 'required|same:password',
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }

            /*
        *check user by id
        *if user not null send data profil
        **/
            if ((Hash::check(request('password_old'), Auth::user()->password)) == false) {
                $response = [
                    'status' => false,
                    'code' => 200,
                    'message' => __('message.check_old_pass'),
                    'data' => null,

                ];
                return response()->json($response, 200);
            } else if ((Hash::check(request('password'), Auth::user()->password)) == true) {
                $response = [
                    'status' => false,
                    'code' => 200,
                    'message' => __('message.password_same_old'),
                    'data' => null,

                ];
                return response()->json($response, 200);
            } else {
                $update = User::where('id', $request['id'])->update(['password' => Hash::make($request['password'])]);
                if ($update) {
                    $data = User::where('id', $request['id'])->first();
                    $response = [
                        'status' => true,
                        'code' => 200,
                        'message' => __('message.data_update_succcess'),
                        'data' => $data,

                    ];
                    return response()->json($response, 200);
                }
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_update_failed'),
                    'data' => $data,

                ];
                return response()->json($response, 200);
            }
        } catch (Throwable $e) {
            report($e);
            $error = [
                'modul' => 'change_password',
                'actions' => 'change data password user',
                'error_log' => $e,
                'device' => "0"
            ];
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

    /*
	 * Function: logout account
	 * Param: 
	 * header:	token 
     * header : Authorization : Bearer
	 **/
    public function logout()
    {
        try {
            $user = request()->user(); //or Auth::user()
            // Revoke current user token
            $user->tokens()->delete();
            return response()->json([
                'success'    => true,
                'message' => __('message.logout_success'),
            ], 200);
        } catch (Throwable $e) {
            report($e);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => [],
            ];
            return response()->json($response, 500);
        }
    }

    public function get_stay(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            /*
            *check user by id
            *if user not null send data profil
            **/
            $stay = Reservation::get_stay($request['id']);
            if ($stay != null) {
                // dd($stay);
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_exist'),
                    'data' => $stay,

                ];
                return response()->json($response, 200);
            }
            //respon if user not found
            $response = [
                'status' => false,
                'code' => 200,
                'message' => __('message.data_not_exist'),
                'data' => null,
            ];
            return response()->json($response, 200);
        } catch (Throwable $e) {
            report($e);
            $error = [
                'modul' => 'logout',
                'actions' => 'logout user',
                'error_log' => $e,
                'device' => "0"
            ];
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
     * Function: add data image user
     * body: id, image
     *	$request	: 
     */
    public function add_image(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,jpg,png,PNG|max:2048',
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            if (!empty($request['image'])) {
                if (!empty($request['id'])) {
                    $path = User::where('id', $request->id)->first();
                    $file_path = app_path($path['image']);
                    if (File::exists($file_path)) File::delete(url($file_path));
                }
                $image = $request->file('image');
                $fileName = time() . '.' . $image->extension();
                $loc = public_path('user-images');
                $loct = $image->move($loc, $fileName);
                $data = [
                    'id' => $request['id'],
                    'image' => 'user-images/' . $fileName
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => __('message.failed_save_data'),
                    'code' => 200,
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            $save = User::update_images($data);
            if ($save) {
                $user = User::where('id', $request['id'])->first();
                $response = [
                    'status' => true,
                    'message' => __('message.data_saved_success'),
                    'code' => 200,
                    'data' => [
                        'image' => $user['image']
                    ],
                ];
                return response()->json($response, 200);
            } else {
                $response = [
                    'status' => false,
                    'message' => __('message.seq_no-allredy'),
                    'code' => 200,
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
        } catch (Throwable $e) {
            report($e);
            $error = [
                'modul' => 'add_image',
                'actions' => 'add data images user',
                'error_log' => $e,
                'device' => "0"
            ];
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

    public function fmc(request $request)
    {
        try {
            $messagex = ["body" => "order Success", "title" => "Dining Order"];
            $message = ["body" => json_encode($messagex), "title" => "dining Order", "data" => json_encode($messagex)];
            $data = [
                "to" => $request->token,
                "priority" => "high",
                "content-available" => "true",
                "collapse_key" => "a",
                "notification" => $message
            ];
            $data = json_encode($data);
            $header = [
                'Content-Type' => 'application/json',
                // 'Authorization' => 'key=',
                'Authorization' => 'key=',
            ];
            $client = new Client(); //GuzzleHttp\Client
            $response = $client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
                'verify' => false,
                'body'   => json_encode(json_decode($data), true),
                'headers'       => $header,
                '/delay/5',
                ['connect_timeout' => 3.14]
            ]);
            $body = $response->getBody();
            $response = json_decode($body, true);
            if ($response['success'] == 1) {
                $response = [
                    'status' => true,
                    'message' => 'notif has been send',
                    'code' => 200,
                    'data' => $request->token,
                ];
                return response()->json($response, 200);
            }
        } catch (Exception $e) {
            report($e);
            $error = [
                'modul' => 'add_image',
                'actions' => 'add data images user',
                'error_log' => $e,
                'device' => "0"
            ];
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
