<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\MiceCategory;
use App\Models\HallCategory;
use App\Models\Msystem;
use App\Http\Controllers\LOG\ErorrLogController;
use App\Models\Hotel;
use File;

/*
|--------------------------------------------------------------------------
| Mice Category API Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process Mice Category data 
| 
| @author: rangga.muharam@arkamaya.co.id 
| @update: March 30th, 2021
*/

class MiceController extends Controller
{
    
    public function __construct() {
        $this->LogController = new ErorrLogController;
    }

     /*
	 * Function: GET mice category from msystems
	 * body: 
	 *	$request	: 
	 **/
    public function get_mice_category_msystem()
    {
        try{
            $data = Msystem::get_mice_category();
            $response = [
                'status' => true,
                'message' => __('message.data_found' ),
                'code' => 200,
                'data' => $data, 
            ];
            return response()->json($response, 200);
        } catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_mice_category_msystem',
                'actions' => 'get data mice category from msystems',
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
	 * Function: GET list of Hotel that exists on Mice Category
	 * body: 
	 *	$request	: 
	 **/
    public function get_hotel_mice()
    {
        try{
            $data = Hotel::get_hotel_mice();
            $response = [
                'status' => true,
                'message' => __('message.data_found' ),
                'code' => 200,
                'data' => $data, 
            ];
            return response()->json($response, 200);
        } catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_mice_category_msystem',
                'actions' => 'get data mice category from msystems',
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
	 * Function: GET list of Hotel that exists on Mice Category with user id param
	 * body: 
	 *	$request	: 
	 **/
    public function get_hotel_mice_with_hotel_user(Request $request)
    {
        try{
            $data = Hotel::get_hotel_mice_with_hotel_user($request->user_id);
            $response = [
                'status' => true,
                'message' => __('message.data_found' ),
                'code' => 200,
                'data' => $data, 
            ];
            return response()->json($response, 200);
        } catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_mice_category_msystem',
                'actions' => 'get data mice category from msystems',
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
	 * Function: add mice category
	 * body: 
	 *	$request	: 
	 **/
    public function add_mice_category(Request $request)
    {
        try {    
            // dd($request->all());
            $validator = Validator::make($request->all(), [
                'hotel_id' => 'required',
                'category_id' => 'required',
                'description' => 'required',
                'images' => 'required|image|mimes:jpeg,jpg|max:1024',
                'created_by' => 'required'
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

            // Check Resolution
            // Check Image Resolution ke tabel M_System
            $dataResolution = [
                                'system_type' => 'img_resolution',
                                'system_cd' => 'hotel'
                              ];
            $checkResolution = Msystem::get_system_type_cd($dataResolution);
            foreach ($checkResolution as $value) {
                $resDimension[] = $value['system_value'];
            }
            // dd($resDimension);
            $data = getimagesize($request->images);
            $arrayDim[] = [$data[0].'x'.$data[1]];
    
            // dd($arrayDim[0][0]);
            if(!in_array($arrayDim[0][0],$resDimension)){
                $response = [
                    'status' => false,
                    'message' => __('message.dimension-erorr'),
                    'code' => 200,
                    'data' => null,
                ];
                return response()->json($response, 200); 
            }
            $images = $request->file('images');
            $fileName = time().'.'.$images->extension();  
            $loc = public_path('mice/mice-images');
            $loct = $images->move($loc, $fileName); 
            $fullImage = 'mice/mice-images/'.$fileName;
            $data =  [
                'hotel_id' => $request->hotel_id,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'images' => 'mice/mice-images/'.$fileName,
                'created_by' => $request->created_by,
                'updated_by' => $request->created_by
            ];
            // dd($data);
            // Cek Dulu hotel_id dan category_id nya ada atau tidak
            $cek = MiceCategory::where('hotel_id', '=', $request->hotel_id)
                                 ->where('category_id', '=', $request->category_id)
                                 ->get();
            if(count($cek) > 0) {
                $response = [
                    'status' => false,
                    'message' => __('message.data-redudant'),
                    'code' => 500,
                    'data' => $data,
                ];
                // Delete The File
                if(File::exists(public_path($fullImage))) {
                    File::delete(public_path($fullImage));
                }
                return response()->json($response, 200);   
            } else {
                $insert = MiceCategory::add_mice_category($data);
                    if($insert){
                            $response = [
                                    'status' => true,
                                    'message' => __('message.data_saved_success'),
                                    'code' => 200,
                                    'data' => $data,
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
        } 
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'add_mice_category',
                'actions' => 'save data mice category',
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
	 * Function: edit mice category
	 * body: 
	 *	$request	: 
	 **/
    public function edit_mice_category(Request $request)
    {
        try {    
            // dd($request->all());
            if(empty($request->oldImages)){
                $validator = Validator::make($request->all(), [
                    'id' => 'required',
                    'hotel_id' => 'required',
                    'category_id' => 'required',
                    'description' => 'required',
                    'images' => 'required|image|mimes:jpeg,jpg|max:1024',
                    'updated_by' => 'required'
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'id' => 'required',
                    'hotel_id' => 'required',
                    'category_id' => 'required',
                    'description' => 'required',
                    'oldImages' => 'required',
                    'updated_by' => 'required'
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

            if(empty($request->oldImages)){
                // Check Resolution
                // Check Image Resolution ke tabel M_System
                $dataResolution = [
                    'system_type' => 'img_resolution',
                    'system_cd' => 'hotel'
                ];
                $checkResolution = Msystem::get_system_type_cd($dataResolution);
                foreach ($checkResolution as $value) {
                    $resDimension[] = $value['system_value'];
                }
                // dd($resDimension);
                $data = getimagesize($request->images);
                $arrayDim[] = [$data[0].'x'.$data[1]];

                // dd($arrayDim[0][0]);
                if(!in_array($arrayDim[0][0],$resDimension)){
                    $response = [
                        'status' => false,
                        'message' => __('message.dimension-erorr'),
                        'code' => 200,
                        'data' => null,
                    ];
                    return response()->json($response, 200); 
                }
                // Cek file lama dan delete
                $cek = MiceCategory::where('id', '=', $request->id)->first();
                if(File::exists(public_path($cek['images']))) {
                    File::delete(public_path($cek['images']));
                }
                // upload File layout to folder
                $image = $request->file('images');
                $fileName = time().'.'.$image->extension();  
                $loc = public_path('mice/mice-images');
                //save image to storage
                $loct = $image->move($loc, $fileName);
                // Check Image lama lalu delete image lama
                $cek = MiceCategory::where('hotel_id', '=', $request->hotel_id)
                                    ->where('category_id', '=', $request->category_id)
                                    ->first();
                // dd($cek['images']);
                if(File::exists(public_path($cek['images']))) {
                    File::delete(public_path($cek['images']));
                }
                
                // construct data param to model
                $data =  [
                    'id' => $request->id,
                    'hotel_id' => $request->hotel_id,
                    'category_id' => $request->category_id,
                    'description' => $request->description,
                    'images' => 'mice/mice-images/'.$fileName,
                    'updated_by' => $request->updated_by
                ];
            } else {
                $data =  [
                    'id' => $request->id,
                    'hotel_id' => $request->hotel_id,
                    'category_id' => $request->category_id,
                    'description' => $request->description,
                    'images' => $request->oldImages,
                    'updated_by' => $request->updated_by
                ];
            }

            $insert = MiceCategory::update_mice_category($data);
                if($insert){
                          $response = [
                                'status' => true,
                                'message' => __('message.data_update_success'),
                                'code' => 200,
                                'data' => $data,
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
            $error = ['modul' => 'edit_mice_category',
                'actions' => 'edit data mice category',
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
     * function : Delete data Mice Category
     * body: param[id]
	 *$request	: 
     */
    public function delete_mice_category(Request $request)
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
            // Check category_id di hall_category, apakah masih dipakai oleh hall
            $checkCatId = HallCategory::where('mice_category_id', $request->id)->where('deleted_at', NULL)->get();
            if(count($checkCatId) == 0){
                $deletedRows = MiceCategory::where('id', $request->id)->delete();
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
            } else {
                // data di tabel hall_category masih aktif/dipakai oleh tabel hall
                $response = [
                    'status' => false,
                    'message' => __('message.data_still_have_transact'),
                    'code' => 200,
                    'data' => $request->all(), 
                    ];
                return response()->json($response, 200);
            }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'delete_mice_category',
                'actions' => 'delete data mice category',
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
     * function : Get All data Mice Category
     * body: 
     *$request	: 
     */
    public function get_all_mice_category(Request $request){
        try {
            if(!empty($request->user_id)){
                // get data Mice Category with hotel user
                $data = MiceCategory::get_all_mice_with_hotel_user($request->user_id);
            } else {
                $data = MiceCategory::get_all_mice();
            }
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_found' ),
                'data' => $data,
                
            ];
            return response()->json($response, 200);

        } catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get all mice category',
                'actions' => 'get all mice category',
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
     * function : Get All data Mice Category
     * body: 
     *$request	: 
     */
    public function get_all_mice_category_hotel_user_filter(Request $request){
        try {
            if(!empty($request->hotel_id) || (!empty($request->user_id))){
                // get data Mice Category with hotel user
                $data = MiceCategory::get_all_mice_category_hotel_user_filter($request);

            } else {
                $data = MiceCategory::get_all_mice($request);
            }
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_found' ),
                'data' => $data,
                
            ];
            return response()->json($response, 200);

        } catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get all mice category',
                'actions' => 'get all mice category',
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
	 * Function: get data Mice Category berdasarkan id_mice_category
	 * body: 
	 *	$request	: 
	 **/
    public function get_mice_category_detail(Request $request)
    {
        try{
            // dd($request->all());
            if(!empty($request->user_id)) {
                $data = MiceCategory::get_mice_detail_with_hotel_user($request->all());    
            } else {
                $data = MiceCategory::get_mice_detail($request->all());
            }
            if(count($data) != 0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $data,
                    
                ];
            } else {
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                    
                ];
            }
            return response()->json($response, 200);
        } catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_mice_category_getail',
                'actions' => 'get Data mice',
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
	 * Function: get data Mice Category berdasarkan id_mice_category
	 * body: 
	 *	$request	: 
	 **/
    public function get_mice_detail(Request $request)
    {
        try{
            // dd($request->all());
             $data = MiceCategory::get_mice_category_detail($request->all());
            if(count($data) != 0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $data,
                    
                ];
            } else {
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                    
                ];
            }
            return response()->json($response, 200);
        } catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_mice_category_getail',
                'actions' => 'get Data mice',
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
