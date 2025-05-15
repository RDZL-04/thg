<?php
/*
|--------------------------------------------------------------------------
| Hall API Controller including HallCategory dan HallImages
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process Hall, Hall Category and Hall Images
| 
| @author: rangga.muharam@arkamaya.co.id 
| @update: April 1st, 2021
*/

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use File;
use App\Http\Controllers\LOG\ErorrLogController;
use App\Models\Halls;
use App\Models\HallCategory;
use App\Models\HallImages;
use App\Models\MiceCategory;
use App\Models\Msystem;

class HallController extends Controller
{
    // Construct Class
    public function __construct() {
        $this->LogController = new ErorrLogController;
    }

    /*
	 * Function: get hall and hall_category all
	 * body: 
	 *	$request	: 
	 **/
    public function get_hall_all(Request $request)
    {
        try {
            if(!empty($request->user_id)){
                // get data Hall dan Hall Category with hotel user
                $data = Halls::get_all_hall_with_hotel_user($request->user_id);
                $dataCategory = Halls::get_mice_category_with_hotel_user($request->user_id);
                $dataArrayCategory =  json_decode($dataCategory,true);
                // dd($dataArrayCategory);
            } else {
                // get data Hall dan Hall Category For ADMIN
                $data = Halls::get_all_hall();
                $dataCategory = Halls::get_mice_category_with_all_hall();
                $dataArrayCategory =  json_decode($dataCategory,true);
            }
                $dataArray = array();
                if(count($data) == count($dataCategory))
                {
                    foreach(json_decode($data) as $value1){
                        foreach(json_decode($dataCategory) as $key=>$value2) {
                            if($value1->id == $value2->id)
                            {
                                $dataArray[$value1->id] = [
                                    'id' => $value1->id,
                                    'name' => $value1->name,
                                    'descriptions' => $value1->descriptions,
                                    'capacity' => $value1->capacity,
                                    'size' => $value1->size,
                                    'layout' => $value1->layout,
                                    'mice_offers' => $value1->mice_offers,
                                    'seq' => $value1->seq,
                                    'created_by' => $value1->created_by,
                                    'updated_by' => $value1->updated_by,
                                    'created_at' => $value1->created_at,
                                    'updated_at' => $value1->updated_at,
                                    'deleted_at' => $value1->deleted_at,
                                    'hotel_name' => $value1->hotel_name,
                                ];
                            }
                        }
                    }
                }
               $dataArray = array_values($dataArray);
                
            
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_found' ),
                'data' => $dataArray,
                'dataCategory' => $dataArrayCategory
                
            ];
            return response()->json($response, 200);
            
        } 
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_hall_all',
                'actions' => 'get data hall and hall category to view',
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
	 * Function: get hall and hall_category all based from Hotel ID
	 * body: 
	 *	$request	: 
	 **/
    public function get_hall(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'hotel_id' => 'required'
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
            if(!empty($request->user_id)){
                // get data Hall dan Hall Category with hotel user
                $data = Halls::get_hall_with_hotel_user($request);
                $dataCategory = Halls::get_mice_category_with_hotel_user_hotel_id($request);
                $dataArrayCategory =  json_decode($dataCategory,true);
                // dd($dataArrayCategory);
            } else {
                // get data Hall dan Hall Category For ADMIN
                $data = Halls::get_hall($request);
                $dataCategory = Halls::get_mice_category_with_hall_hotel_id($request);
                $dataArrayCategory =  json_decode($dataCategory,true);
            }
                $dataArray = array(); $dataUdin = array();
                if(count($data) == count($dataCategory))
                {
                    foreach(json_decode($data) as $value1){
                        foreach(json_decode($dataCategory) as $key=>$value2) {
                            if($value1->id == $value2->id)
                            {
                                $dataArray[$value1->id] = [
                                    'id' => $value1->id,
                                    'name' => $value1->name,
                                    'descriptions' => $value1->descriptions,
                                    'capacity' => $value1->capacity,
                                    'size' => $value1->size,
                                    'layout' => $value1->layout,
                                    'mice_offers' => $value1->mice_offers,
                                    'seq' => $value1->seq,
                                    'created_by' => $value1->created_by,
                                    'updated_by' => $value1->updated_by,
                                    'created_at' => $value1->created_at,
                                    'updated_at' => $value1->updated_at,
                                    'deleted_at' => $value1->deleted_at,
                                    'hotel_name' => $value1->hotel_name
                                ];
                            }
                        }
                    }
                }
               $dataArray = array_values($dataArray);
            
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_found' ),
                'data' => $dataArray,
                'dataCategory' => $dataArrayCategory
                
            ];
            return response()->json($response, 200);
            
        } 
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_hall',
                'actions' => 'get data hall and hall category to view based from Hotel ID',
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
	 * Function: add hall and hall_category
	 * body: 
	 *	$request	: 
	 **/
    public function add_hall(Request $request)
    {
        try {
            // dd($request->all());
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'mice_category_id' => 'required',
                'description' => 'required',
                'capacity' => 'required|numeric|min:0|not_in:0|max:9999999999',
                'size' => 'required',
                'seq' => 'required|numeric|min:0|not_in:0',
                'layout' => 'required|mimes:jpeg,jpg,pdf|max:2048',
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

            // Check jika file mice_offers ada dan tidak null
            if( (!empty($request->file('mice_offers'))) && ($request->file('mice_offers') != null)) {
                $validatorOffers = Validator::make($request->all(), [
                    'mice_offers' => 'mimes:jpeg,jpg,pdf|max:2048'
                ]);
                // Validation
                if ($validatorOffers->fails()) {
                    // return response gagal
                    $response = [
                        'status' => false,
                        'code' => 400,
                        'message' => $validator->errors()->first(), 
                        'data' =>null,
                    ];
                    return response()->json($response, 200);
                }
            }

            // Cek sequence dulu di table Hall
            $cek_seq = Halls::where('seq', '=', $request->seq)->get();
            if(count($cek_seq) > 0) {
                $response = [
                    'status' => false,
                    'message' => __('message.seq_no_allready'),
                    'code' => 200,
                    'data' => null, 
                    ];
                return response()->json($response, 200);
            }
            // upload File layout to folder
            $image = $request->file('layout');
            $fileName = time().'.'.$image->extension();  
            $loc = public_path('mice/layout');
            //save image to storage
            $loct = $image->move($loc, $fileName);

            // Check jika file mice_offers ada dan tidak null
            if( (!empty($request->file('mice_offers'))) && ($request->file('mice_offers') != null)) {
                  // upload File mice_offers to folder
                $mice_offers = $request->file('mice_offers');
                $fileNameMiceOffers = time().'.'.$mice_offers->extension();  
                $locMiceOffers = public_path('mice/mice-offers');
                //save image to storage
                $loctMiceOffers = $mice_offers->move($locMiceOffers, $fileNameMiceOffers);
            }

            // construct data param to model
            $data = [
                'name' => $request['name'],
                'description' => $request['description'],
                'capacity' => $request['capacity'],
                'size' => $request['size'],
                'seq' => $request['seq'],
                'created_by' => $request->created_by,
                'updated_by' => $request->created_by,
                'layout' =>'mice/layout/'.$fileName
            ];

            // Check jika file mice_offers add
            if( (!empty($request->file('mice_offers'))) && ($request->file('mice_offers') != null)) {
                // insert key mice_offers ke data
                $data['mice_offers'] = 'mice/mice-offers/'.$fileNameMiceOffers;
            }

            $insert = Halls::add_hall($data);
            if($insert) {
                    $mice_cat_id = $request->mice_category_id;
                    foreach($mice_cat_id as $cat_id){
                        // Cek mice_category_id exists, baru insert ke Hall
                        $cek = MiceCategory::where('id', '=', $cat_id)
                                            ->get();
                        if(count($cek) > 0){
                            $requestHallCat = [
                                'mice_category_id' => $cat_id,
                                'hall_id' => $insert->id,
                                'created_by' => $request->created_by,
                                'updated_by' => $request->created_by
                            ];
                            $inserHallCat = HallCategory::add_hall_category($requestHallCat);
                        }
                    }
                    $response = [
                            'status' => true,
                            'message' => __('message.data_saved_success'),
                            'code' => 200,
                            'data' => $insert
                    ];
                    return response()->json($response, 200);   
                    
            }
            $response = [
                        'status' => false,
                        'message' => __('message.failed_save_data'),
                        'code' => 200,
                        'data' => null
                        ];
            return response()->json($response, 200);
            
        } 
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'add_hall',
                'actions' => 'save data hall and hall category',
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
	 * Function: edit Hall 
	 * body: 
	 *	$request	: 
	 **/
    public function edit_hall(Request $request)
    {
        try {    
            // Jika Layout merupakan file baru
            if(empty($request->oldLayout)){
                $validator = Validator::make($request->all(), [
                    'id' => 'required',
                    'name' => 'required',
                    'mice_category_id' => 'required',
                    'description' => 'required',
                    'capacity' => 'required|numeric|min:0|not_in:0|max:9999999999',
                    'size' => 'required',
                    'seq' => 'required|numeric|min:0|not_in:0',
                    'layout' => 'required|mimes:jpeg,jpg,pdf|max:2048',
                    'updated_by' => 'required'
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'id' => 'required',
                    'name' => 'required',
                    'mice_category_id' => 'required',
                    'description' => 'required',
                    'capacity' => 'required|numeric|min:0|not_in:0|max:9999999999',
                    'size' => 'required',
                    'seq' => 'required|numeric|min:0|not_in:0',
                    'oldLayout' => 'required',
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
            
            // Check jika mice_offers merupakan file baru
            if(empty($request->oldMiceOffers)){
                 // Check jika file mice_offers ada dan tidak null
                if( (!empty($request->file('mice_offers'))) && ($request->file('mice_offers') != null)) {
                    $validatorOffers = Validator::make($request->all(), [
                        'mice_offers' => 'mimes:jpeg,jpg,pdf|max:2048'
                    ]);
                    // Validation
                    if ($validatorOffers->fails()) {
                        // return response gagal
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => $validator->errors()->first(), 
                            'data' =>null,
                        ];
                        return response()->json($response, 200);
                    }
                }
            }

            if(empty($request->oldLayout)){
                //Get data layout lama 
                $layout = Halls::where('id', '=', $request->id)->first();
                if($layout->layout != null){
                    $file_path = public_path($layout['layout']); 
                    if(File::exists($file_path)) File::delete($file_path);
                }
                // upload File layout to folder
                $image = $request->file('layout');
                $fileName = time().'.'.$image->extension();  
                $loc = public_path('mice/layout');
                //save image to storage
                $loct = $image->move($loc, $fileName);
    
                // construct data param to model
                $data = [
                    'id' => $request['id'],
                    'name' => $request['name'],
                    'mice_category_id' => $request['mice_category_id'],
                    'description' => $request['description'],
                    'capacity' => $request['capacity'],
                    'size' => $request['size'],
                    'seq' => $request['seq'],
                    'updated_by' => $request->updated_by,
                    'layout' =>'mice/layout/'.$fileName
                ];
            } else {
                 // construct data param to model
                 $data = [
                    'id' => $request['id'],
                    'name' => $request['name'],
                    'mice_category_id' => $request['mice_category_id'],
                    'description' => $request['description'],
                    'capacity' => $request['capacity'],
                    'size' => $request['size'],
                    'seq' => $request['seq'],
                    'updated_by' => $request->updated_by,
                    'layout' => $request->oldLayout
                ];
            }
        
            // Validasi dan Construct data key mice_offers
            if(empty($request->oldMiceOffers)){
                if( (!empty($request->file('mice_offers'))) && ($request->file('mice_offers') != null)) {
                    // upload File layout to folder
                    $imageMiceOffers = $request->file('mice_offers');
                    $fileNameMiceOffers = time().'.'.$imageMiceOffers->extension();  
                    $locMice = public_path('mice/mice-offers');
                    //save image to storage
                    $loctMice = $imageMiceOffers->move($locMice, $fileNameMiceOffers);
        
                    // construct data param to model
                    $data['mice_offers'] = 'mice/mice-offers/'.$fileNameMiceOffers;
                }
            } elseif(!empty($request->oldMiceOffers)) {
                 // construct data param to model
                 $data['mice_offers'] = $request->oldMiceOffers;
            }

            // Insert into DB
            $insert = Halls::update_hall($data);
                if($insert){
                        $mice_cat_id = $request->mice_category_id;
                        $qtyMice = HallCategory::where('hall_id', '=', $request->id)
                                                 ->where('deleted_at', '=', null)
                                                 ->get();
                        if(count($qtyMice) < count($mice_cat_id)){
                            foreach($mice_cat_id as $cat_id){
                                // Cek Hall_Category_Id Exist
                                $cek = HallCategory::where('mice_category_id', '=', $cat_id)
                                                    ->where('hall_id', '=', $request->id)
                                                    ->where('deleted_at', '=', null)
                                                    ->get();
                                $requestHallCat = [
                                    'mice_category_id' => $cat_id,
                                    'hall_id' => $request->id,
                                    'created_by' => $request->updated_by,
                                    'updated_by' => $request->updated_by
                                ];
                                
                                if(count($cek) == 0){
                                    $inserHallCat = HallCategory::add_hall_category($requestHallCat);
                                }
                            }
                        } else {
                            // Delete dulu yang lama
                            foreach($qtyMice as $recBefore){
                                foreach($mice_cat_id as $cat_id){
                                    if($recBefore['mice_category_id'] != $cat_id){
                                        $inserHallCat = HallCategory::where('mice_category_id', '=', $recBefore['mice_category_id'])
                                                                    ->where('hall_id', '=', $request->id)
                                                                    ->delete();
                                    } 
                                }
                            }
                            // Insert Hall Category Baru
                            foreach($mice_cat_id as $cat_id){
                                $requestHallCat = [
                                    'mice_category_id' => $cat_id,
                                    'hall_id' => $request->id,
                                    'created_by' => $request->updated_by,
                                    'updated_by' => $request->updated_by
                                ];
                                $inserHallCat = HallCategory::add_hall_category($requestHallCat);
                                
                            }
                        }
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
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'edit_hall',
                'actions' => 'edit data hall dan hall mice category',
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
     * function : Delete data Hall
     * body: param[id]
	 *$request	: 
     */
    public function delete_hall(Request $request)
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
            $deletedRows = Halls::where('id', $request->id)->delete();
                if($deletedRows){
                    $deletedRowsHallCategory = HallCategory::where('hall_id', $request->id)->delete();
                    if($deletedRowsHallCategory){
                            $response = [
                                    'status' => true,
                                    'message' => __('message.data_deleted_success'),
                                    'code' => 200,
                                    'data' => null,
                                ];
                                return response()->json($response, 200);   
                    }
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
            $error = ['modul' => 'delete_hall',
                'actions' => 'delete data Hall and Hall Category',
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
	 * Function: get data Hotel dgn Mice Category
	 * body: param[id]
	 *	$request	: 
	*/
    
    public function get_hotel_with_mice_category(Request $request)
    {
        try {
            // Get Hotel dgn Mice Category utk User terkait Hotel
            if(!empty($request->user_id)){
                $dataHotel = Hotel::get_hotel_mice_with_hotel_user($request->user_id);
            } else {
                // Get Hotel dgn Mice Category utk Admin Dashboard
                $dataHotel = Hotel::get_hotel_mice();
            }
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_found' ),
                'data' => $dataHotel,
            ];
            return response()->json($response, 200);    

        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_hotel_with_mice_category',
                'actions' => 'get data hotel with mice category',
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
	 * Function: get data Hotel dgn Mice Category dan Msystem
	 * body: param[id]
	 *	$request	: 
	*/
    
    public function get_hotel_mice_msystem(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'hotel_id' => 'required',]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            // Get data Category name and id Join Mice Category,Hotel dan Msystem
            $data = MiceCategory::get_hotel_mice_msystem($request->hotel_id);
            if(count($data) > 0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $data
                ];
            } else {
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null
                ];
            }
            return response()->json($response, 200);    

        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_hotel_mice_msystem',
                'actions' => 'get data passing hotel id for mice name from msystem',
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
	 * Function: get data Hall berdasarkan hall_id
	 * body: 
	 *	$request	: string name
	 **/
    public function get_hall_detail(Request $request)
    {
        try {      
            $data = Halls::get_hall_detail($request->hall_id);
            $data_category = Halls::get_hall_category($request->hall_id);
            $data_images = HallImages::get_image($request->hall_id);
            $count_img = count($data_images);
            if ($count_img==0){
                $data_images = null;
            }
            $data = json_decode(json_encode($data), true);
          
            $data[0]["hall_images"] = json_decode($data_images);
            $data[0]["hall_category"] = json_decode($data_category);
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
                    'data' => $data[0],
                    
                ];
                return response()->json($response, 200);
        } 
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_hall_detail',
                'actions' => 'get data Hall detail',
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
	 * Function: Get data images Hall
	 * body: param[hall_id,name,filename,status,seq]
	 *	$request	: 
	*/
    
    public function get_hall_images(Request $request)
    {
        
        try{
            $validator = Validator::make($request->all(), [
                'hall_id' => 'required'
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
            $data_images = HallImages::get_image($request->hall_id);
            $response = [
                        'status' => false,
                        'message' => __('message.data_found'),
                        'code' => 200,
                        'data' => $data_images, 
                        ];
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'add_hall_images',
                'actions' => 'save data image Hall',
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
	 * Function: add data images Hall
	 * body: param[hall_id,name,filename,status,seq]
	 *	$request	: 
	*/
    
    public function add_hall_images(Request $request)
    {
        
        try{
            if(empty($request['oldImages'])){
                $validator = Validator::make($request->all(), [
                    'filename' => 'required|image|mimes:jpeg,jpg|max:2048',
                    'hall_id' => 'required',
                    'name' => 'required',
                    'status' => 'required',
                    'seq' => 'required',
                    'created_by' => 'required'
                ]);
            }else{
                $validator = Validator::make($request->all(), [
                    'id' => 'required',
                    'filename' => 'image|mimes:jpeg,jpg|max:2048',
                    'hall_id' => 'required',
                    'name' => 'required',
                    'status' => 'required',
                    'seq' => 'required',
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
            
            if(!empty($request['filename'])){
                // Check Image Resolution ke tabel M_System
                $dataResolution=[
                    'system_type' => 'img_resolution',
                    'system_cd' => 'hotel'
                ];
                $checkResolution = Msystem::get_system_type_cd($dataResolution);

                
                foreach ($checkResolution as $value) {
                $resDimension[] = $value['system_value'];
                
                }
                $data = getimagesize($request->filename);
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
                        $path = HallImages::where('id', $request->id)->first();
                        $file_path = public_path($path['filename']); 
                        //if file found delete image
                        if(File::exists($file_path)) File::delete($file_path);
                    }
                    
                    $images = $request->file('filename');
                    $fileName = time().'.'.$images->extension();  
                    $loc = public_path('mice/hall-images');
                    $loct = $images->move($loc, $fileName);
                    $data = [
                        'id' =>$request['id'],
                        'hall_id' => $request['hall_id'],
                        'name' => $request['name'],
                        'status' => $request['status'],
                        'seq' => $request['seq'],
                        'filename' =>'mice/hall-images/'.$fileName,
                        'created_by' => $request['created_by']
                    ];
            }
            else{
                $data = [
                    'id' =>$request['id'],
                    'hall_id' => $request['hall_id'],
                    'name' => $request['name'],
                    'seq' => $request['seq'],
                    'status' => $request['status'],
                    'filename' =>$request['oldImages'],
                    'updated_by' => $request['updated_by']
                ];
            }
            $cek_seq = HallImages::Where('hall_id',$request['hall_id'])
                                    ->Where('seq',$request['seq'])
                                    ->first();
                // dd($cek_seq);
                if($cek_seq==null){
                    $save= HallImages::update_hall_images($data);
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
            $save= HallImages::update_hall_images($data);
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
            $error = ['modul' => 'add_hall_images',
                'actions' => 'save data image Hall',
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
     * faunction : Delete data image Hall
     * body: param[id_image]
	 *$request	: 
     */
    public function delete_image_hall(Request $request)
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
            $path = HallImages::where('id', $request->id)->first();
            $file_path = public_path($path['filename']); 
            //if file found delete image from storage
            if(File::exists($file_path)) File::delete($file_path);
            $deletedRows = HallImages::where('id', $request->id)->delete();
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
            $error = ['modul' => 'delete_image_hall',
                'actions' => 'delete data image Hall',
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
     * faunction : Get Hall Data from hotel_id
     * body: 
	 *$request	: 
     */
    public function get_hotel_hall(Request $request)
    {
        
        try{
            $validator = Validator::make($request->all(), [
                'hotel_id' => 'required',
                'category_id' => 'required',
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
            $data = Halls::get_all_hotel_hall($request->all());
            foreach($data as $key => $datax){
                $dataImage = HallImages::get_image($datax['id']);
                $data[$key]['hall_images'] =  $dataImage;
            }
            if(count($data) > 0){
                $data = json_decode(json_encode($data), true);
                $response = [
                    'status' => true,
                    'message' => __('message.data_found' ),
                    'code' => 200,
                    'data' => $data,
                ];
            } else {
                $response = [
                    'status' => true,
                    'message' => __('message.data_not_found' ),
                    'code' => 200,
                    'data' => $data,
                ];
            }
            return response()->json($response, 200);   

        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_hotel_mice',
                'actions' => 'Get Hall Data from hotel_id',
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
     * faunction : Get Search Hall Data Capacity
     * body: 
	 *$request	: 
     */
    public function get_all_hall_capacity(Request $request)
    {
        
        try{
            $data = Halls::get_all_hall_capacity($request->all());
            if(count($data) > 0){
                $data = json_decode(json_encode($data), true);
                $response = [
                    'status' => true,
                    'message' => __('message.data_found' ),
                    'code' => 200,
                    'data' => $data,
                ];
            } else {
                $response = [
                    'status' => true,
                    'message' => __('message.data_not_found' ),
                    'code' => 200,
                    'data' => $data,
                ];
            }
            return response()->json($response, 200);   

        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_hotel_mice',
                'actions' => 'Get Hall Data from hotel_id',
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
     * faunction : Get Search Hall Data Capacity
     * body: 
	 *$request	: 
     */
    public function get_search_hall_capacity(Request $request)
    {
        try{
            // dd($request->all());
            $validator = Validator::make($request->all(), [
                'capacity' => 'required|numeric'
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
            $data = Halls::get_search_hall_capacity($request->all());
            if(count($data) > 0){
                $data = json_decode(json_encode($data), true);
                $response = [
                    'status' => true,
                    'message' => __('message.data_found' ),
                    'code' => 200,
                    'data' => $data,
                ];
            } else {
                $response = [
                    'status' => true,
                    'message' => __('message.data_not_found' ),
                    'code' => 200,
                    'data' => $data,
                ];
            }
            return response()->json($response, 200);   

        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_hotel_mice',
                'actions' => 'Get Hall Data from hotel_id',
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
     * function : Get Mice Category data per hotel Id
     * body: 
	 *$request	: 
     */
    public function get_category_hotel(Request $request)
    {
        
        try{
            $validator = Validator::make($request->all(), [
                'hotel_id' => 'required'
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
            $data = Halls::get_category_hotel($request->all());

            if(count($data) > 0){
                $data = json_decode(json_encode($data), true);
                $response = [
                    'status' => true,
                    'message' => __('message.data_found' ),
                    'code' => 200,
                    'data' => $data,
                ];
            } else {
                $response = [
                    'status' => true,
                    'message' => __('message.data_not_found' ),
                    'code' => 200,
                    'data' => $data,
                ];
            }
            return response()->json($response, 200);   

        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_category_hotel',
                'actions' => 'Get Data Category id Per Hotel',
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
