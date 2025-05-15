<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NearAttraction;
use Validator;
use App\Http\Controllers\LOG\ErorrLogController;

/*
|--------------------------------------------------------------------------
| NearAttraction API Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process hotel data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: January 14 2021
*/
class NearAttractionController extends Controller
{

    public function __construct() {
        $this->LogController = new ErorrLogController;
    }

    /*
	 * Function: get data nearattraction by id hotel 
	 * body: 
	 *	$request	: string name
	 **/
    public function get_nearattraction(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'id_hotel' => 'required'
            ]);
            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            $get_data= NearAttraction::get_near($request->all());
            // dd($get_data);    
            if(empty($get_data)){
                $response = [
                    'status' => true,
                    'message' => __('message.data_not_found'),
                    'code' => 200,
                    'data' => null, 
                    ];
                return response()->json($response, 200);
            }
            else{
                $response = [
                    'status' => true,
                    'message' => __('message.data_found'),
                    'code' => 200,
                    'data' => $get_data,
                ];
                return response()->json($response, 200); 
            }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_nearattraction',
                'actions' => 'get data attraction near hotel',
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

    public function get_near_radius(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'id_hotel' => 'required',
                'radius' => 'required'
            ]);
            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            $get_data= NearAttraction::get_near_radius($request->all());
            // dd($get_data);    
            if(empty($get_data)){
                $response = [
                    'status' => false,
                    'message' => __('message.data_not_found'),
                    'code' => 400,
                    'data' => null, 
                    ];
                return response()->json($response, 200);
            }
            else{
                $response = [
                    'status' => true,
                    'message' => __('message.data_found'),
                    'code' => 200,
                    'data' => $get_data,
                ];
                return response()->json($response, 200); 
            }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_near_radius',
                'actions' => 'get data attraction radius hotel',
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
	 * Function: get data nearattraction by id hotel 
	 * body: 
	 *	$request	: string name
	 **/
    public function get_near_attraction_hotel(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'id_hotel' => 'required'
            ]);
            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            $get_data= NearAttraction::get_near_by_hotels($request->all());
            // dd($get_data);    
            if(empty($get_data)){
                $response = [
                    'status' => false,
                    'message' => __('message.data_not_found'),
                    'code' => 400,
                    'data' => null, 
                    ];
                return response()->json($response, 200);
            }
            else{
                $response = [
                    'status' => true,
                    'message' => __('message.data_found'),
                    'code' => 200,
                    'data' => $get_data,
                ];
                return response()->json($response, 200); 
            }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_nearattraction',
                'actions' => 'get data attraction near hotel',
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
	 * Function: get data nearattraction by id hotel 
	 * body: 
	 *	$request	: string name
	 **/
    public function add_update_near_attraction(Request $request)
    {
        try{
            if(!empty($request['id'])){
                $validator = Validator::make($request->all(), [
                    'id' => 'required',
                    'hotel_id' => 'required',
                    'attraction_nm' => 'required|max:100|regex:/^[A-Za-z0-9-_!,. ]+$/',
                    'category_id' => 'required',
                    'distance' => 'required|max:100|regex:/^[A-Za-z0-9-_!,. ]+$/',
                    'created_by' => 'required',
                ]);
                $messageSuccess = __('message.data_update_success');
                $messageFailed = __('message.data_update_failed');
            }else{
                $validator = Validator::make($request->all(), [
                    'hotel_id' => 'required',
                    'attraction_nm' => 'required|max:100|regex:/^[A-Za-z0-9-_!,. ]+$/',
                    'category_id' => 'required',
                    'distance' => 'required|max:100|regex:/^[A-Za-z0-9-_!,. ]+$/',
                    'created_by' => 'required',
                ]);
                $messageSuccess = __('message.data_saved_success');
                $messageFailed = __('message.failed_save_data');
            }
            
            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            // dd($request->all());
            $save= NearAttraction::save_near_attraction($request->all());
            if(!empty($request['id'])){
                $id = $request['id'];
            }else{
                $id = $save['id'];
            }
            // dd($get_data);    
            if($save){
                $get_data = NearAttraction::where('id',$id)->first();
                $response = [
                    'status' => true,
                    'message' => $messageSuccess,
                    'code' => 200,
                    'data' => $get_data, 
                    ];
                return response()->json($response, 200);
            }
            else{
                $response = [
                    'status' => false,
                    'message' => $messageFailed,
                    'code' => 400,
                    'data' => null,
                ];
                return response()->json($response, 200); 
            }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_nearattraction',
                'actions' => 'get data attraction near hotel',
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
	 * Function: delete data nearattraction by id
	 * body: 
	 *	$request	: string name
	 **/
    public function delete_near_attraction(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            $deletedRows = NearAttraction::where('id', $request->id)->delete();
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
                        'code' => 400,
                        'data' => $request->all(), 
                        ];
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_nearattraction',
                'actions' => 'get data attraction near hotel',
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
	 * Function: get data nearattraction by id
	 * body: 
	 *	$request	: id
	 **/
    public function get_near_attraction_by_id(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            $getData = NearAttraction::where('id', $request->id)->first();
            if($getData != null){
                $response = [
                      'status' => true,
                      'message' => __('message.data_found'),
                      'code' => 200,
                      'data' => $getData,
                  ];
                  return response()->json($response, 200);   
                    }
            $response = [
                        'status' => false,
                        'message' => __('message.data_not_found'),
                        'code' => 400,
                        'data' => $request->all(), 
                        ];
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_nearattraction',
                'actions' => 'get data attraction near hotel',
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
