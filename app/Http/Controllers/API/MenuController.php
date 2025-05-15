<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use File;
use Illuminate\Http\Request;
use App\Models\OutletMenus;
use App\Models\Outlet;
use App\Models\MenuCategories;
use App\Models\MenuSideDish;
use App\Models\OutletImages;
use App\Models\Msystem;
use App\Models\Promo;
use App\Http\Controllers\LOG\ErorrLogController;

/*
|--------------------------------------------------------------------------
| Hotel API Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process hotel data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: Faberuary 01 2021
*/
class MenuController extends Controller
{

    public function __construct() {
        $this->LogController = new ErorrLogController;
    }

    public function get_menu_outlet(Request $request)
    { 
        try {  
            $validator = Validator::make($request->all(), [
                'outlet_id' => 'required',
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
            // get data detail outlet
            $data_outlet = Outlet::get_outlet_detail($request->outlet_id);
            //$error = ['modul' => 'get_menu_outlet',
            //    'actions' => 'get data_outlet',
            //    'error_log' => 'No Error',
            //    'device' => "0" ];
            //$report = $this->LogController->error_log($error);

            // dd($data_outlet);
            // get data image outlet
            $data_image_outlet = OutletImages::get_image_outlet($request->outlet_id);
            //$error = ['modul' => 'get_menu_outlet',
            //    'actions' => 'get data_image_outlet',
            //    'error_log' => 'No Error',
            //    'device' => "0" ];
            //$report = $this->LogController->error_log($error);

            // dd($data_image_outlet);                                
            // get all category 
            $data_category = MenuCategories::get_category_by_outlet_scan($request->outlet_id);
            //$error = ['modul' => 'get_menu_outlet',
            //    'actions' => 'get data_category',
            //    'error_log' => 'No Error',
            //    'device' => "0" ];
            //$report = $this->LogController->error_log($error);
            
            // dd($data_category);                                
            if (empty($data_outlet)){
                $data_outlet = null;
            }
            else if (empty($data_image_outlet)){
                $data_image_outlet = null;
            }
            // else if (count($data_category)==0){
            //         $data_category = null;
            // }
                if(!empty($data_category)){
                //    dd('ada');
                    $default_img_dining = Msystem::where('system_type','default_img_dining')
                                                            ->where('system_cd','img')
                                                            ->first();
                    //dd($request->outlet_id);
                    $data_menu_all = OutletMenus::get_menus_by_outlet($request->outlet_id);
                    $data_menu_array=$data_menu_all->toArray();

                    $data_sidedish = MenuSideDish::get_sidedish_menu_all();
                    $data_sidedish_array=$data_sidedish->toArray();

                    $promo = Promo::getpromo_menu($request->outlet_id);
                    
                    //dd(collect($data_filter));

                    foreach ($data_category as $category){
                        $condition =[
                            'id_outlet' => $request->outlet_id,
                            'id_category' => $category->menu_cat_id
                        ];
                        
                        $data_filter = array_filter($data_menu_array, function ($item) use ($category) {
                            return $item["menu_cat_id"] === $category->menu_cat_id;
                        });

                        $data_menu = collect($data_filter);//$data_menu_all;//OutletMenus::get_menus_category($condition);
                        //dd($data_menu);
                        if(count($data_menu)==0){
                            $data = null;
                        }
                        else{
                            $data = null;
                            foreach ($data_menu as $menu) {
                                //dd($menu['id']);
                                if($menu['images'] == null || $menu['images'] == ""){
                                    //$set_img = Msystem::where('system_type','default_img_dining')
                                    //                        ->where('system_cd','img')
                                    //                        ->first();
                                    $menu['images'] = $default_img_dining['system_img'];//"system-images/1628756056.jpg"; //$set_img['system_img'];
                                }

                                $data_sidedish_filter = array_filter($data_sidedish_array, function ($item) use ($menu) {
                                    return $item["fboutlet_mn_id"] === $menu['id'];
                                });

                                $sidedish = collect($data_sidedish_filter); //MenuSideDish::get_sidedish_menu($menu['id']);
                                $sidedish2 = null;
                                $count_sidedish = count($sidedish);
                                // dd($sidedish);
                                if ($count_sidedish==0){
                                    $sidedish2 = null;
                                }
                                else{
                                    foreach ($sidedish as $datax) {
                                        if($datax['is_promo_sdishs'] == 1){
                                            //$promo = Promo::getpromo_menu($request->outlet_id);
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
                                    //$promo = Promo::getpromo_menu($request->outlet_id);
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
            
            
            $error = ['modul' => 'get_menu_outlet',
                'actions' => 'Finishing Generate data Menu Outlet',
                'error_log' => 'No Error',
                'device' => "0" ];
            $report = $this->LogController->error_log($error);

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
            $error = ['modul' => 'get_menu_outlet',
                'actions' => 'get data menu outlet',
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
	 * Function: get data menu detail
	 * body: 
	 *	$request	: string name
	 **/
    public function get_menu_detail(Request $request)
    {
        try {      
            $data = OutletMenus::get_menu_detail($request->menu_id);
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
            $error = ['modul' => 'get_menu_detail',
                'actions' => 'get data menu detail',
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
	 * Function: get data menu categories all
	 * body: 
	 *	$request	: string name
	 **/
    public function get_categories_all(Request $request)
    {
        try {
            $data = MenuCategories::get_categories_all($request->all());
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
            $error = ['modul' => 'get_categories_all',
                'actions' => 'get data category menu',
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
	 * Function: get data menu categories all for User Outlet
	 * body: 
	 *	$request	: string name
	 **/
    public function get_categories_all_user(Request $request)
    {
        try { 
            $validator = Validator::make($request->all(), [
                'user_id' => 'required'
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
            
            $data = MenuCategories::get_categories_all_user($request->all());
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
            $error = ['modul' => 'get_categories_all',
                'actions' => 'get data category menu',
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
	 * Function: get data menu categories all untuk dapatkan sequence no terakhir,
     *  jadi Order By di Eloquent Model nya berdasarkan Seq No di Menu Category
	 * body: 
	 *	$request	: string name
	 **/
    public function get_categories_all_with_seq_no(Request $request)
    {
        try {
            $data = MenuCategories::get_categories_all_with_seq_no($request->all());
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
            $error = ['modul' => 'get_categories_all',
                'actions' => 'get data category menu',
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
	 * Function: get data menu categories all with Hotel ID
	 * body: 
	 *	$request	: string name
	 **/
    public function get_menu_categories_all_hotel(Request $request)
    {
        try {
            // dd($request);
            $data = MenuCategories::get_menu_categories_all_hotel($request->all());
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
            $error = ['modul' => 'get_categories_all',
                'actions' => 'get data category menu',
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
	 * Function: get data menu categories all for User Outlet with Hotel ID
	 * body: 
	 *	$request	: string name
	 **/
    public function get_menu_categories_all_user_hotel(Request $request)
    {
        try { 
            $validator = Validator::make($request->all(), [
                'user_id' => 'required'
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
            
            $data = MenuCategories::get_menu_categories_all_user_hotel($request->all());
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
            $error = ['modul' => 'get_categories_all',
                'actions' => 'get data category menu',
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
	 * Function: Save Menu (Add or Edit)
	 * body: 
	 *	$request	: string name
	 **/
    public function save_menu(Request $request)
    {
        try {    
            if(empty($request['oldImages'])){
                $validator = Validator::make($request->all(), [
                    'images' => 'image:jpeg,png,jpg,gif,svg|max:256',
                    'fboutlet_id' => 'required',
                    'name' => 'required|max:150|regex:/^[A-Za-z0-9-& ]+$/',
                    'description' => 'required|regex:/^[\r\nA-Za-z0-9-_!:;,.\'&# ]+$/',
                    'price' => 'required|numeric',
                    'menu_sts' => 'required',
                    'menu_cat_id' => 'required',
                    'seq_no' => 'required'
                ]);
            }else{
                $validator = Validator::make($request->all(), [
                    'fboutlet_id' => 'required',
                    'name' => 'required|max:150|regex:/^[A-Za-z0-9-& ]+$/',
                    'description' => 'required|regex:/^[\r\nA-Za-z0-9-_!:;,.\'&# ]+$/',
                    'price' => 'required|numeric',
                    'oldImages' => 'required',
                    'menu_sts' => 'required',
                    'menu_cat_id' => 'required',
                    'seq_no' => 'required'
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
                    $path = OutletMenus::where('id', $request->id)->first();
                    if($path != null) {
                    $file_path = app_path($path['filename']); 
                        //if file found delete image
                        if(File::exists($file_path)) File::delete($file_path);
                        $data = [
                            'changed_by' => $request['changed_by'],
                            'created_by' => null
                        ]; 
                    } else {
                        $response = [
                            'status' => false,
                            'message' => __('message.data_not_found'),
                            'code' => 200,
                            'data' => null, 
                            ];
                        return response()->json($response, 200);
                    }
                } else {
                    $data = [
                        'created_by' => $request['created_by'],
                        'changed_by' => $request['created_by']
                    ];
                }
                $images = $request->file('images');
                $fileName = time().'.'.$images->extension();  
                $loc = public_path('outlet-menu-images');
                $loct = $images->move($loc, $fileName);
                $data = $data + [
                    'id' => $request['id'],
                    'fboutlet_id' => $request['fboutlet_id'],
                    'name' => $request['name'],
                    'description' => $request['description'],
                    'price' => $request['price'],
                    'menu_sts' => $request['menu_sts'],
                    'menu_cat_id' => $request['menu_cat_id'],
                    'seq_no' => $request['seq_no'],
                    'is_promo' => $request['is_promo'],
                    'images' =>'outlet-menu-images/'.$fileName
                ];
            }
            else{
                if(!empty($request['id'])){
                    $data = [
                        'changed_by' => $request['changed_by'],
                        'created_by' => $request['created_by']
                    ];
                } else {
                    $data = [
                        'created_by' => $request['created_by'],
                        'changed_by' => null
                    ];
                }
                $data = $data + [
                    'id' => $request['id'],
                    'fboutlet_id' => $request['fboutlet_id'],
                    'name' => $request['name'],
                    'description' => $request['description'],
                    'price' => $request['price'],
                    'menu_sts' => $request['menu_sts'],
                    'menu_cat_id' => $request['menu_cat_id'],
                    'seq_no' => $request['seq_no'],
                    'is_promo' => $request['is_promo'],
                    'images' => $request['oldImages'],
                ];
                if(empty($request['images']) && empty($request['oldImages'])){
                    // $set_default = Msystem::where('system_type','default_img_dining')
                    //                         ->where('system_cd','img')
                    //                         ->first();
                    // $data['images'] = $set_default['system_img'];
                    $data['images'] = null;
                }
            }

            $insert= OutletMenus::save_menu($data);
                if($insert){
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
            $error = ['modul' => 'save_menu',
                'actions' => 'save data menu',
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
     * faunction : Delete data menu
     * body: param[id]
	 *$request	: 
     */
    public function delete_menu(Request $request)
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
            $path = OutletMenus::where('id', $request->id)->first();
            $file_path = app_path($path['images']); 
            //if file found delete image from storage
            if(File::exists($file_path)) File::delete($file_path);
            $deletedRows = OutletMenus::where('id', $request->id)->delete();
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
            $error = ['modul' => 'delete_menu',
                'actions' => 'delete data menu',
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
	 * Function: Get Sidedish Menu
	 * body: 
	 *	$request	: string name
    **/
    public function get_menu_sidedish(Request $request)
    {
        try {      
            $data = MenuSideDish::get_menu_sidedish($request);
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
            $error = ['modul' => 'get_menu_sidedish',
                'actions' => 'get data menu sidedish',
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
	 * Function: Get Sidedish Menu
	 * body: 
	 *	$request	: string name
    **/
    public function get_sidedish(Request $request)
    {
        try {      
            $data = MenuSideDish::get_sidedish($request);
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
            $error = ['modul' => 'get_sidedish',
                'actions' => 'get data menu sidedish',
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
	 * Function: Get Sidedish Menu
	 * body: 
	 *	$request	: string name
    **/
    public function get_sidedish_menu_cat(Request $request)
    {
        try {      
            $data = MenuSideDish::get_sidedish_menu_cat($request);
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
            $error = ['modul' => 'get_sidedish_menu_cat',
                'actions' => 'get data menu sidedish with menu cat id',
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
	 * Function: Add Sidedish Menu
	 * body: 
	 *	$request	: string name
    **/
    public function add_sidedish(Request $request)
    {
        try {      
            $data = MenuSideDish::add_sidedish($request);
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
            $error = ['modul' => 'add_sidedish',
                'actions' => 'save data menu sidedish',
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
	 * Function: Add Sidedish Menu
	 * body: 
	 *	$request	: string name
    **/
    public function delete_sidedish(Request $request)
    {
        try {      
            $data = MenuSideDish::delete_sidedish($request);
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
                    'message' => __('message.data_deleted_success' ),
                    'data' => $data,
                    
                ];
                return response()->json($response, 200);
        } 
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'delete_sidedish',
                'actions' => 'delete data menu sidedish',
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
	 * Function: get data menu detail
	 * body: 
	 *	$request	: string name
	 **/
    public function get_menu(Request $request)
    {
        try {      
            $data = OutletMenus::get_menu_detail_all($request->menu_id);
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
            $error = ['modul' => 'get_menu',
                'actions' => 'get data menu',
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
	 * Function: Add or Edit data menu categories
	 * body: 
	 *	$request	: string name, created_by, updated_by dan id jika ada utk update
	 **/
    public function save_menu_categories(Request $request)
    {
        try {      
            if(!empty($request->id)){
                $message =  __('message.data_update_success' );
                $validator = Validator::make($request->all(), [
                    'name' => 'required|max:150',
                    'seq_no' => 'required',
                    'fboutlet_id' => 'required',
                    'show_in_menu' => 'required',
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
            }
            else{
                $message = __('message.data_saved_success' );
                $validator = Validator::make($request->all(), [
                    'name' => 'required|max:150',
                    'seq_no' => 'required',
                    'fboutlet_id' => 'required',
                    'show_in_menu' => 'required',
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
            }
            if(!empty($request->id)){
            $cek_seq = MenuCategories::Where('seq_no', $request['seq_no'])
                                        ->where('fboutlet_id',$request['fboutlet_id'])
                                        ->where('deleted_at',null)
                                        ->where('id','!=',$request['id'])
                                        ->get();
            // if($cek_seq != 0){
            }
            else{
                $cek_seq = MenuCategories::Where('seq_no', $request['seq_no'])
                                        ->where('fboutlet_id',$request['fboutlet_id'])
                                        ->where('deleted_at',null)
                                        ->get();
            } 
                if(count($cek_seq)!=0){
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => __('message.seq_no_allready' ),
                            'data' => null,
                        ];
                        return response()->json($response, 200);
                }
            
            $data = MenuCategories::add_menu_categories($request);
            if($data == null){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.failed_save_data' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => $message,
                    'data' => $data,
                    
                ];
                return response()->json($response, 200);
        } 
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'save_menu_categories',
                'actions' => 'save data category menu',
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
	 * Function: Delete data menu categories (Soft Deletes)
	 * body: 
	 *	$request	: id
	 **/
    public function delete_menu_categories(Request $request)
    {
        // dd($request->all());
        try {      
            $check_menu = OutletMenus::where('menu_cat_id',$request['id'])
                                        ->get();
            // dd($check_menu);
            if(count($check_menu) != 0){
                $response = [
                    'status' => false,
                    'code' => 200,
                    'message' => __('message.data_deleted_failed_used_data' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            $data = MenuCategories::delete_menu_categories($request);
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
            $error = ['modul' => 'delete_menu_categories',
                'actions' => 'delete data category menu',
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
	 * Function: Get Menu is Side Dish
	 * body: 
	 *	$request	: id
	 **/
    public function get_menu_is_sidedish(Request $request)
    {
        // dd($request->all());
        try {      
            $check_menu = MenuSideDish::where('fboutlet_mn_sdish_id',$request['id'])
                                        ->where('deleted_at', '=', null)
                                        ->get();
            // dd($check_menu);
            if(count($check_menu) != 0){
                $response = [
                    'status' => false,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $check_menu,
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
        } 
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_menu_is_sidedish',
                'actions' => 'Get Data Menu is Side Dish',
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
     * Function: select outlet sequence number
	 * body: id_user, id_outlet
	 *	$request	: 
     */

    public function get_seq_no(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'id_outlet' => 'required',
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
            $check_seq_no = MenuCategories::where('fboutlet_id', $request['id_outlet'])
                                            ->where('deleted_at', null)
                                            ->orderBy('seq_no', 'DESC')
                                            ->get();
            // dd($check_seq_no);
            if(count($check_seq_no) !=0){
                $seqNo = (int)$check_seq_no[0]['seq_no'] + 1;
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => $seqNo,
                ];
             
            }else{
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
            }
            return $response;
            // return response()->json($response, 200);           
        } catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_seq_no',
                'actions' => 'get_seq_no',
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
