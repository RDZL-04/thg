<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\HotelImages;
use App\Models\HotelFacility;
use App\Models\Reservation;
use App\Models\Msystem;
use App\Models\Review;
use App\Models\NearAttraction;
use App\Models\Facility;
use App\Http\Controllers\LOG\ErorrLogController;
use Validator;
use Illuminate\Support\Facades\Storage;
use File;

/*
|--------------------------------------------------------------------------
| Hotel API Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process hotel data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: December 21
*/
class HotelController extends Controller
{
    public function __construct() {
        $this->LogController = new ErorrLogController;
    }
    /*
	 * Function: get data hotel 
	 * body: 
	 *	$request	: string name
	 **/
    public function get_hotel_all()
    {
        
        try {      
            $data = Hotel::get_hotel_all();
            if($data == null || count($data) == 0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            else{
                /*
                 * input data hotel image to data hotel
                **/
                foreach ($data as $hotel) {
                    /**
                     * search data image by  id hotel
                     * and search data facility by id hotel
                     */
                    $hotel_image = HotelImages::get_image($hotel->id);
                    $hotel_facility = HotelFacility::get_facility($hotel->id);
                    $hotel_near = NearAttraction::get_near_hotels($hotel->id);
                    $get_ranting = Review::get_ranting($hotel->id);
                    //get data top review hotel
                    $get_top_review = Review::get_top_review($hotel->id);
                    $rating = $get_ranting['0'];
                        if(empty($get_top_review)){
                            $get_top_review=null;
                        }
                        else if(empty($get_ranting)){
                            $get_ranting=null;
                        }
                    $review = ['rating' => $rating->rating,
                    'top_review' => $get_top_review,];
                    $count_img = count($hotel_image);
                    $count_facility = count($hotel_facility);
                    //convert data to array object
                    $hotel = json_decode(json_encode($hotel), true); 
                    // dd($get);
                    if ($count_img==0){
                        $hotel_image = null;
                    }
                    if($count_facility == 0){
                        $hotel_facility = null;
                    }
                    if (empty($hotel_near)) { 
                        $hotel_near = null;
                    }
                    $hotel_near = ['hotel_near' => $hotel_near];
                    $hotel_image = ['hotel_image' => $hotel_image];
                    $hotel_facility = ['hotel_facility' => $hotel_facility];
                    $hotel_review = ['hotel_review' => $review];
                        //input data to array
                        $get_hotel[] = $hotel+$hotel_image+$hotel_facility+$hotel_near+$hotel_review;
                }
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $get_hotel,
                    
                ];
                return response()->json($response, 200);
            }
        } 
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_hotel_all',
                'actions' => 'get data hotel',
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
	 * Function: get data hotel active
	 * body: 
	 *	$request	: string name
	 **/
    public function get_hotel_active()
    {
        
        try {      
            $data = Hotel::get_hotel_active();
            if($data == null){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            /*
                 * input data hotel image to data hotel
                **/
                foreach ($data as $hotel) {
                    /**
                     * search data image by  id hotel
                     * and search data facility by id hotel
                     */
                    $hotel_image = HotelImages::get_image($hotel->id);
                    $hotel_facility = HotelFacility::get_facility($hotel->id);
                    $hotel_near = NearAttraction::get_near_hotels($hotel->id);
                    $get_ranting = Review::get_ranting($hotel->id);
                    //get data top review hotel
                    $get_top_review = Review::get_top_review($hotel->id);
                    $rating = $get_ranting['0'];
                        if(empty($get_top_review)){
                            $get_top_review=null;
                        }
                        else if(empty($get_ranting)){
                            $get_ranting=null;
                        }
                    $review = ['rating' => $rating->rating,
                    'top_review' => $get_top_review,];
                    $count_img = count($hotel_image);
                    $count_facility = count($hotel_facility);
                    //convert data to array object
                    $hotel = json_decode(json_encode($hotel), true); 
                    // dd($get);
                    if ($count_img==0){
                        $hotel_image = null;
                    }
                    if($count_facility == 0){
                        $hotel_facility = null;
                    }
                    if (empty($hotel_near)) { 
                        $hotel_near = null;
                    }
                    $hotel_near = ['hotel_near' => $hotel_near];
                    $hotel_image = ['hotel_image' => $hotel_image];
                    $hotel_facility = ['hotel_facility' => $hotel_facility];
                    $hotel_review = ['hotel_review' => $review];
                        //input data to array
                        $get_hotel[] = $hotel+$hotel_image+$hotel_facility+$hotel_near+$hotel_review;
                }
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $get_hotel,
                    
                ];
                return response()->json($response, 200);
        } 
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_hotel_active',
                'actions' => 'get data hotel',
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
	 * Function: get data hotel web app
	 * body: 
	 *	$request	: string name
	 **/
    public function get_hotel_all_web_app()
    {
        
        try {      
            $data = Hotel::get_hotel_all_web_app();
            if($data == null){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            /*
                 * input data hotel image to data hotel
                **/
                foreach ($data as $hotel) {
                    /**
                     * search data image by  id hotel
                     * and search data facility by id hotel
                     */
                    $hotel_image = HotelImages::get_image($hotel->id);
                    $hotel_facility = HotelFacility::get_facility($hotel->id);
                    $hotel_near = NearAttraction::get_near_hotels($hotel->id);
                    $get_ranting = Review::get_ranting($hotel->id);
                    //get data top review hotel
                    $get_top_review = Review::get_top_review($hotel->id);
                    $rating = $get_ranting['0'];
                        if(empty($get_top_review)){
                            $get_top_review=null;
                        }
                        else if(empty($get_ranting)){
                            $get_ranting=null;
                        }
                    $review = ['rating' => $rating->rating,
                    'top_review' => $get_top_review,];
                    $count_img = count($hotel_image);
                    $count_facility = count($hotel_facility);
                    //convert data to array object
                    $hotel = json_decode(json_encode($hotel), true); 
                    // dd($get);
                    if ($count_img==0){
                        $hotel_image = null;
                    }
                    if($count_facility == 0){
                        $hotel_facility = null;
                    }
                    if (empty($hotel_near)) { 
                        $hotel_near = null;
                    }
                    $hotel_near = ['hotel_near' => $hotel_near];
                    $hotel_image = ['hotel_image' => $hotel_image];
                    $hotel_facility = ['hotel_facility' => $hotel_facility];
                    $hotel_review = ['hotel_review' => $review];
                        //input data to array
                        $get_hotel[] = $hotel+$hotel_image+$hotel_facility+$hotel_near+$hotel_review;
                }
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $get_hotel,
                    
                ];
                return response()->json($response, 200);
        } 
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_hotel_all',
                'actions' => 'get data hotel',
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
	 * Function: get data hotel mice web app
	 * body: 
	 *	$request	: string name
	 **/
    public function get_mice_hotel_all_web_app(Request $request)
    {
        
        try {      
            if(empty($request->city))
                $data = Hotel::get_mice_hotel_all_web_app();
            else 
                $data = Hotel::get_hotel_mice_by_city($request->all());
            
            if($data == null){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }   
                $temp = [];
                foreach ($data as $k => $value) {
                    $temp[$value['id']] = ['id' => $value['id'], 'name' => $value['name'], 'address' => $value['address'], 'latitude' => $value['latitude'], 'longitude' => $value['longitude']];
                    $temp[$value['id']]['hotel_image'][] = $value['hotel_images'];
                }
                $temp = array_values($temp);
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $temp,
                    
                ];
                return response()->json($response, 200);
        } 
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_mice_hotel_all_web_app',
                'actions' => 'get data mice hotel',
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
	 * Function: get_hotel_all_with_user_outlet filtering user sesuai terdaftar di outlet user
	 * body: 
	 *	$request	: int user_id
	 **/
    public function get_hotel_all_with_user_outlet(Request $request)
    {
        // dd($request->user_id);
        try {      
            $data = Hotel::get_hotel_all_with_user_outlet($request->user_id);
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
            $error = ['modul' => 'get_hotel_all_with_user_outlet',
                'actions' => 'get data hotel',
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
	 * Function: get_hotel_active_with_user_outlet filtering user sesuai terdaftar di outlet user
	 * body: 
	 *	$request	: int user_id
	 **/
    public function get_hotel_active_with_user_outlet(Request $request)
    {
        // dd($request->user_id);
        try {      
            $data = Hotel::get_hotel_active_with_user_outlet($request->user_id);
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
            $error = ['modul' => 'get_hotel_active_with_user_outlet',
                'actions' => 'get data hotel',
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
	 * Function: add data hotel
	 * body:data hotel
	 *	$request	: 
	*/
    public function add_hotel(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'address' => 'required',
                'description' => 'required',
                'be_hotel_id' => 'required',
                'be_api_key' => 'required|unique:hotels',
                'be_secreet_key' => 'required',
                'hotel_star' => 'required',
                'status' => 'required',
                'mpg_merchant_id' => 'unique:hotels',
                'mpg_api_key' => 'required',
                'mpg_secreet_key' => 'required',
                'longitude' => 'required',
                'latitude' => 'required',
                'created_by' =>'required',
                'city' =>'required',
                'email_notification'=>'required',
                'mice_email'=>'required',
                'mice_wa'=>'required|numeric|digits_between:10,15',
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
            $insert= Hotel::add_hotel($request->all());
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
            $error = ['modul' => 'add_hotel',
                'actions' => 'Save data hotel',
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
    
    public function delete_hotel(Request $request)
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
            // get data transaction by hotel id
            $checked_transaction = Reservation::where('hotel_id', $request->id)->first();
            // check data transaction
            if($checked_transaction != null){
                $response = [
                    'status' => false,
                    'message' => __('message.data_deleted_failed_used_data'),
                    'code' => 200,
                    'data' => $request->all(), 
                    ];
                return response()->json($response, 200);
            }else{
                //delete data hotel,image, and hotel facility by id hotel
                $deletedRows = Hotel::where('id', $request->id)->delete();
                $deletedImages = HotelImages::where('hotel_id', $request->id)->delete();
                $deletedHotelFacility = HotelFacility::where('hotel_id', $request->id)->delete();
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
                            'message' => __('message.data_deleted_failed_error'),
                            'code' => 200,
                            'data' => $request->all(), 
                            ];
                return response()->json($response, 200);
            }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'delete_hotel',
                'actions' => 'Delete data hotel',
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
    
    public function edit_hotel(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'name' => 'required',
                'address' => 'required',
                'description' => 'required',
                'be_hotel_id' => 'required',
                'be_api_key' => 'required',
                'be_secreet_key' => 'required',
                'hotel_star' => 'required',
                'status' => 'required',
                'mpg_api_key' => 'required',
                'mpg_secreet_key' => 'required',
                'mpg_merchant_id' => 'max:50',
                'longitude' => 'required',
                'latitude' => 'required',
                'updated_by' => 'required',
                'city' =>'required',
                'email_notification'=>'required',
                'mice_email'=>'required',
                'mice_wa'=>'required|numeric|digits_between:10,15',
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
            
            $update= Hotel::update_hotel($request->all());
            // dd($update);
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
                            'message' => __('message.data_update_failed'),
                            'code' => 200,
                            'data' => null, 
                            ];
                return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'edit_hotel',
                'actions' => 'Edit data hotel',
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
	 * Function: add data images hotel
	 * body: param[id_hotel,name,image,seq_no]
	 *	$request	: 
	*/
    
    public function add_image_hotel(Request $request)
    {
        
        try{
            if(empty($request['oldImages'])){
                $validator = Validator::make($request->all(), [
                    'images' => 'required|image|mimes:jpeg,jpg,png|max:256',
                    'hotel_id' => 'required',
                    'name' => 'required',
                    'seq_no' => 'required',
                ]);
            }else{
                $validator = Validator::make($request->all(), [
                    'images' => 'image|mimes:jpeg,jpg,png|max:256',
                    'hotel_id' => 'required',
                    'name' => 'required',
                    'seq_no' => 'required',
                    'oldImages' => 'required'
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
                // Check Resolution
            // Check Image Resolution ke tabel M_System
            $dataResolution=[
                'system_type' => 'img_resolution',
                'system_cd' => 'hotel'
            ];
                $checkResolution = Msystem::get_system_type_cd($dataResolution);
                // dd($checkResolution['data']);
                // $body = $resMSystem->getBody();
                // $resMSystem = json_decode($body, true);
                
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
                    if(!empty($request['id'])){
                        $path = HotelImages::where('id', $request->id)->first();
                        $file_path = app_path($path['file_name']); 
                        //if file found delete image
                        if(File::exists($file_path)) File::delete($file_path);
                    }
                    
					//update sequence first;
					$this->update_sequence($request['hotel_id'], $request['seq_no']);
					
                    $images = $request->file('images');
                    $fileName = time().'.'.$images->extension();  
                    $loc = public_path('hotel-images');
                    $loct = $images->move($loc, $fileName);
                    $data = [
                        'id' =>$request['id'],
                        'hotel_id' => $request['hotel_id'],
                        'name' => $request['name'],
                        'seq_no' => $request['seq_no'],
                        'file_name' =>'hotel-images/'.$fileName
                    ];
            }
            else{
                $data = [
                    'id' =>$request['id'],
                    'hotel_id' => $request['hotel_id'],
                    'name' => $request['name'],
                    'seq_no' => $request['seq_no'],
                    'file_name' =>$request['oldImages']
                ];
            }
            $cek_seq = HotelImages::Where('hotel_id',$request['hotel_id'])
                                    ->Where('seq_no',$request['seq_no'])
                                    ->first();
                // dd($cek_seq);
                if($cek_seq==null){
                    $save= HotelImages::update_image($data);
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
                else if($cek_seq['id'] != $request['id'])
                {
                    $response = [
                        'status' => false,
                        'message' => __('message.seq_no_allready'),
                        'code' => 200,
                        'data' => null, 
                        ];
                return response()->json($response, 200);
                }
            $save= HotelImages::update_image($data);
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
            $error = ['modul' => 'add_image_hotel',
                'actions' => 'save data image hotel',
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
	 * Function: add data images hotel
	 * body: param[id_hotel,name,image,seq_no]
	 *	$request	: 
	*/
    public function edit_image_hotel(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,jpg|max:2048',
                'hotel_id' => 'required',
                'name' => 'required',
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
            $loc = public_path('hotel-images');
            //save image to storage
            $loct = $image->move($loc, $fileName);
            $data = [
                'hotel_id' => $request['hotel_id'],
                'name' => $request['name'],
                'seq_no' => $request['seq_no'],
                'file_name' =>'hotel-images/'.$fileName
            ];
            
            $save= HotelImages::add_image($data);
            if($save){
                $response = [
                      'status' => true,
                      'message' => __('message.edit-success'),
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
            $error = ['modul' => 'edit_image_hotel',
                'actions' => 'Edit data image hotel',
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
     * faunction : Delete data image hotel
     * body: param[id_image]
	 *$request	: 
     */
    public function delete_image_hotel(Request $request)
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
            $path = HotelImages::where('id', $request->id)->first();
            $file_path = app_path($path['file_name']); 
            //if file found delete image from storage
            if(File::exists($file_path)) File::delete($file_path);
            $deletedRows = HotelImages::where('id', $request->id)->delete();
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
            $error = ['modul' => 'delete_image_hotel',
                'actions' => 'delete data image hotel',
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
	 * Function: get data hotel by id
	 * body: param id
	 *	$request	: 
	*/
    
    public function get_hotel_id(Request $request)
    {
        try {
         $get = Hotel::get_hotel_id($request);
            $count = count($get);
            if($count == 0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => ['data' => null],
                ];
                return response()->json($response, 200);
            }else{
                /*
                 * input data hotel image to data hotel
                **/
                foreach ($get as $hotel) {
                    /**
                     * search data image by  id hotel
                     * and search data facility by id hotel
                     */
                    $hotel_image = HotelImages::get_image($hotel->id);
                    $hotel_facility = HotelFacility::get_facility($hotel->id);
                    $count_img = count($hotel_image);
                    $count_facility = count($hotel_facility);
                    //convert data to array object
                    $hotel = json_decode(json_encode($hotel), true); 
                    // dd($get);
                    if ($count_img==0){
                        $hotel_image = null;
                    }
                    if($count_facility == 0){
                        $hotel_facility = null;
                    }
                    $hotel_image = ['hotel_image' => $hotel_image];
                    $hotel_facility = ['hotel_facility' => $hotel_facility];
                        //input data to array
                        $get_hotel[] = $hotel+$hotel_image+$hotel_facility;
                }
                /**
                 * send response data hotel, image, facility
                 */
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => ['total_page' => $get->lastPage(),
                               'current_page' => $get->currentPage(),
                               'data' => $get_hotel],
                ];
                return response()->json($response, 200);    
                
            }        
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_hotel_id',
                'actions' => 'get data hotel',
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
	 * Function: get data hotel by id user
	 * body: param id
	 *	$request	: 
	*/
    
    public function get_hotel_user_id(Request $request)
    {
        try {
            // dd($request->user_id);
         $get = Hotel::get_hotel_user_id($request->user_id);
        //  dd($get);
            $count = count($get);
            if($count == 0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }else{
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $get,
                ];
                return response()->json($response, 200);    
                
            }        
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_hotel_user)id',
                'actions' => 'get data user hotel',
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
	 * Function: get data hotel main page
	 * body: param[id/name,page]
	 *	$request	: 
	*/
    
    public function get_hotel_images(Request $request)
    {
        try {
        /*
        * check id if null search by name
        * if not null search by id and name
        **/
            $get = Hotel::get_id_hotel();
            $count = count($get);
            if($count == 0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }else{
               
                /*
                 * input data hotel image to data hotel
                **/
                foreach ($get as $hotel) {
                    /**
                     * search data image by  id hotel
                     * and search data facility by id hotel
                     */
                    $hotel_image = HotelImages::get_image_by_seq($hotel->id);
                    $hotel = json_decode(json_encode($hotel), true); 
                    
                    $hotel_image = json_decode(json_encode($hotel_image), true); 
                    if ($hotel_image==null){
                        $hotel_image['file_name'] = null;
                    }
                    $data = [
                        'id_hotel' => $hotel['id'],
                        'image' => $hotel_image['file_name']];
                    // $hotel_facility = ['hotel_facility' => $hotel_facility];
                        //input data to array
                        $get_hotel[] =$data;
                }
                /**
                 * send response data hotel, image, facility
                 */
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $get_hotel,
                ];
                return response()->json($response, 200);    
                
            }        
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_hotel_images',
                'actions' => 'get data image hotel',
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
	 * Function: update sequence no start from specific number
	 * param: integer $start_seq 
	 * @author: Arkra (arif@arkamaya.co.id) 27/04/2021 09:06am
	*/
    private function update_sequence($hotel_id, $start_seq)
    {
		if($start_seq)
		{
			$data = HotelImages::where('hotel_id', $hotel_id)
								->where('seq_no','>=',$start_seq)
								->orderBy('seq_no', 'ASC')
								->get();
			
			$i = $start_seq + 1;
			
			foreach($data as $d)
			{
				$update = array('seq_no' => $i);
				
				HotelImages::whereId($d->id)->update($update);
				
				$i++;
			}
		}
	}
}
