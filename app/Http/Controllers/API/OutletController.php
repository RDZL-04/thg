<?php
/*
|--------------------------------------------------------------------------
| Hotel API Controller
|--------------------------------------------------------------------------
|
| This controller will process hotel data
| 
| @author: rangga.muharam@arkamaya.co.id 
| @update: December 28
*/

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Outlet;
use App\Models\OutletImages;
use App\Model\Reservation;
use App\Models\OutletMenus;
use App\Models\OutletUsers;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use File;
use Illuminate\Validation\Rule;
use App\Http\Controllers\LOG\ErorrLogController;

class OutletController extends Controller
{
    public function __construct() {
        $this->LogController = new ErorrLogController;
    }
     /*
	 * Function: get data outlet all
	 * body: 
	 *	$request	: string name
	 **/
    public function get_outlet_all()
    {
        try {      
            //dd($request->hotel_id);
            $data = Outlet::get_outlet_all();
            if($data == null || count($data) == 0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            
            foreach ($data as $outlet) {
                $outlet_image = OutletImages::get_image_outlet($outlet->id);
                // $count_img = count($outlet_image);
                // dd($outlet_image);
                $outlet = json_decode(json_encode($outlet), true); 
                // print_r($outlet);
                // print_r($outlet);
                if (empty($outlet_image)){
                    $outlet_image = null;
                }
                $outlet_image = ['outlet_image' => $outlet_image];
                $get_outlet[] = $outlet+$outlet_image;
            }
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_found' ),
                'data' => $get_outlet,
            ];
            return response()->json($response, 200); 
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_outlet_all',
                'actions' => 'get data outlet all',
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
	 * Function: get data outlet all active
	 * body: 
	 *	$request	: string name
	 **/
    public function get_outlet_active()
    {
        try {      
            //dd($request->hotel_id);
            $data = Outlet::get_outlet_active();
            if($data == null){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            
            foreach ($data as $outlet) {
                $outlet_image = OutletImages::get_image_outlet($outlet->id);
                // $count_img = count($outlet_image);
                // dd($outlet_image);
                $outlet = json_decode(json_encode($outlet), true); 
                // print_r($outlet);
                // print_r($outlet);
                if (empty($outlet_image)){
                    $outlet_image = null;
                }
                $outlet_image = ['outlet_image' => $outlet_image];
                $get_outlet[] = $outlet+$outlet_image;
            }
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_found' ),
                'data' => $get_outlet,
            ];
            return response()->json($response, 200); 
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_outlet_all',
                'actions' => 'get data outlet all',
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
	 * Function: get data outlet all for web
	 * body: 
	 *	$request	: string name
	 **/
    public function get_outlet_all_web()
    {
        try {      
            //dd($request->hotel_id);
            $data = Outlet::get_outlet_all_web();
            if(count($data) == 0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            
            foreach ($data as $outlet) {
                $outlet_image = OutletImages::get_image_outlet($outlet->id);
                // $count_img = count($outlet_image);
                // dd($outlet_image);
                $outlet = json_decode(json_encode($outlet), true); 
                // print_r($outlet);
                // print_r($outlet);
                if (empty($outlet_image)){
                    $outlet_image = null;
                }
                $outlet_image = ['outlet_image' => $outlet_image];
                $get_outlet[] = $outlet+$outlet_image;
            }
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_found' ),
                'data' => $get_outlet,
            ];
            return response()->json($response, 200); 
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_outlet_all',
                'actions' => 'get data outlet all',
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
	 * Function: get data outlet all sesuai user_id
	 * body: 
	 *	$request	: string name
	 **/
    public function get_outlet_all_with_user(Request $request)
    {
        try {      
            // dd($request->user_id);
            $data = Outlet::get_outlet_all_with_user($request->user_id);
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
            $error = ['modul' => 'get_outlet_all_with_user',
                'actions' => 'get data outlet all with user',
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
	 * Function: get data outlet active sesuai user_id
	 * body: 
	 *	$request	: string name
	 **/
    public function get_outlet_active_with_user(Request $request)
    {
        try {      
            // dd($request->user_id);
            $data = Outlet::get_outlet_active_with_user($request->user_id);
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
            $error = ['modul' => 'get_outlet_active_with_user',
                'actions' => 'get data outlet active with user',
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
	 * Function: get data outlet berdasarkan hotel_id
	 * body: 
	 *	$request	: string name
	 **/
    public function get_hotel_outlet(Request $request)
    {
        try {      
            //dd($request->hotel_id);
            $data = Outlet::get_hotel_outlet($request->hotel_id);
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
            $error = ['modul' => 'get_hotel_outlet',
                'actions' => 'get data outlet by hotel',
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
	 * Function: get data outlet berdasarkan hotel_id dan user_id
	 * body: 
	 *	$request	: string name
	 **/
    public function get_hotel_outlet_with_user(Request $request)
    {
        try {      
            //dd($request->hotel_id);
            $data = Outlet::get_hotel_outlet_with_user($request);
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
            $error = ['modul' => 'get_hotel_outlet_with_user',
                'actions' => 'get data outlet hotel with user',
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
	 * Function: get data outlet detail berdasarkan outlet_id
	 * body: 
	 *	$request	: string name
	 **/
    public function get_outlet_detail(Request $request)
    {
        try {      
            $data = Outlet::get_outlet_detail($request->outlet_id);
            $data_images = OutletImages::get_image($request->outlet_id);
            $data_menu = OutletMenus::get_menus($request->outlet_id);
            $data_outlet_user = OutletUsers::get_outlet_user($request->outlet_id);
            $count_img = count($data_images);
            $count_menu = count($data_menu);
            $count_user = count($data_outlet_user);
            if ($count_img==0){
                $data_images = null;
            }
            if ($count_menu==0){
                $data_menu = null;
            }
            if ($count_user==0){
                $data_outlet_user = null;
            }
            $data = json_decode(json_encode($data), true);
          
            $data["outlet_images"] = json_decode($data_images);
            $data["outlet_menus"] = json_decode($data_menu);
            $data["outlet_user"] = json_decode($data_outlet_user);
            // $data_images["outlet_images"] = json_decode($data_images);
            // $data_menu["outlet_menus"] = json_decode($data_menu);
            // $datax[] = $data . $data_images . $data_menu;
            // $data = json_decode(json_encode($data), true);
            // $data = $data + ["outlet_images" => $data_images,
            //         "outlet_menus" => $data_menu,
            //         "outlet_user" => $data_outlet_user];
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
            $error = ['modul' => 'get_outlet_detail',
                'actions' => 'get data outlet detail',
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
	 * Function: get data outlet menu berdasarkan outlet_id
	 * body: 
	 *	$request	: string name
	 **/
    public function get_outlet_menu(Request $request)
    {
        try {      
            $data_menu = OutletMenus::get_menus($request->outlet_id);
            $count_menu = count($data_menu);
            if ($count_menu==0){
                $data = null;
            }
            $data = json_decode($data_menu);
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
            $error = ['modul' => 'get_outlet_menu',
                'actions' => 'get data outlet menu',
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
	 * Function: get data outlet images berdasarkan outlet_id
	 * body: 
	 *	$request	: string name
	 **/
    public function get_outlet_images(Request $request)
    {
        try {      
            $data_menu = OutletImages::get_image($request->outlet_id);
            $count_menu = count($data_menu);
            if ($count_menu==0){
                $data = null;
            }
            $data = json_decode($data_menu);
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
            $error = ['modul' => 'get_outlet_menu',
                'actions' => 'get data outlet menu',
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
	 * Function: add data outlet
	 * body:data outlet
	 *	$request	: 
	*/
    public function add_outlet(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'hotel_id' => 'required',
                'name' => 'required|max:100|regex:/^[A-Za-z0-9 ]+$/',
                'address' => 'required|max:200|regex:/^[A-Za-z0-9-_!,. ]+$/',
                'description' => 'required|regex:/^[\r\nA-Za-z0-9-_!:;,.\'&# ]+$/',
                'status' => 'required',
                'seq_no' => 'required|unique:fboutlets,seq_no,NULL,id,hotel_id,'.$request->hotel_id.',deleted_at,NULL',
                // 'seq_no' => 'required|unique:fboutlets,seq_no,NULL,id,deleted_at,null',
                // 'mpg_merchant_id' => 'required|unique:fboutlets',
                'mpg_api_key' => 'required|max:50',
                'mpg_secret_key' => 'required|max:50',
                'longitude' => 'required|max:50',
                'latitude' => 'required',
                'tax'=> 'required|numeric',
                'service'=> 'required|numeric',
                'open_at'=> 'required',
                'close_at'=> 'required',
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
            $insert = Outlet::add_outlet($request->all());
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
            $error = ['modul' => 'add_outlet',
                'actions' => 'add data outlet',
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
	 * Function: ediy data hotel
	 * body: data hotel
	 *	$request	: 
	*/
    
    public function edit_outlet(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'hotel_id' => 'required',
                'name' => 'required|max:100|regex:/^[A-Za-z0-9 ]+$/',
                'address' => 'required|max:200|regex:/^[A-Za-z0-9-_!,. ]+$/',
                'description' => 'required|regex:/^[\r\nA-Za-z0-9-_!:;,.\'&# ]+$/',
                'status' => 'required',
                'seq_no' => 'required',
                // 'seq_no' => 'required|unique:fboutlets,seq_no,NULL,id,hotel_id,'.$request->hotel_id.',id,'.$request->id,
                // 'mpg_merchant_id' => 'required|max:50',
                'mpg_api_key' => 'required|max:50',
                'mpg_secret_key' => 'required|max:50',
                'longitude' => 'required',
                'latitude' => 'required',
                'tax'=> 'required|numeric',
                'service'=> 'required|numeric',
                'open_at'=> 'required',
                'close_at'=> 'required',
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
            if(!empty($request->id)){
                $cek_seq = Outlet::Where('seq_no', $request['seq_no'])
                                            ->where('hotel_id',$request['hotel_id'])
                                            ->where('deleted_at',null)
                                            ->where('id','!=',$request['id'])
                                            ->get();
                if(count($cek_seq)!=0){
                    $response = [
                        'status' => false,
                        'code' => 400,
                        'message' => __('message.seq_no_allready' ),
                        'data' => null,
                    ];
                    return response()->json($response, 200);
                }
            }
            $update= Outlet::update_outlet($request->all());
            // dd($update);
                if($update){
                          $response = [
                                'status' => true,
                                'message' => __('message.data_saved_success'),
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
            $error = ['modul' => 'edit_outlet',
                'actions' => 'edit data outlet',
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
    
    public function delete_outlet(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'outlet_id' => 'required',]);
            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
              $deletedRows = Outlet::where('id', $request->outlet_id)->delete();
                $deletedImages = OutletImages::where('fboutlet_id', $request->outlet_id)->first();
                if($deletedImages != null){
                    $file_path = public_path($deletedImages['filename']); 
                    //if file found delete image from storage
                    if(File::exists($file_path)) File::delete($file_path);
                }
                $deletedImages = OutletImages::where('fboutlet_id', $request->outlet_id)->delete();
                $deletedOutletMenus = OutletMenus::where('fboutlet_id', $request->outlet_id)->delete();
                if($deletedRows){
                          $response = [
                                'status' => true,
                                'message' => __('message.data_saved_success'),
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
            //}
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'delete_outlet',
                'actions' => 'delete data outlet',
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
	 * Function: get_image_outlet_detail
	 * body: param[id] => outlet_id
	 *	$request	: 
	*/
    
    public function get_image_outlet(Request $request)
    {
        try{
            $response= OutletImages::where('id',$request->id)->first();
            return $response;
        } catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_image_outlet',
                'actions' => 'get data image outlet',
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
	 * Function: add data images outlet
	 * body: param[fboutlet_id,name,filename,seq_no]
	 *	$request	: 
	*/
    
    public function add_image_outlet(Request $request)
    {
        
        try{
            if(empty($request['jenis'])){
                if(empty($request['oldImages'])){
                    $validator = Validator::make($request->all(), [
                        'images' => 'required|mimes:jpeg,jpg|max:256',
                        'fboutlet_id' => 'required',
                        'name' => 'required|max:50|regex:/^[A-Za-z0-9 ]+$/',
                        'seq_no' => 'required'
                        // 'seq_no' => 'required|unique:fboutlet_images,seq_no,NULL,id,fboutlet_id,'.$request->fboutlet_id
                    ]);
                }else{
                    $validator = Validator::make($request->all(), [
                        'fboutlet_id' => 'required',
                        'name' => 'required|max:50|regex:/^[A-Za-z0-9 ]+$/',
                        'seq_no' => 'required',
                        // 'seq_no' => 'required|unique:fboutlet_images,seq_no,NULL,id,fboutlet_id,'.$request->fboutlet_id,
                        'oldImages' => 'required'
                    ]);
                }
            } else {
                $validator = Validator::make($request->all(), [
                    'images' => 'required|mimes:jpeg,jpg|max:256',
                    'fboutlet_id' => 'required',
                    'name' => 'required|max:50|regex:/^[A-Za-z0-9 ]+$/',
                    'seq_no' => 'required|unique:fboutlet_images,seq_no,NULL,id,fboutlet_id,'.$request->fboutlet_id
                ]);
            }
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
            
            if(!empty($request['images'])){
                if(!empty($request['id'])){
                    $path = OutletImages::where('id', $request->id)->first();
                    $file_path = app_path($path['filename']); 
                    //if file found delete image
                    if(File::exists($file_path)) File::delete($file_path);
                }
                $images = $request->file('images');
                $fileName = time().'.'.$images->extension();  
                $loc = public_path('outlet-images');
                $loct = $images->move($loc, $fileName);
                $data = [
                    'id' =>$request['id'],
                    'fboutlet_id' => $request['fboutlet_id'],
                    'name' => $request['name'],
                    'seq_no' => $request['seq_no'],
                    'filename' =>'outlet-images/'.$fileName
                ];
            }
            else{
                $data = [
                    'id' =>$request['id'],
                    'fboutlet_id' => $request['fboutlet_id'],
                    'name' => $request['name'],
                    'seq_no' => $request['seq_no'],
                    'filename' =>$request['oldImages']
                ];
            }
            // dd($data);
            
            $save= OutletImages::update_image($data);
            if($save){
                $response = [
                      'status' => true,
                      'message' => __('message.data_saved_success'),
                      'code' => 200,
                      'data' => $save,
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
            $error = ['modul' => 'add_image_outlet',
                'actions' => 'add data image outlet',
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
	 * Function: edit data images outlet
	 * body: param[fboutlet_id,name,filename,seq_no]
	 *	$request	: 
	*/
    
    public function edit_image_outlet(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'filename' => 'required|mimes:jpeg,jpg|max:256',
                'txtOutletId' => 'required',
                'name' => 'required|max:50|regex:/^[A-Za-z0-9 ]+$/',
                'seq_no' => 'required',
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
            $image = $request->file('image');
            $fileName = time().'.'.$image->extension();  
            $loc = public_path('outlet-images');
            dd($loc);
            //save image to storage
            $loct = $image->move($loc, $fileName);
            $data = [
                'fboutlet_id' => $request['fboutlet_id'],
                'name' => $request['name'],
                'seq_no' => $request['seq_no'],
                'filename' =>'outlet-images/'.$fileName
            ];
            $save= OutletImages::add_image($data);
            if($save){
                $response = [
                      'status' => true,
                      'message' => __('message.data_saved_success'),
                      'code' => 200,
                      'data' => $save,
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
            $error = ['modul' => 'edit_image_outlet',
                'actions' => 'edit data image outlet',
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
     * faunction : Delete data image outlet
     * body: param[id_image]
	 *$request	: 
     */
    public function delete_image_outlet(Request $request)
    {
        
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required',]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            //get path image
            $path = OutletImages::where('id', $request->id)->first();
            $file_path = public_path($path['file_name']); 
            //if file found delete image from storage
            if(File::exists($file_path)) File::delete($file_path);
            $deletedRows = OutletImages::where('id', $request->id)->forceDelete();
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
            $error = ['modul' => 'delete_image_outlet',
                'actions' => 'delete data image outlet',
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
	 * Function: get_image_seq sesuai outlet_id
	 * body: param[fboutlet_id,]
	 *	$request	: 
	*/
    
    public function get_image_seq(Request $request)
    {
        try{
            $response= OutletImages::where('fboutlet_id',$request->id)
                                     ->where('deleted_at','=',null)
                                     ->get();
            return $response;
        } catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_image_seq',
                'actions' => 'get data image outlet by seq no',
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
	 * Function: get_outlet_users sesuai outlet_id
	 * body: param[fboutlet_id,]
	 *	$request	: 
	*/
    
    public function get_outlet_user(Request $request)
    {
        try{
            $response= OutletUsers::get_outlet_user($request->fboutlet_id);
            return $response;
        } catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_outlet_user',
                'actions' => 'get data user outlet',
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
	 * Function: add data outlet user
	 * body:data outlet
	 *	$request	: 
	*/
    public function save_outlet_user(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'fboutlet_id' => 'required',
                'user_id' => 'required',
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
            $insert = OutletUsers::save_outlet_user($request->all());
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
            $error = ['modul' => 'save_outlet_user',
                'actions' => 'save data user outlet',
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
	 * Function: get_outlet_user_avail sesuai outlet_id (User Available yg dapat ditambahan ke User Outlet)
	 * body: param[fboutlet_id,]
	 *	$request	: 
	*/
    
    public function get_outlet_user_avail(Request $request)
    {
        try{
            $data= OutletUsers::get_outlet_user_avail($request->fboutlet_id);
            if(count($data) == 0){
                $data = null;
            }
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_found' ),
                'data' => $data 
            ];
            return $response;
        } catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_outlet_user_avail',
                'actions' => 'get data user outlet avail',
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
	 * Function: delete_outlet_user, Soft Delete Outlet User
	 * body: param[id] tabel fboutlet_users
	 *	$request	: 
	*/
    
    public function delete_outlet_user(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required',]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            $deletedRows = OutletUsers::where('id', $request->id)->delete();
            if($deletedRows){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_deleted_success'),
                    'data' => null 
                ];
                return $response;
            } else {
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            
           
        } catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'delete_outlet_user',
                'actions' => 'delete data user outlet',
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
	 * Function: Search User Outlet, 
	 * body: 
	 *	$request: user_id dan fboutlet_id Mandatory 
	*/
    
    public function search_outlet_user(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'fboutlet_id' => 'required'
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
            $rows = OutletUsers::where('user_id', $request->user_id)
                                        ->where('fboutlet_id',$request->fboutlet_id)
                                        ->first();
            if($rows){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $rows 
                ];
                return $response;
            } else {
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            
           
        } catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'delete_outlet_user',
                'actions' => 'delete data user outlet',
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

    public function get_outlet_city(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'id_city' => 'required|numeric',]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 400);
            }
            // dd($request['city']);
            $data = Outlet::get_outlet_city($request['id_city']);
            // dd($data);
            if(empty($data)){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            else{
                foreach ($data as $outlet) {
                    $outlet_image = OutletImages::get_image_outlet($outlet->id);
                    // $count_img = count($outlet_image);
                    // dd($outlet_image);
                    $outlet = json_decode(json_encode($outlet), true); 
                    // print_r($outlet);
                    // print_r($outlet);
                    if (empty($outlet_image)){
                        $outlet_image = null;
                    }
                    $outlet_image = ['outlet_image' => $outlet_image];
                    $get_outlet[] = $outlet+$outlet_image;
                }
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $get_outlet,
                ];
                return response()->json($response, 200); 
            }
            
           
        } catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_outlet_city',
                'actions' => 'get data outlet city',
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
     * Function: select outlet waiters
	 * body: id_user, id_outlet
	 *	$request	: 
     */

    public function select_outlet(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'id_user' => 'required',
                // 'id_outlet' => 'required',
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
            if($request['id_outlet'] == null || empty($request['id_outlet'])){
                $data = User::where('id',$request['id_user'])->first();
                if($data != null){
                    $updateOutlet = OutletUsers::unactivated_outlet($request['id_user']);
                    if($updateOutlet){
                        if($updateOutlet){
                            $response = [
                                'status' => true,
                                'code' => 200,
                                'message' => __('message.hotel_selected_success' ),
                                'data' => $data,
                            ];
                        }
                    }else{
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => __('message.hotel_selected_failed' ),
                            'data' => null,
                        ];
                    }
                }else{
                    $response = [
                        'status' => false,
                        'code' => 400,
                        'message' => __('message.hotel_selected_failed' ),
                        'data' => null,
                    ];
                }
            }else{
                $data = User::where('id',$request['id_user'])->first();
                $data2 = OutletUsers::where('user_id',$request['id_user'])
                                    ->where('fboutlet_id',$request['id_outlet'])->first();
                                    
                if($data != null && $data2 != null){
                    $updateOutlet = OutletUsers::active_outlet($request['id_user'],$request['id_outlet']);
                    // dd($updateOutlet);
                    if($updateOutlet){
                        $response = [
                            'status' => true,
                            'code' => 200,
                            'message' => __('message.hotel_selected_success' ),
                            'data' => $data,
                        ];
                    }else{
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => __('message.hotel_selected_failed' ),
                            'data' => null,
                        ];
                    }                    
                }else{
                    $response = [
                        'status' => false,
                        'code' => 400,
                        'message' => __('message.hotel_selected_failed' ),
                        'data' => null,
                    ];
                }
            }
            return response()->json($response, 200);           
        } catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'select_outlet',
                'actions' => 'Select outlet',
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
     * Function: select outlet waiters
	 * body: id_user, id_outlet
	 *	$request	: 
     */

    public function get_waiters_outlet(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'id_user' => 'required',
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
            $check_user_outlet = OutletUsers::where('user_id', $request['id_user'])->get();
            if(count($check_user_outlet) !=0){
                $data_outlet=[];
                foreach ($check_user_outlet as $outlet_user){
                    $outlet = $outlet_user;
                    $outletDetail = Outlet::Where('id',$outlet_user['fboutlet_id'])->first();
                    $image_outlet = OutletImages::Where('fboutlet_id',$outlet_user['fboutlet_id'])->first();
                    if($image_outlet == null){
                        $outlet['outlet_images'] = null;
                    }else{
                        $outlet['outlet_images'] = $image_outlet['filename'];
                    }            
                    $outlet['name_outlet'] = $outletDetail['name'];
                    array_push($data_outlet,$outlet);
                }
                if(!empty($data_outlet)){
                    $response = [
                        'status' => true,
                        'code' => 200,
                        'message' => __('message.data_found' ),
                        'data' => $data_outlet,
                    ];
                }else{
                    $response = [
                        'status' => false,
                        'code' => 400,
                        'message' => __('message.data_not_found' ),
                        'data' => null,
                    ];
                }
            }else{
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
            }
            return response()->json($response, 200);           
        } catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'select_outlet',
                'actions' => 'Select outlet',
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
