<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HotelUser;
use App\Models\OutletUsers;
use App\Http\Controllers\LOG\ErorrLogController;
use App\Models\Hotel;
use App\Models\Outlet;
use Validator;

/*
|--------------------------------------------------------------------------
| Hotel User API Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process hotel data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: January 05 2021
*/

class HotelUserController extends Controller
{

    public function __construct() {
        $this->LogController = new ErorrLogController;
    }

    /*
	 * Function: get data user hotel 
	 * body: 
	 *	$request	: string name
	 **/
    public function get_hotel_user(Request $request)
    {
        try {
                $get = HotelUser::get_hotel_user($request);
                // dd($request['id_hotel']);
                $count = count($get);
                // dd($count);
                if($count!=0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $get,
                ];
                return response()->json($response, 200);
            }else{
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
                $response = [
                    'status' => false,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => $get,
                ];
                return response()->json($response, 200);       
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_hotel_user',
                'actions' => 'get data hotel user',
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

    public function add_hotel_user(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|unique:hotel_users,user_id,NULL,id,hotel_id,'.$request->hotel_id,
                'hotel_id' => 'required',
                'created_by' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            $insert= HotelUser::add_user($request->all());
                if($insert){
                          $response = [
                                'status' => true,
                                'message' => __('message.data_saved_success'),
                                'code' => 200,
                                'data' => $insert,
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
            $error = ['modul' => 'add_hotel_user',
                'actions' => 'add data hotel user',
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
	 * Function: delete data hotel user
	 * body: param[id]
	 *	$request	: 
	*/
    
    public function delete_hotel_user(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'hotel_id' => 'required'
            ]);
            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
                // remark by Arka.Rangga 20210628
                // Delete User di Outlet User jika ada
                // Get user_id from hotel_users table dan get list outlet from hotel_id
                $get_user_id = HotelUser::get_hotel_user_id($request->id);
                $data_outlet = Outlet::get_hotel_outlet($request->hotel_id);
                if(count($get_user_id) == 1 && count($data_outlet) > 0) {
                    $user_id = $get_user_id[0]['user_id'];
                    for($i=0; $i < count($data_outlet); $i++)
                    {
                        $data = ['fboutlet_id' => $data_outlet[$i]->id, 'user_id' => $user_id];
                        // Delete user from Outlet_Users table from fboutlet_id and hotel_id
                        $check_outlet = OutletUsers::delete_outlet_user_from_hotel_id($data);
                    }
                }
                $deletedRows = HotelUser::where('id', $request->id)->delete();
                if($deletedRows){
                          $response = [
                                'status' => true,
                                'message' => __('message.data_deleted_success'),
                                'code' => 200,
                                'data' => null,
                            ];
                            return response()->json($response, 200);   
                        }
                $response = [
                            'status' => false,
                            'message' => __('message.failed_save_data'),
                            'code' => 200,
                            'data' => $request->all(), 
                            ];
                return response()->json($response, 200);
            
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'delete_hotel_user',
                'actions' => 'delete data hotel user',
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
