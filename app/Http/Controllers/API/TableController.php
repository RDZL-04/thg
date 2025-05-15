<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\OutletMenus;
use App\Models\Outlet;
use App\Models\MenuCategories;
use App\Models\MenuSideDish;
use App\Models\OutletImages;
use App\Models\Promo;
use App\Models\Msystem;
use App\Http\Controllers\LOG\ErorrLogController;
use Validator;

/*
|--------------------------------------------------------------------------
| Table API Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process promo data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: January 25 2021
*/

class TableController extends Controller
{
    public function __construct() {
        $this->LogController = new ErorrLogController;
    }
    /**
	 * Function: get data table
	 * body: 
	 *	$request	: 
	*/
    
    public function get_table_by_user(Request $request)
    {
        try {
            // dd($request->all());
                $get = Table::get_table_user($request);
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
            $error = ['modul' => 'get_table_by_user',
                'actions' => 'get data table by user',
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
	 * Function: get data table
	 * body: 
	 *	$request	: 
	*/
    
    public function get_table_all(Request $request)
    {
        try {
                $get = Table::get_table_all();
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
            $error = ['modul' => 'get_table_all',
                'actions' => 'get data table all',
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
	 * Function: get data table by hotel
	 * body: 
	 *	$request	: 
	*/
    
    public function get_table_by_hotel(Request $request)
    {
        try {
                $get = Table::get_table_by_hotel($request);
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
            $error = ['modul' => 'get_table_by_hotel',
                'actions' => 'get data table by hotel',
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
	 * Function: get data table by outlet
	 * body: 
	 *	$request	: 
	*/
    
    public function get_table_by_outlet(Request $request)
    {
        try {
                $get = Table::get_table_by_outlet($request);
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
            $error = ['modul' => 'get_table_by_outlet',
                'actions' => 'get data table by outlet',
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
	 * Function: get data table by outlet
	 * body: 
	 *	$request	: 
	*/
    
    public function get_table_by_id(Request $request)
    {
        try {
                $get = Table::get_table_by_id($request);
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
            $error = ['modul' => 'get_table_by_id',
                'actions' => 'get data table by id',
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

    public function add_table(Request $request)
    {
        // dd($request->all());
        try{
            $validator = Validator::make($request->all(), [
                'fboutlet_id' => 'required',
                'table_no' => 'required|max:10',
                'created_by' =>'required',
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
            $insert= table::add_table($request->all());
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
            $error = ['modul' => 'add_table',
                'actions' => 'add data table',
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

    public function edit_table(Request $request)
    {
        // dd($request->all());
        try{
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'fboutlet_id' => 'required',
                'table_no' => 'required|max:10',
                'updated_by' =>'required',
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
            $edit= Table::edit_table($request->all());
                if($edit){
                    $data = Table::Where('id',$request['id'])->get();
                    // dd($data);
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
            $error = ['modul' => 'edit_table',
                'actions' => 'edit data table',
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

    public function delete_table(Request $request)
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
            $set_flag = Table::where('id', $request->id)
                                ->update(['deleted_flag' => 1]);
            $deletedRows = Table::where('id', $request->id)->delete();
            
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
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'delete_table',
                'actions' => 'delete data table',
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
	 * Function: get data outlet menu berdasarkan barcode
	 * body: 
	 *	$request	: string name
	 **/
    public function scan_barcode(Request $request)
    {
        try {      
            $validator = Validator::make($request->all(), [
                'barcode' => 'required',
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
            $barcode = explode("-",$request['barcode']);
            if(count($barcode)!=2){
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => __('message.invalid_barcode'),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            $outlet = $barcode[0];
            $table = ['no_table' => $barcode[1]];
            
            //cek outlet
            $cek_outlet = Outlet::where('id', $outlet)
                                ->where('status',1)
                                ->first();
            // dd($cek_outlet);
            // cek table
            $cek_table = Table::where('fboutlet_id', $outlet)
                                ->where('table_no', $barcode[1])
                                ->first();
            if($cek_outlet == null || $cek_table == null){
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => __('message.invalid_barcode'),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            // dd($cek_table);
            // get data detail outlet
            $data_outlet = Outlet::get_outlet_detail($outlet);
            $data_outlet = json_decode(json_encode($data_outlet),true);
            $data_outlet = $data_outlet + ['no_table' => $barcode[1]];
            // dd($data_outlet);
            // get data image outlet
            $data_image_outlet = OutletImages::get_image_outlet($outlet);     
            // dd($data_image_outlet);                           
            // get all category 
            $data_category = MenuCategories::get_category_by_outlet_scan($outlet);
            // dd($data_category);    
            if (empty($data_outlet)){
                $data_outlet = null;
            }
            else if (empty($data_image_outlet)){
                $data_image_outlet = null;
            }
            else if (empty($data_category)){
                    $data_category = null;
            }
                if (count($data_category)!=0 && $data_category != null){
                
                    foreach ($data_category as $category){
                        $condition =[
                            'id_outlet' => $outlet,
                            'id_category' => $category->menu_cat_id
                        ];
                        $data_menu = OutletMenus::get_menus_category($condition);
                        // dd($data_menu);
                        if(count($data_menu)==0){
                        $data = null;
                        }
                        else{
                            $data = null;
                            foreach ($data_menu as $menu) {
                                if($menu['images'] == null || $menu['images'] == ""){
                                    $set_img = Msystem::where('system_type','default_img_dining')
                                                            ->where('system_cd','img')
                                                            ->first();
                                    $menu['images'] = $set_img['system_img'];
                                }
                                $sidedish = MenuSideDish::get_sidedish_menu($menu->id);
                                $sidedish2 = null;
                                $count_sidedish = count($sidedish);
                                // dd($sidedish);
                                if ($count_sidedish==0){
                                    $sidedish2 = null;
                                }
                                else{
                                    foreach ($sidedish as $datax) {
                                        // dd($datax);
                                        if($datax['is_promo_sdishs'] == 1){
                                            // dd($datax);
                                            $promo = Promo::getpromo_menu($data_outlet['id']);
                                            // dd($promo);
                                            if(empty($promo)){
                                                $harga_promo = [
                                                'promo_id' => null,
                                                'discount' =>null,
                                                'max_discount_price' => null,
                                                'promo_value' => null,
                                                'promo_price' => null];
                                                // echo 'ada';
                                                // $datax = json_decode(json_encode($datax), true); 
                                                // $datax = $datax + $harga_promo;
                                            }
                                            else{
                                                $value_promo = $datax['price']*$promo['value']/100;
                                                if($promo['max_discount_price']!= null){
                                                    if($value_promo > $promo['max_discount_price'])
                                                    {
                                                        $value_promo = $promo['max_discount_price'];
                                                    }
                                                }
                                                $harga_promo = $datax['price']-$value_promo;
                                                
                                                $harga_promo = number_format((float)$harga_promo, 2, '.', '');
                                                $harga_promo = ['promo_id' => $promo['id'],
                                                                'discount' => $value_promo,
                                                                'max_discount_price' => $promo['max_discount_price'],
                                                                'promo_value' => $promo['value'],
                                                                'promo_price' => $harga_promo];
                                            }
                                            // dd($harga_promo);
                                            
                                            $datax = json_decode(json_encode($datax), true); 
                                            $datax = $datax + $harga_promo;
                                            
                                        }
                                        else{
                                            $harga_promo = ['discount' => null,
                                                            'max_discount_price' => null,
                                                            'promo_value' => null,
                                                            'promo_price' => null];
                                            $datax = json_decode(json_encode($datax), true); 
                                            $datax = $datax + $harga_promo;
                                        }

                                        $sidedish2[]=$datax+$datax;
                                    }
                                    // dd($sidedish2);
                                }
                                if($menu['is_promo'] == 1){
                                    $promo = Promo::getpromo_menu($outlet);
                                    // dd($promo);
                                    if(empty($promo)){
                                        $harga_promo = [
                                        'promo_id' => null,
                                        'discount' =>null,
                                        'max_discount_price' => null,
                                        'promo_value' => null,
                                        'promo_price' => null];
                                    }
                                    else{
                                        $value_promo = $menu['price']*$promo['value']/100;
                                        if($promo['max_discount_price']!= null){
                                            if($value_promo > $promo['max_discount_price'])
                                            {
                                                $value_promo = $promo['max_discount_price'];
                                            }
                                        }
                                        $harga_promo = $menu['price']-$value_promo;
                                        $harga_promo = number_format((float)$harga_promo, 2, '.', '');
                                        $harga_promo = ['promo_id' => $promo['id'],
                                                        'discount' => $value_promo,
                                                        'max_discount_price' => $promo['max_discount_price'],
                                                        'promo_value' => $promo['value'],
                                                        'promo_price' => $harga_promo];
                                    }
                                    
                                    $menu = json_decode(json_encode($menu), true); 
                                    $menu = $menu + $harga_promo;
                                }else{
                                    $harga_promo = ['discount' => null,
                                                    'max_discount_price' => null,
                                                    'promo_value' => null,
                                                    'promo_price' => null];
                                    $menu = json_decode(json_encode($menu), true); 
                                    $menu = $menu + $harga_promo;
                                }
                                $sidedish = ['sidedish' => $sidedish2];
                                $menu = json_decode(json_encode($menu), true); 
                                $data[] = $menu+$sidedish;
                                // $data_menu = ['menu' => $data];
                            }
                        
                        }
                        // dd($data);
                        $data = json_decode(json_encode($data), true); 
                        $menu_category[] = ['category' => $category->name,
                                            'menu'=>$data];
                    }
                    
                }
                if(empty($menu_category)){
                    $menu_category = null; 
                }
            $data_outlet = json_decode(json_encode($data_outlet), true); 
            // dd();
            $data = [
                    'outlet' => $data_outlet,
                    'images_outlet' => $data_image_outlet,
                    'category_menu' => $menu_category];

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
            $error = ['modul' => 'scan_barcode',
                'actions' => 'get data table by barcode',
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
