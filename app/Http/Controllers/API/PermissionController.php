<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RolePermission;
use App\Http\Controllers\LOG\ErorrLogController;
use Validator;

/*
|--------------------------------------------------------------------------
| Permission API Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process permission data
| 
| @author: arif@arkamaya.co.id 
| @update: 02 June 2021 14:39
*/

class PermissionController extends Controller
{

    public function __construct() {
        $this->LogController = new ErorrLogController;
    }

    /*
	 * Function: get data Permissions 
	 * body: 
	 *	$request	:
	 **/
    public function get_permissions()
    {
        try {      
            $data = RolePermission::all();
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
            $error = ['modul' => 'get_role',
                'actions' => 'get data role',
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
    
    public function add_role(Request $request)
    {
        try{
        //validasi data request
            $validator = Validator::make($request->all(), [
                'role_nm' => 'required|max:50',
                'created_by' => 'required|max:30',
                'description' => 'required|max:100',
            ]);
            if ($validator->fails()) {
                // return response gagal
                // dd($validator->errors('role_nm'));
                if($validator->errors('role_nm')){
                    $message = 'The role name field is required and not be greater than 50 characters';
                }
                else{
                    $message = $validator->errors()->first();
                }
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $message, 
                    'data' =>null,
                    
                ];
                return response()->json($response, 200);
            }
                $save= MasterRole::add_role($request->all());
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
            $error = ['modul' => 'add_role',
                'actions' => 'add data role',
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
