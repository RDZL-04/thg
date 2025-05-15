<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promo;
use App\Models\OutletMenus;
use Validator;
use App\Http\Controllers\LOG\ErorrLogController;

/*
|--------------------------------------------------------------------------
| Promo API Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process promo data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: January 22 2021
*/

class PromoController extends Controller
{
    /**
	 * Function: get data promo by id
	 * body: param[id_hotel]
	 *	$request	: 
	*/
    public function __construct() {
        $this->LogController = new ErorrLogController;
    }
    
    public function get_promo_id(Request $request)
    {
        try {
                $get = Promo::get_promo_id($request->id);
                if($get != null){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $get,
                ];
                return response()->json($response, 200);
            }
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => $get,
                ];
                return response()->json($response, 200);       
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_promo_id',
                'actions' => 'get data promo',
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
	 * Function: get data facility
	 * body: param[id_hotel]
	 *	$request	: 
	*/
    
    public function get_promo_all(Request $request)
    {
        try {
                $get = Promo::get_promo_all();
                if(count($get) != 0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $get,
                ];
                return response()->json($response, 200);
            }
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => $get,
                ];
                return response()->json($response, 200);       
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_promo_all',
                'actions' => 'get data promo all',
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
	 * Function: get data facility
	 * body: param[id_hotel]
	 *	$request	: 
	*/
    
    public function get_promo_outlet_with_user(Request $request)
    {
        try {
                $get = Promo::get_promo_outlet_with_user($request);
                if(count($get) != 0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $get,
                ];
                return response()->json($response, 200);
            }
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => $get,
                ];
                return response()->json($response, 200);       
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_promo_outlet_with_user',
                'actions' => 'get data promo outlet with user',
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
	 * Function: get data Promo With Hotel ID
	 * body: param[id_hotel]
	 *	$request	: 
	*/
    
    public function get_all_promo_with_hotel(Request $request)
    {
        try {
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
                $get = Promo::get_all_promo_with_hotel($request);
                if(count($get) != 0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $get,
                ];
                return response()->json($response, 200);
            }
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => $get,
                ];
                return response()->json($response, 200);       
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_promo_all',
                'actions' => 'get data promo all',
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
	 * Function: get data Promo With outlet ID
	 * body: param[id_hotel]
	 *	$request	: 
	*/
    
    public function get_all_promo_with_outlet(Request $request)
    {
        try {
                $validator = Validator::make($request->all(), [
                    'outlet_id' => 'required'
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
                $get = Promo::get_all_promo_with_outlet($request);
                if(count($get) != 0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $get,
                ];
                return response()->json($response, 200);
            }
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => $get,
                ];
                return response()->json($response, 200);       
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_promo_all',
                'actions' => 'get data promo all',
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

    public function add_promo(Request $request)
    {
        // dd($request->all());
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:25',
                'description' => 'required|max:50',
                'value' => 'required',
                'fboutlet_id' => 'required',
                // 'max_discount_price' => 'required',
                'valid_from' => 'required',
                'valid_to' => 'required',
                'created_by' =>'required',
                'chkPromoAll' => 'required'
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
            $check_promo = Promo::check_promo($request['fboutlet_id']);
            // dd($check_promo);
            if($check_promo != null){
                // dd($check_promo['valid_to']);
                if($check_promo['valid_to'] > $request['valid_from'])
                {
                    $tgl = date('Y-m-d', strtotime('-1 days', strtotime($request['valid_from'])));
                    // echo 'update';
                    // echo $tgl;
                   $updated = Promo::where('id', $check_promo['id'])
                    ->update(['valid_to' => $tgl]);
                }
            }

            $insert= Promo::add_promo($request->all());
                if($insert){
                          $response = [
                                'status' => true,
                                'message' => __('message.data_saved_success'),
                                'code' => 200,
                                'data' => $insert,
                            ];
                            // looping chkPromoAll di tabel menu sesuai fboutlets_id nya
                            $menuAll = OutletMenus::get_menus($request->fboutlet_id);
                            foreach ($menuAll as $menu) {
                                OutletMenus::where('id', $menu['id'])->update(array('is_promo' => $request->chkPromoAll));
                            }
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
            $error = ['modul' => 'add_promo',
                'actions' => 'add data promo outlet',
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

    public function edit_promo(Request $request)
    {
        // dd($request->all());
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'name' => 'required',
                'description' => 'required|max:50',
                'value' => 'required',
                'fboutlet_id' => 'required',
                // 'max_discount_price' => 'required',
                'valid_from' => 'required',
                'valid_to' => 'required',
                'updated_by' =>'required',
                'chkPromoAll' => 'required'
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
            $edit= Promo::edit_promo($request->all());
                if($edit){
                    $data = Promo::Where('id',$request['id'])->get();
                    // dd($data);
                     // looping chkPromoAll di tabel menu sesuai fboutlets_id nya
                     $menuAll = OutletMenus::get_menus($request->fboutlet_id);
                     foreach ($menuAll as $menu) {
                         OutletMenus::where('id', $menu['id'])->update(array('is_promo' => $request->chkPromoAll));
                     }
                          $response = [
                                'status' => true,
                                'message' => __('message.data_update_succcess'),
                                'code' => 200,
                                'data' => $data,
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
            $error = ['modul' => 'edit_promo',
                'actions' => 'edit data promo outlet',
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

    public function delete_promo(Request $request)
    {
        // dd($request->all());
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required',
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
            $set_flag = Promo::where('id', $request->id)
                                ->update(['deleted_flag' => 1]);
            if($set_flag){
                $deletedRows = Promo::where('id', $request->id)->delete();
            // $edit= Promo::edit_promo($request->all());
                if($deletedRows){
                    // $data = Promo::Where('id',$request['id'])->get();
                    // dd($data);
                          $response = [
                                'status' => true,
                                'message' => __('message.data_deleted_success'),
                                'code' => 200,
                                'data' => $deletedRows,
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
                    'message' => __('message.failed_save_data'),
                    'code' => 200,
                    'data' => null, 
                    ];
                return response()->json($response, 200);
            }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'delete_promo',
                'actions' => 'delete data promo outlet',
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
