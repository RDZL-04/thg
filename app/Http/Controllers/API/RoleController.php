<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterRole;
use App\Models\User;
use App\Http\Controllers\LOG\ErorrLogController;
use Validator;

/*
|--------------------------------------------------------------------------
| role user API Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process role data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: january 07 2021
*/

class RoleController extends Controller
{

    public function __construct() {
        $this->LogController = new ErorrLogController;
    }

    /*
	 * Function: get data Role 
	 * body: 
	 *	$request	:
	 **/
    public function get_role()
    {
        try {      
            $data = MasterRole::get_role();
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

    public function edit_role(Request $request)
    {
        try{
        //validasi data request
            $validator = Validator::make($request->all(), [
                'id' =>'required',
                'role_nm' => 'required|max:50',
                'description' => 'required|max:100',
                'updated_by' => 'required|max:30',
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
                $save= MasterRole::edit_role($request->all());
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
            $error = ['modul' => 'edit_role',
                'actions' => 'edit data role',
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
	 * Function: delete data role
	 * body: param[id]
	 *	$request	: 
	*/
    
    public function delete_role(Request $request)
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
                $chekedRole = User::where('id_role',$request->id)->get();
                if (count($chekedRole) == 0){
                    $deletedRows = MasterRole::where('id', $request->id)->delete();
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
                }else{
                    $response = [
                        'status' => false,
                        'message' => __('message.failed_save_data'),
                        'code' => 200,
                        'data' => $request->all(), 
                        ];
            return response()->json($response, 200);
                }
                
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'delete_role',
                'actions' => 'delete data role',
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
