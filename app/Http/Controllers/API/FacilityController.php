<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\HotelFacility;
use App\Models\Msystem;
use App\Http\Controllers\LOG\ErorrLogController;
use Validator;
use Illuminate\Support\Facades\Storage;
use File;

/*
|--------------------------------------------------------------------------
| Facility API Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process facility data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: December 21
*/

class FacilityController extends Controller
{
    public function __construct() {
        $this->LogController = new ErorrLogController;
    }
    /**
	 * Function: get data facility
	 * body: param[id_hotel]
	 *	$request	: 
	*/
    
    public function get_facility(Request $request)
    {
        try {
                $get = Facility::get_facility_Byhotel($request);
                if($get){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $get,
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
            $error = ['modul' => 'get_facility',
                'actions' => 'Get data facility hotel',
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
	 * Function: add data facility
	 * body: icon, name, seq_no
	 *	$request	: 
	*/
    public function add_facility(Request $request)
    {
        try{
            if(empty($request['id'])){
                $validator = Validator::make($request->all(), [
                    'icon' => 'required|image|mimes:png,svg|max:2048',
                    'name' => 'required|max:100',
                    'seq_no' => 'required',
                ]);
            }else{
                $validator = Validator::make($request->all(), [
                    'icon' => 'image|mimes:png,svg|max:2048',
                    'name' => 'required|max:100',
                    'seq_no' => 'required',
                    'id' => 'required',
                ]);
            }
            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(), 
                    'data' =>null,
                ];
                return response()->json($response, 200);
            }
			
			//update sequence first;
			$this->update_sequence($request['seq_no']);
			
                if(!empty($request['icon'])){
                    if(!empty($request['id'])){
                        $path = Facility::where('id', $request->id)->first();
                        $file_path = app_path($path['icon']); 
                        if(File::exists($file_path)) File::delete($file_path);
                    }
                    $icon = $request->file('icon');
                    $fileName = time().'.'.$icon->extension();  
                    $loc = public_path('icon');
                    $loct = $icon->move($loc, $fileName);
                    $data = [
                        'id' =>$request['id'],
                        'name' => $request['name'],
                        'seq_no' => $request['seq_no'],
                        'icon' =>'icon/'.$fileName
                    ];
                }
                else{
                    $data = [
                        'id' =>$request['id'],
                        'name' => $request['name'],
                        'seq_no' => $request['seq_no'],
                        'icon' =>$request['old_icon']
                    ];
                }
                if(empty($request['id'])){
                    $save= Facility::update_facility($data);
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
                else if(!empty($request['id'])){
                    $cek_seq = Facility::where('seq_no', $request->seq_no)->first();
                    // dd($cek_seq);
                    if($cek_seq==null){
                        $save= Facility::update_facility($data);
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
                        else if($cek_seq['id']==$request->id){
                            
                            $save= Facility::update_facility($data);
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
                        else{
                            
                            $response = [
                                'status' => false,
                                'message' => __('message.seq_no_allready'),
                                'code' => 200,
                                'data' => null, 
                                ];
                                return response()->json($response, 200);
                        
                    }
                }
    }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'add_facility',
                'actions' => 'add data facility hotel',
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
	 * Function:add data hotel facility by all facility
	 * body: id_hotel
	 *	$request	: 
	*/
    public function add_hotel_facility_all(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'hotel_id' => 'required',
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
            //get data facility
            $get_facility = Facility::get();
            $count = count($get_facility);
            if($count == 0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => ['total_page' => $get->lastPage(),
                               'current_page' => $get->currentPage(),
                               'data' => null],
                ];
                return response()->json($response, 200);
            }else{
                /*
                 * input data hotel to facility hotel
                 * loop until all facility end
                **/
                foreach ($get_facility as $facility) {
                    $data=['hotel_id' => $request['hotel_id'], 'facility_id' => $facility['id']];
                    $insert= HotelFacility::add_hotel_facility($data); 
                }
            $hotel_facility = HotelFacility::get_facility($request['hotel_id']);
            
                if($hotel_facility != null){
                          $response = [
                                'status' => true,
                                'message' => __('message.data_saved_success'),
                                'code' => 200,
                                'data' => $hotel_facility,
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
            $error = ['modul' => 'add_hotel_facility_all',
                'actions' => 'add data hotel facility all',
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
	 * Function: delete data all hotel facility
	 * body: id_hotel
	 *	$request	: 
	*/
    public function delete_hotel_facility_all(Request $request)
    {
        try{
            $messages = array(
                'hotel_id.required' => __('message.id_hotel_required'), 
            );
            $validator = Validator::make($request->all(), [
                'hotel_id' => 'required', 
            ],$messages);
            if ($validator->fails()) {
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            $hotel_facility = HotelFacility::delete_hotel_facility_all($request['hotel_id']);
            
                if($hotel_facility){
                          $response = [
                                'status' => true,
                                'message' => __('message.data_deleted_success'),
                                'code' => 200,
                                'data' => $hotel_facility,
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
            $error = ['modul' => 'delete_hotel_facility_all',
                'actions' => 'delete data hotel facility all',
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
     * faunction : Delete data facility hotel
     * body: param[id_facility]
	 *$request	: 
     */
    public function delete_facility(Request $request)
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
            $hotel_facility = HotelFacility::where('facility_id', $request->id)->first();
            // dd($hotel_facility);
            if($hotel_facility == null ){
                //get path image
                $path = Facility::where('id', $request->id)->first();
                $file_path = app_path($path['icon']); 
                //if file found delete image
                if(File::exists($file_path)) File::delete($file_path);
                $deletedRows = Facility::where('id', $request->id)->delete();
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
            $error = ['modul' => 'delete_facility',
                'actions' => 'delete data hotel facility',
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
	 * Function: add data hotel facility
	 * body: id_hotel, facility_id
	 *	$request	: 
	*/
    public function add_hotel_facility(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'hotel_id' => 'required',
                'facility_id' => 'required'
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
            // dd($request->all());
            $insert= HotelFacility::add_hotel_facility($request->all());
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
            $error = ['modul' => 'add_hotel_facility',
                'actions' => 'add data hotel facility',
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
	 * Function: delete data hotel facility
	 * body: id_hotel, facility_id
	 *	$request	: 
	*/
    public function delete_hotel_facility(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'hotel_id' => 'required',
                'facility_id' => 'required'
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
            $delete= HotelFacility::delete_hotel_facility($request->all());
                if($delete){
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
                            'data' => null, 
                            ];
                return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'delete_hotel_facility',
                'actions' => 'delete data hotel facility',
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
	 * @author: Arkra (arif@arkamaya.co.id) 20/04/2021 04:04pm
	*/
    private function update_sequence($start_seq)
    {
		if($start_seq)
		{
			$data = Facility::where('seq_no','>=',$start_seq)->orderBy('seq_no', 'ASC')->get();
			
			$i = $start_seq + 1;
			
			foreach($data as $d)
			{
				$update = array('seq_no' => $i);
				
				Facility::whereId($d->id)->update($update);
				
				$i++;
			}
		}
	}
}
