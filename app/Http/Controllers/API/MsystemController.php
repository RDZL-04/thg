<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Msystem;
use App\Models\Facility;
use App\Models\HotelImages;
use App\Models\HotelFacility;
use App\Http\Controllers\LOG\ErorrLogController;
use Validator;
use File;


/*
|--------------------------------------------------------------------------
| System API Controller
|--------------------------------------------------------------------------
|
| Validate, Authorize api key
| This user controller will control data reservation
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: December 29 , 2020 09:05
*/

class MsystemController extends Controller
{
    public function __construct() {
        $this->LogController = new ErorrLogController;
    }

    public function getByTypeCD(Request $request)
    {
        try{
            $data = Msystem::where('system_type',$request->query('system_type'))
                ->where('system_cd',$request->query('system_cd'))
                ->first();

            if ( 
                $request->query('system_type') === 'card_section_enabler' 
                && $request->query('system_cd') === 'menu'
            ) {
                $data['system_value'] = $data['system_value'] === "Enable";
            }

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => __('message.data_found' ),
                'data' => $data,
            ], 200);
        } catch ( \Throwable $e ) {
            return response()->json([
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ], 500);
        }
    }

    /*
	 * Function: get data System search by id
	 * body: 
	 * $request	:
	 **/
    public function get_system_id(Request $request)
    {
        try{
            // dd($request->system_type);
            $data = Msystem::where('id',$request->id)
                            ->first();
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
            $error = ['modul' => 'get_system_id',
                'actions' => 'get data msystem',
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
	 * Function: get data payment_source 
	 * body: 
	 * $request	:
	 **/
    public function get_payment_source()
    {
        try{
            $data = Msystem::get_payment_source();
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
            $error = ['modul' => 'get_payment_source',
                'actions' => 'get data payment_source',
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
	 * Function: get data System 
	 * body: 
	 * $request	:
	 **/
    public function get_system()
    {
        try{
            $data = Msystem::getData();
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
            $error = ['modul' => 'get_system',
                'actions' => 'get data msystem',
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
	 * Function: add data Msystem
	 * body: data Msystem
	 *	$request	: 
	*/
    public function add_system(Request $request)
    {
        // return response()->json($request->all(), 200);
        
        try{
            $validator = Validator::make($request->all(), [
                'system_type' => 'required|max:100',
                'system_cd' => 'required|max:100',
                'system_value' => 'required|max:255',
                'created_by' => 'required|max:100',
                'system_img' => 'image|mimes:jpg,jpeg|max:2048',
            ]);
            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            if (!empty($request->system_img)){
                $image = $request->file('system_img');
                $fileName = time().'.'.$image->extension();  
                $loc = public_path('system-images');
                $loct = $image->move($loc, $fileName);
                $data = [
                    'system_type' =>$request['system_type'],
                    'system_cd' =>$request['system_cd'],
                    'system_value' =>$request['system_value'],
                    'created_by' =>$request['created_by'],
                    'system_img' =>'system-images/'.$fileName
                ];
            }
            else{
                $data = [
                    'system_type' =>$request['system_type'],
                    'system_cd' =>$request['system_cd'],
                    'system_value' =>$request['system_value'],
                    'created_by' =>$request['created_by'],
                    'system_img' => null
                ];
            }
            $insert= Msystem::add_system($data);
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
            $error = ['modul' => 'add_system',
                'actions' => 'Save data msystem',
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
	 * Function: edit data System
	 * body: data Msystem
	 *	$request	: 
	*/
    
    public function edit_system(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'system_type' => 'required|max:100',
                'system_cd' => 'required|max:100',
                'system_value' => 'required|max:255',
                'updated_by' => 'required|max:100',
                'system_img' => 'image|mimes:jpg,jpeg|max:2048',
            ]);
            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            if (!empty($request->system_img)){
                $path = Msystem::where('id', $request->id)->first();
                        $file_path = app_path($path['system_img']); 
                        if(File::exists($file_path)) File::delete($file_path);
                    
                $image = $request->file('system_img');
                $fileName = time().'.'.$image->extension();  
                $loc = public_path('system-images');
                $loct = $image->move($loc, $fileName);
                $data = [
                    'id' =>$request['id'],
                    'system_type' =>$request['system_type'],
                    'system_cd' =>$request['system_cd'],
                    'system_value' =>$request['system_value'],
                    'updated_by' =>$request['updated_by'],
                    'system_img' =>'system-images/'.$fileName
                ];
            }
            else{
                $data = [
                    'id' =>$request['id'],
                    'system_type' =>$request['system_type'],
                    'system_cd' =>$request['system_cd'],
                    'system_value' =>$request['system_value'],
                    'updated_by' =>$request['updated_by'],
                ];
            }
            $update= Msystem::update_system($data);
                if($update){
                          $response = [
                                'status' => true,
                                'message' => __('message.edit-success'),
                                'code' => 200,
                                'data' => $request->all(),
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
            $error = ['modul' => 'edit_system',
                'actions' => 'Edit data msystem',
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
	 * Function: delete data hotel
	 * body: param[id]
	 *	$request	: 
	*/
    
    public function delete_system(Request $request)
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
                $deletedRows = Msystem::where('id', $request->id)->delete();
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
            $error = ['modul' => 'delete_system',
                'actions' => 'Delete data msystem',
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
	 * Function: get data System search by type and cd
	 * body: 
	 * $request	:
	 **/
    public function get_system_type_cd(Request $request)
    {
        try{
            // dd($request->system_type);
            $data = Msystem::where('system_type','LIKE','%'.$request->system_type.'%')
                            ->where('system_cd','LIKE','%'.$request->system_cd.'%')
                            ->get();
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
            $error = ['modul' => 'get_system_type_cd',
                'actions' => 'Get data msystem',
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
	 * Function: get data System search by type and cd
	 * body: 
	 * $request	:
	 **/
    public function get_system_country()
    {
        try{
            // dd($request->system_type);
            $data = Msystem::getCountry();
            $count_country = count($data);
            if ($count_country==0){
                $data = null;
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => $data,
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
            $error = ['modul' => 'get_system_country',
                'actions' => 'Get data msystem',
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
	 * Function: get data System search by type and cd
	 * body: 
	 * $request	:
	 **/
    public function get_system_city()
    {
        try{
            // dd($request->system_type);
            $request =['system_type' => 'city',
                        'system_cd' => null];
            $data = Msystem::get_system_type_cd($request);
            // dd($data);
            $count_city = count($data);
            if ($count_city == 0){
                $data = null;
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => $data,
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
            $error = ['modul' => 'get_system_city',
                'actions' => 'Get data msystem',
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

    public function generate_sign()
    {
        try{
            $date = now();
            $nonce = random_bytes(32);
            $length = 32;
                $length = ($length < 4) ? 4 : $length;
                $nonce = bin2hex(random_bytes(($length-($length%2))/2));
                // dd($nonce);
            $header = ['nonce' => $nonce,
                        'timestamp' => $date,
                        'Content-Type' => 'application/json', 
                        'appId' => '50002THT01'];
            $body = json_encode($header);
            echo $date.'<br>';
                echo hash('sha256', $body);
                print_r($header);
                        // echo "SHA-256: ".crypt($body,'SHA256')."\n<br>"; 
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_system_city',
                'actions' => 'Get data msystem',
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
