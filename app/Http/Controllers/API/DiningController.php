<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderDining;
use App\Models\OrderDiningDetail;
use App\Models\OrderDiningDetailSdishs;
use App\Models\Members;
use App\Models\User;
use App\Models\OutletUsers;
use App\Models\Outlet;
use App\Models\OutletImages;
use App\Http\Controllers\LOG\ErorrLogController;
use App\Http\Controllers\API\FcmController;
use App\Models\Msystem;
use Validator;
use DNS2D;
use PDF;
use GuzzleHttp\Client;
/*
|--------------------------------------------------------------------------
| Dining Order API Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process order dining data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: January 29 2021
*/

class DiningController extends Controller
{
    public function __construct() {
        $this->LogController = new ErorrLogController;
        $this->FcmController = new FcmController;
    }


    /**
	 * Function: update data os type order dining
	 * body: 
	 *	$request	: 
	*/
    public function update_os_type_dining(Request $request){
        try{
            // dd($request->all()); 
            $validator = Validator::make($request->all(), [
                'transaction_no' => 'required',
                'os_type' => 'required'
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
            $update_order = OrderDining::update_os_type_dining($request->all());
            if($update_order){
                $data = OrderDining::where('transaction_no','=', $request['transaction_no'])
                                    ->first();
                $response = [
                    'status' => true,
                    'message' => __('message.data_saved_success'),
                    'code' => 200,
                    'data' =>$data,
                    ];
                return response()->json($response, 200);
            }
            else{
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => __('message.failed_save_data' ),
                    'data' => null,
                    ];
                return response()->json($response, 200);
            }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'update_status_transaction_dining',
                'actions' => 'update status order dining',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'code' => 400,
                'message' => __('message.data_not_found' ),
                'data' => null,
            ];
            return response()->json($response, 200);
        }
    }

    /**
	 * Function: update data order dining
	 * body: 
	 *	$request	: 
	*/
    public function update_status_dining(Request $request){
        try{
            // dd($request->all()); 
            $validator = Validator::make($request->all(), [
                'transaction_no' => 'required',
                'payment_progress_sts' => 'required'
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
            $update_order = OrderDining::update_status_dining($request->all());
            if($update_order){
                $data = OrderDining::where('transaction_no','=', $request['transaction_no'])
                                    ->first();
                $sendNotificationUser = $this->FcmController->send_notification_order_user($data);
                $response = [
                    'status' => true,
                    'message' => __('message.data_saved_success'),
                    'code' => 200,
                    'data' =>$data,
                    ];
                return response()->json($response, 200);
            }
            else{
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => __('message.failed_save_data' ),
                    'data' => null,
                    ];
                return response()->json($response, 200);
            }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'update_status_transaction_dining',
                'actions' => 'update status order dining',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'code' => 400,
                'message' => __('message.data_not_found' ),
                'data' => null,
            ];
            return response()->json($response, 200);
        }
    }


    /**
	 * Function: update data approve order and add or delete order detail
	 * body: 
	 * $request	: json
     * if the order will be deleted then fill in quantity 0
	*/
    public function approve_order(Request $request){
        try{
            $data = $request->getContent();
            $data = json_decode($data);
            $data_order = json_decode(json_encode($data->order), true);
            // dd($data_order['detail_order']);
            $rulesOrder = [
                'transaction_no' => 'required',
                'payment_progress_sts' => 'required|numeric',
                'approver_id' => 'required|numeric',
                'tax' => 'required',
                'sub_total_price' => 'required',
                'total_price' => 'required',
                'detail_order' => 'required',
                
            ];
            $rulesDetail = [
                'fb_menu_id' => 'required|max:10|',
                'price' => 'required|numeric',
                'quantity' => 'required|numeric',
                'amount' => 'required',
                'quantity' => 'required|numeric',
            ];
            $rulesDetailPromo = [
                'promo_id' => 'required',
                'price' => 'required|numeric',
                'discount' => 'required',
                'promo_value' =>'required',
                'max_discount_price' =>'required',
                'amount' => 'required',
                'quantity' => 'required|numeric',
            ];
            $validator = Validator::make($data_order, $rulesOrder);
            if($validator->fails()) 
            {
                    // return response gagal
                    $response = [
                        'status' => false,
                        'code' => 400,
                        'message' => $validator->errors()->first(), 
                        'data' =>null,
                        
                    ];
                    return response()->json($response, 200);
            }
            // dd($data_order);
            if(!empty($data_order['detail_order'])){
                foreach ($data_order['detail_order'] as $detail){
                    if(empty($detail['promo_id'])){
                        $validatorDetail = Validator::make($detail, $rulesDetail);
                        if($validatorDetail->fails()) {
                                // return response gagal
                                $response = [
                                    'status' => false,
                                    'code' => 400,
                                    'message' => $validatorDetail->errors()->first(), 
                                    'data' =>null,
                                    
                                ];
                                return response()->json($response, 200);
                        }
                    }
                    else{
                        $validatorDetail = Validator::make($detail, $rulesDetailPromo);
                        if($validatorDetail->fails()) {
                                // return response gagal
                                $response = [
                                    'status' => false,
                                    'code' => 400,
                                    'message' => $validatorDetail->errors()->first(), 
                                    'data' =>null,
                                    
                                ];
                                return response()->json($response, 200);
                        }
                    }
                }
                    // dd($data_order);
                    $update_order = OrderDining::update_approver_with_order($data_order);
                    if($update_order)
                    {
                        $get_id_transaction = OrderDining::where('transaction_no', $data_order['transaction_no'])
                                                    ->first();
                        $check_order = OrderDiningDetail::where('transaction_id', $get_id_transaction['id'])
                                                        ->get();
                    if(count($check_order) == 0){
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => __('message.failed_save_data' ),
                            'data' => null,
                        ];
                        return response()->json($response, 200);
                    }else{
                        $deletedRows = OrderDiningDetail::where('transaction_id', $get_id_transaction['id'])->forceDelete();
                    }
                    if($deletedRows == false){
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => __('message.failed_save_data' ),
                            'data' => null,
                        ];
                        return response()->json($response, 200);
                    }
                    
                    // dd($get_id_transaction['id']);
                        foreach ($data_order['detail_order'] as $order){
                            if($order['quantity'] == 0)
                            {
                                // dd('ada');
                                if(!empty($order['id'])){
                                    // dd('ada');
                                    $data_detail = $order+[
                                        "transaction_id" => $get_id_transaction['id']
                                        ];
                                    // dd($data_detail);
                                    $check_detail_order = OrderDiningDetail::where('id', $data_detail['id'])
                                                                            ->first();                                                                       
                                    if(!empty($check_detail_order)){
                                        $check_sdish = OrderDiningDetail::where('parent_id',$data_detail['id'])
                                                                            ->first();
                                        // dd($check_sdish);                                                                            
                                        if(!empty($check_sdish)){
                                            $delete_detail_order = OrderDiningDetail::where('parent_id',$data_detail['id'])
                                                                                    ->delete();
                                        }
                                        $delete_detail_order = OrderDiningDetail::where('id', $data_detail['id'])
                                                                                ->delete();
                                    }
                                }
                                else
                                {
                                    $response = [
                                        'status' => false,
                                        'code' => 400,
                                        'message' => __('message.failed_save_data' ),
                                        'data' => null,
                                    ];
                                    return response()->json($response, 200);
                                }
                                
                            }
                            else{
                                if(empty($order['promo_id']) || $order['promo_id'] == null)
                                {
                                    $data_detail = $order+[
                                                "transaction_id" => $get_id_transaction['id']
                                                ];
                                    $save_detail = OrderDiningDetail::add_order_detail($data_detail);
                                    if($save_detail){
                                        $id_parent= $save_detail['id'];
                                        // print_r($order['order_sdishs']);
                                        if($order['order_sdishs'] != null){
                                            // print_r($order['order_sdishs']);
                                            foreach($order['order_sdishs'] as $sdish){
                                                $data_sdish = $sdish + [
                                                    "parent_id" => $id_parent,
                                                    "transaction_id" => $save_detail['transaction_id']];
                                                $save_detail = OrderDiningDetail::add_order_sdish($data_sdish);
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    $data_detail = $order+[
                                                "transaction_id" => $get_id_transaction['id']
                                                ];
                                    // print_r($data_detail);
                                    // dd();
                                    $save_detail = OrderDiningDetail::add_order_detail_promo($data_detail);
                                    if($save_detail){
                                        $id_parent= $save_detail['id'];
                                        // print_r($order['order_sdishs']);
                                        if($order['order_sdishs'] != null){
                                            // print_r($order['order_sdishs']);
                                            foreach($order['order_sdishs'] as $sdish){
                                                $data_sdish = $sdish + [
                                                    "parent_id" => $id_parent,
                                                    "transaction_id" => $save_detail['transaction_id']];
                                                $save_detail = OrderDiningDetail::add_order_sdish($data_sdish);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $dining = OrderDining::get_order_id($data_order['transaction_no']);
                        $approver = User::get_user_id($dining['approver_id']);
                        // dd($approver[0]['full_name']);
                        $notif = [
                            'fboutlet_id' => $dining['fboutlet_id'],
                            'approver_id' => $dining['approver_id'],
                            'approver_name' => $approver[0]['full_name'],
                            'transaction_no' => $dining['transaction_no'],
                            'data' => $dining
                        ];
                        $sendNotification = $this->FcmController->send_notification_approve_order($notif);
                        $sendNotificationUser = $this->FcmController->send_notification_order_user($dining);
                        if($dining['payment_progress_sts'] == 0)
                        {
                            $message =  __('message.order-waiting' );
                        }
                        elseif($dining['payment_progress_sts'] == 1)
                        {
                            $message =  __('message.order-approve' );
                        }
                        elseif($dining['payment_progress_sts'] == 2)
                        {
                            $message = __('message.order-reject' );
                        }
                        elseif($dining['payment_progress_sts'] == 3)
                        {
                            $message = __('message.order-paid-success' );
                        }
                        elseif($dining['payment_progress_sts'] == 4)
                        {
                            $message = __('message.order-paid-failed' );
                        }
                        elseif($dining['payment_progress_sts'] == 6)
                        {
                            $message = __('message.order-done' );
                        }
                        else
                        {
                            $message = __('message.order-cancel' );
                        }
                        $response = [
                            'status' => true,
                            'code' => 200,
                            'message' => $message,
                            'data' => $dining
                        ];
                        return response()->json($response, 200);
                    }
                    $response = [
                        'status' => true,
                        'code' => 200,
                        'message' => __('message.failed_save_data' ),
                        'data' => null,
                    ];
                    return response()->json($response, 200);
                }
                else
                {
                    $update_order = OrderDining::update_approver($data_order);
                    if($update_order){
                        $dining = OrderDining::get_order_id($data_order['transaction_no']);
                        // dd($dining['payment_progress_sts']);
                        // dd($dining);
                        $approver = User::get_user_id($dining['approver_id']);
                            $notif = [
                                'fboutlet_id' => $dining['fboutlet_id'],
                                'approver_id' => $dining['approver_id'],
                                'approver_name' => $approver[0]['full_name'],
                                'transaction_no' => $dining['transaction_no']
                            ];
                        $sendNotification = $this->FcmController->send_notification_approve_order($notif);
                        $sendNotificationUser = $this->FcmController->send_notification_order_user($dining);
                        if($dining['payment_progress_sts'] == 0)
                        {
                            $message =  __('message.order-waiting' );
                        }
                        elseif($dining['payment_progress_sts'] == 1)
                        {
                            $message =  __('message.order-approve' );
                        }
                        elseif($dining['payment_progress_sts'] == 2)
                        {
                            $message = __('message.order-reject' );
                        }
                        elseif($dining['payment_progress_sts'] == 3)
                        {
                            $message = __('message.order-paid-success' );
                        }
                        elseif($dining['payment_progress_sts'] == 4)
                        {
                            $message = __('message.order-paid-failed' );
                        }
                        elseif($dining['payment_progress_sts'] == 6)
                        {
                            $message = __('message.order-done');
                        }
                        else
                        {
                            $message = __('message.order-cancel' );
                        }
                        $response = [
                            'status' => true,
                            'code' => 200,
                            'message' => $message,
                            'data' => $dining
                        ];
                        return response()->json($response, 200);
                    }
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.failed_save_data' ),
                    'data' => null,
                    ];
                return response()->json($response, 200);
                }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'approve_order',
                'actions' => 'approve order dining',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_not_found' ),
                'data' => null,
            ];
            return response()->json($response, 200);
        }
    }

    
    /**
	 * Function: get data order dining on progress
	 * body: 
	 *	$request	: 
	*/
    public function update_payment_dining(Request $request){
        try{
            // dd($request->all()); 
            $validator = Validator::make($request->all(), [
                'transaction_no' => 'required',
                'currency' => 'required',
                // 'payment_source' => 'required'
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
            $update_order = OrderDining::update_payment_source($request->all());
            if($update_order){
                $data = OrderDining::where('transaction_no','=', $request['transaction_no'])
                                    ->first();
                $response = [
                    'status' => true,
                    'message' => __('message.data_saved_success'),
                    'code' => 200,
                    'data' =>$data,
                    ];
                return response()->json($response, 200);
            }
            else{
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.failed_save_data' ),
                    'data' => null,
                    ];
                return response()->json($response, 200);
            }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'update_payment_dining',
                'actions' => 'update payment source order dining',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_not_found' ),
                'data' => null,
            ];
            return response()->json($response, 200);
        }
    }


    /**
	 * Function: get data order dining on progress
	 * body: 
	 *	$request	: 
	*/
    public function get_order(Request $request){
        try{
            
            $validator = Validator::make($request->all(), [
                // 'payment_progress_sts' => 'required',
                'fboutlet_id' =>'required',
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
            if(!empty($request['status'])){
                // dd($request['status']);
                if($request['status'] != "null"){
                if(strtoupper($request['status']) != 'NEW'){
                    $validator_id = Validator::make($request->all(), [
                        // 'payment_progress_sts' => 'required',
                        'fboutlet_id' =>'required',
                        'approver_id' =>'required',
                    ]);
                    if ($validator_id->fails()) {
                        // return response gagal
                        $response = [
                            'status' => false,
                            'code' => 400 ,
                            'message' => $validator_id->errors()->first(),
                        ];
                        return response()->json($response, 200);
                    }
                }                
            }
            }
            if(!empty($request['dtFrom']))
            {
                $validator = Validator::make($request->all(), [
                    'fboutlet_id' =>'required',
                    'dtTo' => 'required',
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
            if(empty($request['status'])){
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => 'Status can`t empty',
                ];
                return response()->json($response, 200);
            }
            if(!empty($request['approver_id'])){
            //    dd($request['approver_id']);
               $checkOutletUser = OutletUsers::where('user_id', $request['approver_id'])
                                            ->where('fboutlet_id', $request['fboutlet_id'])
                                            ->first();
                if($checkOutletUser == null){
                    $response = [
                        'status' => false,
                        'code' => 403 ,
                        'message' => 'is Denied',
                        'data' => null
                    ];
                    return response()->json($response, 200);
                }
            }       
            if(!empty($request['dtFrom']))
            {
                // $get_review = Review::get_review_by_date($request->all());
                $data = OrderDining::get_order_fb_by_date($request->all());
                if($request['payment_progress_sts'] == 6){
                    $url = "payment_progress_sts=".$request['payment_progress_sts']."&pg_payment_status=".$request['pg_payment_status']."&fboutlet_id=".$request['fboutlet_id']."&dtFrom=".$request['dtFrom']."&dtTo=".$request['dtTo'];
                }
            }
            else
            {
                
                $data = OrderDining::get_order_fb($request->all());
                if($request['payment_progress_sts'] == 6){
                    $url = "payment_progress_sts=".$request['payment_progress_sts']."&pg_payment_status=".$request['pg_payment_status']."&fboutlet_id=".$request['fboutlet_id'];
                }
                
            }
            // dd($data['total_data']);
            $pages = ceil($data['total_data']/10); 
            // for ($i=1; $i<=$pages ; $i++){ 
            //     $i;
            // } 
            // dd($i);
            // $total_page = $i;
            if(empty($request['page'])){
                $request['page'] = 1;
            }
            
            if($data['data'] != null){
                // dd($data['data']);
                foreach($data['data'] as $dining){
                    // dd($dining);
                    $filter_outlet = ['transaction_id' => $dining->id,
                                        'fboutlet_id' => $request['fboutlet_id']];
                    $order_detail = OrderDiningDetail::get_order_detail_outlet($filter_outlet);
                    $outlet= Outlet::Where('id',$dining->fboutlet_id)->first();
                    // dd($outlet);
                    if(count($order_detail) != 0){
                        $data_detail = null;
                        foreach ($order_detail as $detail){
                            $order_sdishs = OrderDiningDetail::get_order_shdishs($detail['id']);
                            if($order_sdishs == null){
                                $sdishs = ['order_sdishs' => null]; 
                            }else{
                                $sdishs = ['order_sdishs' => $order_sdishs]; 
                            }
                            $detail = json_decode(json_encode($detail), true); 
                            $data_detail[] =  $detail + $sdishs;       
                        }
                        $dining = json_decode(json_encode($dining), true);
                        $dining['name_outlet'] = $outlet['name'];
                        $dining['outlet_service'] = $outlet['service'];
                        $dining['outlet_tax'] = $outlet['tax'];
                        $data_order[] = $dining+['order_detail' => $data_detail]; 
                        
                    }else{
                        $dining = json_decode(json_encode($dining), true);
                        $dining['name_outlet'] = $outlet['name'];
                        $dining['outlet_service'] = $outlet['service'];
                        $dining['outlet_tax'] = $outlet['tax'];
                        $data_order[] = $dining+['order_detail' => null];  
                    }
                }
                $order_new=[];
                $order_pick_up=[];
                $order_on_progress=[];
                $order_done=[];
                $order_paid=[];
                $order_failed=[];
                // dd($data_order);
                foreach($data_order as $order){
                    if($order['payment_progress_sts'] == 0 && $order['approver_id'] == null){
                        array_push($order_new,$order);
                    }
                    else if($order['payment_progress_sts'] == 0 && $order['approver_id'] != null){
                        array_push($order_pick_up,$order);
                        // dd('masuk');
                    }
                    else if($order['payment_progress_sts'] == 1 && $order['approver_id'] != null){
                    //    dd($order);
                        array_push($order_on_progress,$order);
                    }
                    else if($order['payment_progress_sts'] == 3 && $order['approver_id'] != null){
                        array_push($order_paid,$order);
                    }
                    else if($order['payment_progress_sts'] == 6 && $order['approver_id'] != null){
                        array_push($order_done,$order);
                    }
                    else{
                        array_push($order_failed,$order);
                    }
                }
                // dd($order_on_progress);
                if( count($order_new) == 0){
                    $order_new =null;
                }
                if( count($order_pick_up) == 0){
                    $order_pick_up =null;
                }
                if( count($order_on_progress) == 0){
                    $order_on_progress =null;
                }
                if( count($order_paid) == 0){
                    $order_paid =null;
                }
                if( count($order_done) == 0){
                    $order_done =null;
                }
                if( count($order_failed) == 0){
                    $order_failed =null;
                }
                if (!empty($request['status'])){
                    if(strtoupper($request['status']) == 'NEW'){
                        $data_response = [
                            'order_new' => $order_new,
                            'order_pick_up' => null,
                            'order_on_progress' => null,
                            'order_done' => null,
                            'order_failed' => null,
                            'order_paid' => null
                        ];
                    }elseif(strtoupper($request['status']) == 'ON_PROGRESS'){
                        $data_response = [
                            'order_new' => null,
                            'order_pick_up' => null,
                            'order_on_progress' => $order_on_progress,
                            'order_done' => null,
                            'order_failed' => null,
                            'order_paid' => null
                        ];
                    }elseif(strtoupper($request['status']) == 'ORDER_PICKUP'){
                        $data_response = [
                            'order_new' => null,
                            'order_pick_up' => $order_pick_up,
                            'order_on_progress' => null,
                            'order_done' => null,
                            'order_failed' => null,
                            'order_paid' => null
                        ];
                    }elseif(strtoupper($request['status']) == 'DONE'){
                        $data_response = [
                            'order_new' => null,
                            'order_pick_up' => null,
                            'order_on_progress' => null,
                            'order_done' => $order_done,
                            'order_failed' => null,
                            'order_paid' => null
                        ];
                    }elseif(strtoupper($request['status']) == 'PAID'){
                        $data_response = [
                            'order_new' => null,
                            'order_pick_up' => null,
                            'order_on_progress' => null,
                            'order_done' => null,
                            'order_failed' => null,
                            'order_paid' => $order_paid
                        ];
                    }elseif(strtoupper($request['status']) == 'FAILED'){
                        $data_response = [
                            'order_new' => null,
                            'order_pick_up' => null,
                            'order_on_progress' => null,
                            'order_done' => null,
                            'order_failed' => $order_failed,
                            'order_paid' => null
                        ];
                    }
                    else{
                        $data_response = [
                            'order_new' => $order_new,
                            'order_pick_up' => $order_pick_up,
                            'order_on_progress' => $order_on_progress,
                            'order_done' => $order_done,
                            'order_failed' => $order_failed,
                            'order_paid' => $order_paid
                        ];
                    }
                }else{
                    $data_response = [
                        'order_new' => $order_new,
                        'order_pick_up' => $order_pick_up,
                        'order_on_progress' => $order_on_progress,
                        'order_done' => $order_done,
                        'order_failed' => $order_failed,
                        'order_paid' => $order_paid
                    ];
                }
                // dd($data_response);
                
                        // encrypt($url);
                if($request['payment_progress_sts']==6){
                    $response = [
                        'status' => true,
                        'code' => 200,
                        'message' => __('message.data_found' ),
                        'data' => ['total_page' => round($pages),
                                    'current_page' => $request['page'],
                                    'url' => url('/report_fnb?param=').encrypt($url),
                                    'data'=> $data_response
                                ]
                        
                    ];
                    return response()->json($response, 200);
                }
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => ['total_page' => round($pages),
                                    'total_data' => $data['total_data'],
                                    'current_page' => $request['page'],
                                    'url' => null,
                                    'data'=> $data_response
                                ]
                ];
                return response()->json($response, 200);
            }
            else{
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
        
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_order',
                'actions' => 'get data order dining',
                'error_log' => $e,
                'device' => "0" ];
            $report = $this->LogController->error_log($error);
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_not_found' ),
                'data' => null,
            ];
            return response()->json($response, 200);
        }
    }

    /**
	 * Function: add approver data order dining
	 * body: no_transaction, id_approver
	 *	$request	: 
	*/
    public function add_approver(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'transaction_no' => 'required',
                'approver_id' =>'required',
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
            $check_approver = OrderDining::Where('transaction_no',$request['transaction_no'])->first();
            if($check_approver['approver_id'] != null){
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => __('the order has been pick up by another waiter' ),
                    'data' =>['transaction_no' => $request['transaction_no'],
                              'approver_id' => $check_approver['approver_id']]
                ];
                return response()->json($response, 200);
            }else{
                $check_outlet_user = OutletUsers::Where('user_id', $request['approver_id'])
                                                ->where('fboutlet_id', $check_approver['fboutlet_id'])->first();
                if($check_outlet_user){
                    // dd($check_approver['fboutlet_id']);
                    // dd($check_outlet_user);
                    $save_approver = OrderDining::add_approver($request->all());
                    if($save_approver){
                        $sendNotificationUser = $this->FcmController->send_notification_order_user($check_approver);
                        $response = [
                            'status' => true,
                            'code' => 200,
                            'message' => __('The approver succes to add'),
                            'data' =>['transaction_no' => $request['transaction_no'],
                                      'approver_id' => $request['approver_id']]
                        ];
                        return response()->json($response, 200);
                    }
                    else{
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => __('The approver failed to add'),
                            'data' =>null
                        ];
                        return response()->json($response, 200);
                    }
                    
                }
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => __('you are not a user outlet'),
                    'data' =>null
                ];
                return response()->json($response, 200);
                
            }
            // dd($check_approver);

        }catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_order',
                'actions' => 'get data order dining',
                'error_log' => $e,
                'device' => "0" ];
            $report = $this->LogController->error_log($error);
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_not_found' ),
                'data' => null,
            ];
            return response()->json($response, 200);
        }
    }

    /**
	 * Function: get data order dining done
	 * body: 
	 *	$request	: 
	*/
    public function get_order_done(Request $request){
        try{
            $data = OrderDining::get_order_done();
            if(count($data) != 0){
                foreach($data as $dining){
                    $order_detail = OrderDiningDetail::get_order_detail($dining['id']);
                    if(count($order_detail) != 0){
                        $data_detail = null;
                        foreach ($order_detail as $detail){
                            $order_sdishs = OrderDiningDetail::get_order_shdishs($detail['id']);
                            if($order_sdishs == null){
                                $sdishs = ['order_sdishs' => null]; 
                            }else{
                                $sdishs = ['order_sdishs' => $order_sdishs]; 
                            }
                            $detail = json_decode(json_encode($detail), true); 
                            $data_detail[] =  $detail + $sdishs;       
                        }
                        $dining = json_decode(json_encode($dining), true);
                        $data_order[] = $dining+['order_detail' => $data_detail]; 
                        
                    }else{
                        $dining = json_decode(json_encode($dining), true);
                        $data_order[] = $dining+['order_detail' => null];  
                    }
                }
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $data_order,
                ];
                return response()->json($response, 200);
            }
            else{
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
        
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_order_done',
                'actions' => 'get data order dining done',
                'error_log' => $e,
                'device' => "0" ];
            $report = $this->LogController->error_log($error);
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_not_found' ),
                'data' => null,
            ];
            return response()->json($response, 200);
        }
    }
    /**
	 * Function: get data order dining failed
	 * body: 
	 *	$request	: 
	*/
    public function get_order_failed(Request $request){
        try{
            $data = OrderDining::get_order_failed();
            if(count($data) != 0){
                foreach($data as $dining){
                    $order_detail = OrderDiningDetail::get_order_detail($dining['id']);
                    if(count($order_detail) != 0){
                        $data_detail = null;
                        foreach ($order_detail as $detail){
                            $order_sdishs = OrderDiningDetail::get_order_shdishs($detail['id']);
                            if($order_sdishs == null){
                                $sdishs = ['order_sdishs' => null]; 
                            }else{
                                $sdishs = ['order_sdishs' => $order_sdishs]; 
                            }
                            $detail = json_decode(json_encode($detail), true); 
                            $data_detail[] =  $detail + $sdishs;       
                        }
                        $dining = json_decode(json_encode($dining), true);
                        $data_order[] = $dining+['order_detail' => $data_detail]; 
                        
                    }else{
                        $dining = json_decode(json_encode($dining), true);
                        $data_order[] = $dining+['order_detail' => null];  
                    }
                }
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $data_order,
                ];
                return response()->json($response, 200);
            }
            else{
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
        
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_order_failed',
                'actions' => 'get data order failed',
                'error_log' => $e,
                'device' => "0" ];
            $report = $this->LogController->error_log($error);
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_not_found' ),
                'data' => null,
            ];
            return response()->json($response, 200);
        }
    }

    /**
	 * Function: get data order dining callback
	 * body: 
	 *	$request	: 
	*/
    public function get_order_dining_callback(Request $request){
        try{
                $validator = Validator::make($request->all(), [
                    'transaction_no' => 'required',
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
                // $dining = OrderDining::get_order($request['transaction_no']);
                $dining = OrderDining::get_order($request['transaction_no']);                
                if($dining != null){
                    $url =['url_pdf' => url('/payment_fnb?transaction_no='.$dining['transaction_no'])];
                    // dd($url);
                    $dining = json_decode(json_encode($dining), true); 
                    $dining = $dining + $url;
                    // dd($dining);
                    $order_detail = OrderDiningDetail::get_order_detail($dining['id']);
                    if(count($order_detail) != 0){
                        foreach ($order_detail as $detail){
                            $order_sdishs = OrderDiningDetail::get_order_shdishs($detail['id']);
                            $order_sdishs = ['order_sdishs' => $order_sdishs]; 
                            $detail = json_decode(json_encode($detail), true); 
                            $data_detail[] = $detail + $order_sdishs;         
                        }
                        $data = ['order' => $dining,
                                'order_detail' => $data_detail];
                        $response = [
                            'status' => true,
                            'code' => 200,
                            'message' => __('message.data_found' ),
                            'data' => $data,
                        ];
                        return response()->json($response, 200);
                    }
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
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_order_dining',
                'actions' => 'get data order dining',
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
	 * Function: get data order dining by transaction no
	 * body: 
	 *	$request	: 
	*/
    public function get_order_dining(Request $request){
        try{
                $validator = Validator::make($request->all(), [
                    'transaction_no' => 'required',
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
                // $dining = OrderDining::get_order($request['transaction_no']);
                $dining = OrderDining::get_order($request['transaction_no']);
                if($dining['pg_payment_status'] === null || ($dining['pg_payment_status'] === 'paid' && $dining['pg_transaction_status'] === 'captured')){
                    $dining = OrderDining::get_order($request['transaction_no']);
                }
                else{
                    $configURL = Msystem::where('system_type', 'url')->where('system_cd', 'mpg_inquiries')->first();
                    $url_mpg = $configURL['system_value'];
                    $dataOutlet = Outlet::where('id',$dining['fboutlet_id'])->first();
                    $mpg_apiKey = $dataOutlet['mpg_api_key'];
                    
                    if($dining['mpg_id'] != null){
                        $payment_status_mpg = $this->payment_status_mpg($url_mpg,$mpg_apiKey,$dining['mpg_id']);
                        $transaction_status_mpg = $this->transaction_status_mpg($url_mpg,$mpg_apiKey,$dining['mpg_id']);
                        
                        // dd($transaction_mpg_code_status);
                        if($transaction_status_mpg != null){
                            $transaction_status = $transaction_status_mpg['status'];
                            $transaction_mpg_code_status = $transaction_status_mpg['mpg_status_code'];
                            if($transaction_status === 'authenticate' && $transaction_mpg_code_status === '-1'){
                              $transaction_status_mpg = 'failed';
                            }else{
                                $transaction_status_mpg = $transaction_status;
                            }
                        }
                        if($dining['pg_payment_status'] != $payment_status_mpg || $transaction_status_mpg != $dining['pg_transaction_status']){
                            if($payment_status_mpg === 'paid'){
                                $progress_status = 3;
                              }else if($payment_status_mpg === 'failed'){
                                $progress_status = 4;
                              }
                              else{
                                if($transaction_status_mpg === 'failed' && $transaction_status_mpg === 'declined'){
                                  $progress_status = 4;
                                }else{
                                  $progress_status = $dining['payment_progress_sts'];
                                }
                                
                              }
                              
                              $data = ['transaction_no' => $dining['transaction_no'],
                                      'payment_progress_sts' => $progress_status,
                                      'pg_payment_status' => $payment_status_mpg,
                                      'pg_transaction_status' => $transaction_status_mpg];
                            $update_statusdining = OrderDining::update_status_payment_dining($data);
                            $dining = OrderDining::get_order($request['transaction_no']);
                        }
                    }
                    $dining = OrderDining::get_order($request['transaction_no']);
                    
                }
                
                if($dining != null){
                    $url =['url_pdf' => url('/payment_fnb?transaction_no='.$dining['transaction_no'])];
                    // dd($url);
                    $dining = json_decode(json_encode($dining), true); 
                    $dining = $dining + $url;
                    // dd($dining);
                    $order_detail = OrderDiningDetail::get_order_detail($dining['id']);
                    if(count($order_detail) != 0){
                        foreach ($order_detail as $detail){
                            $order_sdishs = OrderDiningDetail::get_order_shdishs($detail['id']);
                            $order_sdishs = ['order_sdishs' => $order_sdishs]; 
                            $detail = json_decode(json_encode($detail), true); 
                            $data_detail[] = $detail + $order_sdishs;         
                        }
                        $data = ['order' => $dining,
                                'order_detail' => $data_detail];
                        $response = [
                            'status' => true,
                            'code' => 200,
                            'message' => __('message.data_found' ),
                            'data' => $data,
                        ];
                        return response()->json($response, 200);
                    }
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
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_order_dining',
                'actions' => 'get data order dining',
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
	 * Function: Get Transaction Status MPG
	 * @param  URL MPG, API key outlet, inquiry no
	 */
	private function transaction_status_mpg($url_mpg,$mpg_apiKey,$inquiry_no)
	{
    $client = new Client();
    $pgInquiryURL = $url_mpg . '/' . $inquiry_no . '/transactions';
    
      $headers = array(
        "authorization: ".$mpg_apiKey,
        "cache-control: no-cache",
        "content-type: application/json"
      );
      $response = $client->request('GET', $pgInquiryURL,[
        'verify' => false,
        'headers'       => $headers,
        // '/delay/5',
        //   ['connect_timeout' => 3.14]
        ]);
        $code = $response->getStatusCode();
        $body = $response->getBody();
        
        if($code == 200){
          $response = json_decode($body, true);
          if(!empty($response)){
            foreach($response as $data)
            {
                $transaction_status_mpg = ['status' => $data['status'],
                                            'mpg_status_code' => $data['statusCode']];
            }
          }else{
            $transaction_status_mpg = null;
            }

        }
        else{
        $transaction_status_mpg = null;
        }
			return $transaction_status_mpg;
		
	}

    /*
	 * Function: Get Status payment mpg
	 * @param  url mpg, apikey outlet, inquiry no
	 */
	private function payment_status_mpg($url_mpg,$mpg_apiKey,$inquiry_no)
	{
    $client = new Client();
    $pgInquiryURL = $url_mpg . '/' . $inquiry_no;
    
      $headers = array(
        "authorization: ".$mpg_apiKey,
        "cache-control: no-cache",
        "content-type: application/json"
      );
      $response = $client->request('GET', $pgInquiryURL,[
        'verify' => false,
        'headers'       => $headers,
        // '/delay/5',
        //   ['connect_timeout' => 3.14]
        ]);
        $code = $response->getStatusCode();
        $body = $response->getBody();
        
        if($code == 200){
          $response = json_decode($body, true);
        //   dd($response);
          $payment_status_mpg = $response['status'];
        }
        else{
        $payment_status_mpg = null;
        }
			return $payment_status_mpg;
		
	}

    /**
	 * Function: save data order dining
	 * body: json order,order detail
	 *	$request	: 
	*/
    public function order_dining(Request $request)
    {
        try { 
            
            $data = $request->getContent();
            // return $data;
            $data = json_decode($data);
            $order = json_decode(json_encode($data->order), true);
            // $order
            // dd($order);
            
            $rulesOrder = [
                'is_member' => 'required',
                'customer_name' => 'required',
                'detail_order' => 'required',
                'total_price' => 'required|numeric',
                'sub_total_price' => 'required|numeric',
                'tax' => 'required|numeric',
                'fboutlet_id' => 'required|numeric',
                'table_no' => 'required|numeric',
                'os_type' => 'required',
            ];
            $rulesOrderMember = [
                'is_member' => 'required',
                'customer_id' => 'required|numeric',
                'detail_order' => 'required',
                'total_price' => 'required|numeric',
                'sub_total_price' => 'required|numeric',
                'tax' => 'required|numeric',
                'fboutlet_id' => 'required|numeric',
                'table_no' => 'required|numeric',
                'os_type' => 'required',
            ];
            $rulesDetail = [
                'fb_menu_id' => 'required|max:10|',
                'price' => 'required|numeric',
                'amount' => 'required|numeric',
                'quantity' => 'required|numeric',
                'note' => 'max:250',
            ];
            $rulesDetailPromo = [
                'promo_id' => 'required',
                'price' => 'required|numeric',
                'amount' => 'required|numeric',
                'discount' => 'required',
                'promo_value' =>'required',
                'max_discount_price' =>'required',
                'quantity' => 'required|numeric',
                'note' => 'max:250',
            ];
           
            $rules = [
                'is_member' => 'required'
            ];
            $isMember = false;
            $validator = Validator::make($order, $rules);
            if($validator->fails()) {
                    // return response gagal
                    $response = [
                        'status' => false,
                        'code' => 400,
                        'message' => $validator->errors()->first(), 
                        'data' =>null,
                        
                    ];
                    return response()->json($response, 200);
                }
                if($order['is_member']==true){
                    $isMember = true;
                    $validatorOrder = Validator::make($order, $rulesOrderMember);
                    if($validatorOrder->fails()) {
                            // return response gagal
                            $response = [
                                'status' => false,
                                'code' => 400,
                                'message' => $validatorOrder->errors()->first(), 
                                'data' =>null,
                                
                            ];
                            return response()->json($response, 200);
                    }
                }
                else if($order['is_member']==false){
                    $isMember = false;
                    $validatorOrder = Validator::make($order, $rulesOrder);
                    if($validatorOrder->fails()) {
                            // return response gagal
                            $response = [
                                'status' => false,
                                'code' => 400,
                                'message' => $validatorOrder->errors()->first(), 
                                'data' =>null,
                                
                            ];
                            return response()->json($response, 200);
                    }
                }
                
            // return($order['detail_order'][0]['promo_id']);
                if(!empty($order['detail_order'])){
                    foreach ($order['detail_order'] as $detail){
                        // dd($detail);
                        // echo $detail['promo_id'];
                        if($order['is_member']==true){
                            
                            if(empty($detail['promo_id']) || $detail['promo_id'] == null){
                            
                                $validatorDetail = Validator::make($detail, $rulesDetail);
                                if($validatorDetail->fails()) {
                                        // return response gagal
                                        $response = [
                                            'status' => false,
                                            'code' => 400,
                                            'message' => $validatorDetail->errors()->first(), 
                                            'data' =>null,
                                            
                                        ];
                                        return response()->json($response, 200);
                                }
                            }else{
                                $validatorDetail = Validator::make($detail, $rulesDetailPromo);
                                if($validatorDetail->fails()) {
                                        // return response gagal
                                        $response = [
                                            'status' => false,
                                            'code' => 400,
                                            'message' => $validatorDetail->errors()->first(), 
                                            'data' =>null,
                                            
                                        ];
                                        return response()->json($response, 200);
                                }
                            }
                            
                        }
                        else{
                            
                            $validatorDetail = Validator::make($detail, $rulesDetail);
                            if($validatorDetail->fails()) {
                                    // return response gagal
                                    $response = [
                                        'status' => false,
                                        'code' => 400,
                                        'message' => $validatorDetail->errors()->first(), 
                                        'data' =>null,
                                        
                                    ];
                                    return response()->json($response, 200);
                            }
                        }
                    }
                }
                $detail_order = $order['detail_order'];
                // dd($detail_order);
            if($order['is_member']==true){
                $get_member = Members::where('id', $order['customer_id'])
                                        ->first();
                if(empty($get_member))
                {
                    $response = [
                        'status' => true,
                        'message' => __('message.not_members'),
                        'code' => 200,
                        'data' => null, 
                    ];
                    return response()->json($response, 500);
                }
                // print_r($get_member['fullname']);
                    $day = date('d');
                    $cekSeq = OrderDining::latest('id')->first();
                    // dd($cekSeq);
                    if($cekSeq == null)
                    {
                        $transaction_no = "THG/".date('Ymd')."/D-1";
                        $confirm_code = date('Ymd')."1";
                    }
                    else
                    {
                    $PecahCreated_at= explode(" ", $cekSeq['created_at']);
                    $last_data= explode("-", $PecahCreated_at[0]);
                    // dd($last_data[2]);
                    // $last_created=$PecahCreated_at[0]." ".$PecahCreated_at[1]." ".$PecahCreated_at[2];
                    // $last_data = explode(",",$PecahCreated_at[0]);
                        if ($last_data[2] != $day)
                        {
                            $transaction_no = "THG/".date('Ymd')."/D-1";
                            $confirm_code = date('Ymd')."1";
                        }
                        else
                        {
                            $PecahTransaction= explode("D-", $cekSeq['transaction_no']);
                            // dd($PecahTransaction);
                            $seq_no = $PecahTransaction[1]+1;
                            // dd($PecahTransaction);
                            $transaction_no = "THG/".date('Ymd')."/D-".$seq_no;
                            $confirm_code = date('Ymd').$seq_no;
                           
                        }
                    }    
                    // dd($confirm_code);
                    $data_order = $order+[
                                        "customer_name" => $get_member['fullname'],
                                        "transaction_no" => $transaction_no,
                                        "confirmation_code" => $confirm_code,
                                        ];
                   
            }
            else
            {
                $day = date('d');
                 $cekSeq = OrderDining::latest('id')->first();
                //  dd($cekSeq);
                    if($cekSeq == null){
                        $transaction_no = "THG/".date('Ymd')."/D-1";
                        $confirm_code = date('Ymd')."1";
                    }
                    else
                    {
                    $PecahCreated_at= explode(" ", $cekSeq['created_at']);
                    $last_data= explode("-", $PecahCreated_at[0]);
                    // dd($last_data[2]);
                    // $last_created=$PecahCreated_at[0]." ".$PecahCreated_at[1]." ".$PecahCreated_at[2];
                    // $last_data = explode(",",$PecahCreated_at[0]);
                        if ($last_data[2] != $day)
                        {
                            $transaction_no = "THG/".date('Ymd')."/D-1";
                            $confirm_code = date('Ymd')."1";
                        }
                        else
                        {
                            $PecahTransaction= explode("D-", $cekSeq['transaction_no']);
                            // dd($PecahTransaction);
                            $seq_no = $PecahTransaction[1]+1;
                            // dd($PecahTransaction);
                            $transaction_no = "THG/".date('Ymd')."/D-".$seq_no;
                            $confirm_code = date('Ymd').$seq_no;
                           
                        }
                    }
                    // dd($transaction_no);
                    $data_order = $order+[
                                        "customer_id" => null,
                                        "transaction_no" => $transaction_no,
                                        "confirmation_code" => $confirm_code,
                                        ];
                    
            }
            $save_order = OrderDining::add_order($data_order);
            if($save_order){
                foreach ($detail_order as $order){
                    $id_parent= null;
                    if($isMember == true){
                        if(empty($order['promo_id']) || $order['promo_id'] == null ){
                            $data_detail = $order+[
                                        "transaction_id" => $save_order['id']
                                        ];
                                        // print_r($data_detail);
                            $save_detail = OrderDiningDetail::add_order_detail($data_detail);
                            
                            if($save_detail){
                                $id_parent= $save_detail['id'];
                                // print_r($order['order_sdishs']);
                                if($order['order_sdishs'] != null){
                                    // print_r($order['order_sdishs']);
                                    foreach($order['order_sdishs'] as $sdish){
                                        $data_sdish = $sdish + [
                                            "parent_id" => $id_parent,
                                            "transaction_id" => $save_detail['transaction_id']];
                                        $save_detail = OrderDiningDetail::add_order_sdish($data_sdish);
                                    }
                                }
                            }
                        }
                        else{
                            $data_detail = $order+[
                                        "transaction_id" => $save_order['id']
                                        ];
                            // print_r($data_detail);
                            // dd();
                            $save_detail = OrderDiningDetail::add_order_detail_promo($data_detail);
                            if($save_detail){
                                $id_parent= $save_detail['id'];
                                // print_r($order['order_sdishs']);
                                if($order['order_sdishs'] != null){
                                    // print_r($order['order_sdishs']);
                                    foreach($order['order_sdishs'] as $sdish){
                                        $data_sdish = $sdish + [
                                            "parent_id" => $id_parent,
                                            "transaction_id" => $save_detail['transaction_id']];
                                        $save_detail = OrderDiningDetail::add_order_sdish($data_sdish);
                                    }
                                }
                            }
                        }
                    }else{
                        $data_detail = $order+[
                            "transaction_id" => $save_order['id']
                            ];
                            // print_r($data_detail);
                            $save_detail = OrderDiningDetail::add_order_detail($data_detail);
                            
                            if($save_detail){
                                $id_parent= $save_detail['id'];
                                // print_r($order['order_sdishs']);
                                if($order['order_sdishs'] != null){
                                    // print_r($order['order_sdishs']);
                                    foreach($order['order_sdishs'] as $sdish){
                                        $data_sdish = $sdish + [
                                            "parent_id" => $id_parent,
                                            "transaction_id" => $save_detail['transaction_id']];
                                        $save_detail = OrderDiningDetail::add_order_sdish($data_sdish);
                                    }
                                }
                            }
                    }
                }
                $payment_source = Msystem::get_payment_source();
                $dining = json_decode(json_encode($save_order), true); 
                $order_detail = OrderDiningDetail::get_order_detail($dining['id']);
                                
                    if(count($order_detail) != 0){
                        $data_detail=[];
                        foreach ($order_detail as $detail){
                            $data_order = null;
                            $order_sdishs = OrderDiningDetail::get_order_shdishs($detail['id']);
                            $order_sdishs = ['order_sdishs' => $order_sdishs]; 
                            $detail = json_decode(json_encode($detail), true); 
                            $data_order =  $detail + $order_sdishs; 
                            array_push($data_detail, $data_order);    
                        }
                        $data = [
                                'order' => $save_order,
                                'order_detail' => $data_detail,
                                'payment_source' => $payment_source];
                    }
                    else{
                        $data = [
                                'order' => $save_order,
                                'order_detail' => null,
                                'payment_source' => $payment_source];
                    }
                // dd($data);
                if($save_order['approver_id']==null){
                    $sendNotification = $this->FcmController->send_notification_order($data);
                    
                }
                $dataNotif = OrderDining::where('transaction_no' , $data['order']['transaction_no'])->first();
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_saved_success' ),
                    'data' => $data
                ];
                return response()->json($response, 200);
            }
            else{
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.failed_save_data' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'order_dining',
                'actions' => 'save data order dining',
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
	 * Function: save new data order dining failed
	 * body: transaction no
	 *	$request	: 
	*/
    public function replace_order_dining_failed(Request $request)
    {
        try { 
            
            $data = $request->all();
            // dd('asa');
            // return response()->json($data, 200);
            $rules = [
                'order' => 'required',
                'order_detail' => 'required'
            ];
            
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                    // return response gagal
                    $response = [
                        'status' => false,
                        'code' => 400,
                        'message' => $validator->errors()->first(), 
                        'data' =>null,
                        
                    ];
                    return response()->json($response, 200);
                }
            
                $day = date('d');
                // return response()->json($data, 200);
                    $cekSeq = OrderDining::latest('id')->first();
                    // dd($cekSeq);
                    // return response()->json($cekSeq, 200);
                    if($cekSeq == null)
                    {
                        $transaction_no = "THG/".date('Ymd')."/D-1";
                        $confirm_code = date('Ymd')."1";
                    }
                    else
                    {
                    $PecahCreated_at= explode(" ", $cekSeq['created_at']);
                    // $last_created=$PecahCreated_at[0]." ".$PecahCreated_at[1]." ".$PecahCreated_at[2];
                    $last_data = explode("-",$PecahCreated_at[0]);
                        if ($last_data[2] != $day)
                        {
                            $transaction_no = "THG/".date('Ymd')."/D-1";
                            $confirm_code = date('Ymd')."1";
                        }
                        else
                        {
                            $PecahTransaction= explode("D-", $cekSeq['transaction_no']);
                            // dd($PecahTransaction);
                            $seq_no = $PecahTransaction[1]+1;
                            // dd($PecahTransaction);
                            $transaction_no = "THG/".date('Ymd')."/D-".$seq_no;
                            $confirm_code = date('Ymd').$seq_no;
                           
                        }
                    }    
                    // return response()->json($transaction_no, 200);
                // $data['order'];
                // dd($data['order']);
                
                $data['order']['new_transaction_no'] = $transaction_no;
                $data['order']['new_confirmation_code'] = $confirm_code;
                // return $data;
                
                $save_order = OrderDIning:: new_order($data['order']);
                if($save_order){
                    
                    foreach ($data['order_detail'] as $order){
                        $id_parent= null;
                        
                        if(empty($order['promo_id'])){
                            
                            $data_detail = $order+[
                                        "new_transaction_id" => $save_order['id']
                                        ];
                                        // print_r($data_detail);
                                        // return response()->json($data_detail, 200);
                            $save_detail = OrderDiningDetail::add_order_detail_failed($data_detail);
                            // return response()->json($save_detail, 200);
                            
                            if($save_detail){
                                $id_parent= $save_detail['id'];
                                
                                if(!empty($order['order_sdishs'])){
                                    //
                                    
                                    foreach($order['order_sdishs'] as $sdish){
                                        $data_sdish = $sdish + [
                                            "parent_id" => $id_parent,
                                            "new_transaction_id" => $save_detail['transaction_id']];
                                            // return response()->json($data_sdish, 200);
                                        $save_detail = OrderDiningDetail::add_order_sdish_failed($data_sdish);
                                        // return response()->json($save_detail, 200);
                                    }
                                }
                            }
                        }
                        else{
                            
                            $data_detail = $order+[
                                        "new_transaction_id" => $save_order['id']
                                        ];
                            // return response()->json($data_detail, 200);
                            // print_r($data_detail);
                            // dd();
                            
                            $save_detail = OrderDiningDetail::add_order_detail_promo_failed($data_detail);
                            // return response()->json($save_detail, 200);
                            if($save_detail){
                                $id_parent= $save_detail['id'];
                                // print_r($order['order_sdishs']);
                                
                                if(!empty($order['order_sdishs'])){
                                    // print_r($order['order_sdishs']);
                                    foreach($order['order_sdishs'] as $sdish){
                                        $data_sdish = $sdish + [
                                            "parent_id" => $id_parent,
                                            "new_transaction_id" => $save_detail['transaction_id']];
                                        $save_detail = OrderDiningDetail::add_order_sdish_failed($data_sdish);
                                    }
                                }
                            }
                        }
                    }
                $payment_source = Msystem::get_payment_source();
                $dining = json_decode(json_encode($save_order), true); 
                $order_detail = OrderDiningDetail::get_order_detail($dining['id']);
                $data = OrderDining::where('id',$save_order['id'])->first();
                    if(count($order_detail) != 0){
                        $data_detail=[];
                        foreach ($order_detail as $detail){
                            $data_order = null;
                            $order_sdishs = OrderDiningDetail::get_order_shdishs($detail['id']);
                            $order_sdishs = ['order_sdishs' => $order_sdishs]; 
                            $detail = json_decode(json_encode($detail), true); 
                            $data_order =  $detail + $order_sdishs; 
                            array_push($data_detail, $data_order);    
                        }
                        $data = [
                                'order' => $data,
                                'order_detail' => $data_detail,
                                'payment_source' => $payment_source];
                    }
                    else{
                        $data = [
                                'order' => $data,
                                'order_detail' => null,
                                'payment_source' => $payment_source];
                    }
                // dd($data);
                $dataNotif = OrderDining::where('transaction_no' , $data['order']['transaction_no'])->first();
                $sendNotificationUser = $this->FcmController->send_notification_order_user($dataNotif);
                // $sendNotificationUser = $this->FcmController->send_notification_order_user($data);
                
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_saved_success' ),
                    'data' => $data
                ];
                return response()->json($response, 200);
            }
            $response = [
                'status' => false,
                'code' => 400,
                'message' => 'erorr', 
                'data' =>null,
                
            ];
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'order_dining',
                'actions' => 'replace data order dining',
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
	 * Function: get data order dining by id_member
	 * body: param customer_id, payment_progress_sts
	 *	$request	: 
	*/
    public function get_order_dining_member(Request $request){
        try
        {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required',

                ]);
            if ($validator->fails()) 
            {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            if(!empty($request['dtFrom']))
            {
                $validator = Validator::make($request->all(), [
                    'customer_id' => 'required',
                    'dtTo' => 'required',
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
            
            if(!empty($request['payment_progress_sts'])){
                if(!empty($request['dtFrom']))
                {
                    $data = OrderDining::get_order_customer_date($request->all());
                    
                }else{
                    $data = OrderDining::get_order_customer_sts($request->all());
                }
            }else{
                if(!empty($request['dtFrom']))
                {
                    $data = OrderDining::get_order_customer_all_date($request->all());
                    // dd($data);
                }else{
                    $data = OrderDining::get_order_customer_all($request->all());
                }
            }
            // dd($data);
                
                if(count($data) != 0){
                    foreach($data as $dining){
                        $order_detail = OrderDiningDetail::get_order_detail($dining['id']);
                        $image_outlet = OutletImages::where('fboutlet_id', $dining['fboutlet_id'])->first();
                        if($image_outlet != null){
                            // $image_outlet = $image_outlet['filename'];
                            $dining['outlet_image'] = $image_outlet['filename'];
                        }else{
                            $dining['outlet_image'] = null;
                            // $image_outlet = null;
                        }
                        if(count($order_detail) != 0){
                            $data_detail = null;
                            foreach ($order_detail as $detail){
                                $order_sdishs = OrderDiningDetail::get_order_shdishs($detail['id']);
                                // dd($order_sdishs);
                                if($order_sdishs == null){
                                    $sdishs = ['order_sdishs' => null]; 
                                }else{
                                    $sdishs = ['order_sdishs' => $order_sdishs]; 
                                }
                                $detail = json_decode(json_encode($detail), true); 
                                $data_detail[] =  $detail + $sdishs;       
                            }
                            $dining = json_decode(json_encode($dining), true);
                            $data_order[] = $dining+['order_detail' => $data_detail]; 
                            
                        }else{
                            $dining = json_decode(json_encode($dining), true);
                            $data_order[] = $dining+['order_detail' => null];  
                        }
                        
                    }
                    $response = [
                        'status' => true,
                        'code' => 200,
                        'message' => __('message.data_found' ),
                        'data' => $data_order,
                    ];
                    return response()->json($response, 200);
                }
                else{
                    $response = [
                        'status' => true,
                        'code' => 200,
                        'message' => __('message.data_not_found' ),
                        'data' => null,
                    ];
                    return response()->json($response, 200);
                }
            
        }
        catch (Throwable $e) 
        {        
            report($e);
            $error = ['modul' => 'get_order_dining_member',
                'actions' => 'get data order dining member',
                'error_log' => $e,
                'device' => "0" ];
            $report = $this->LogController->error_log($error);
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_not_found' ),
                'data' => null,
            ];
            return response()->json($response, 200);
        }
    }

    /**
	 * Function: get data order dining by id_member
	 * body: 
	 *	$request	: 
	*/
    public function get_order_dining_non_member(Request $request){
        try
        {
            $validator = Validator::make($request->all(), [
                'uniqueid' => 'required',
                ]);
            if ($validator->fails()) 
            {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
                $data = OrderDining::get_order_non_memeber($request->all());
                if(count($data) != 0){
                    foreach($data as $dining){
                        $order_detail = OrderDiningDetail::get_order_detail($dining['id']);
                        if(count($order_detail) != 0){
                            $data_detail = null;
                            foreach ($order_detail as $detail){
                                $order_sdishs = OrderDiningDetail::get_order_shdishs($detail['id']);
                                if($order_sdishs == null){
                                    $sdishs = ['order_sdishs' => null]; 
                                }else{
                                    $sdishs = ['order_sdishs' => $order_sdishs]; 
                                }
                                $detail = json_decode(json_encode($detail), true); 
                                $data_detail[] =  $detail + $sdishs;       
                            }
                            $dining = json_decode(json_encode($dining), true);
                            $data_order[] = $dining+['order_detail' => $data_detail]; 
                            
                        }else{
                            $dining = json_decode(json_encode($dining), true);
                            $data_order[] = $dining+['order_detail' => null];  
                        }
                    }
                    $response = [
                        'status' => true,
                        'code' => 200,
                        'message' => __('message.data_found' ),
                        'data' => $data_order,
                    ];
                    return response()->json($response, 200);
                }
                else{
                    $response = [
                        'status' => true,
                        'code' => 200,
                        'message' => __('message.data_not_found' ),
                        'data' => null,
                    ];
                    return response()->json($response, 200);
                }
            
        }
        catch (Throwable $e) 
        {        
            report($e);
            $error = ['modul' => 'get_order_dining_non_member',
                'actions' => 'get data order dining non member',
                'error_log' => $e,
                'device' => "0" ];
            $report = $this->LogController->error_log($error);
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_not_found' ),
                'data' => null,
            ];
            return response()->json($response, 200);
        }
    }

    /**
	 * Function: get data order dining by mac address
	 * body: param customer_id, payment_progress_sts
	 *	$request	: 
	*/
    public function get_order_dining_by_device_id(Request $request){
        try
        {
            $validator = Validator::make($request->all(), [
                'device_id' => 'required',

                ]);
            if ($validator->fails()) 
            {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            if(!empty($request['dtFrom']))
            {
                $validator = Validator::make($request->all(), [
                    'device_id' => 'required',
                    'dtTo' => 'required',
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
            
            if(!empty($request['payment_progress_sts'])){
                if(!empty($request['dtFrom']))
                {
                    $data = OrderDining::get_order_mac_address_date($request->all());
                    
                }else{
                    $data = OrderDining::get_order_mac_address_sts($request->all());
                }
            }else{
                if(!empty($request['dtFrom']))
                {
                    $data = OrderDining::get_order_mac_address_all_date($request->all());
                    // dd($data);
                }else{
                    $data = OrderDining::get_order_mac_address_all($request->all());
                }
            }
            // dd($data);
                
                if(count($data) != 0){
                    foreach($data as $dining){
                        $order_detail = OrderDiningDetail::get_order_detail($dining['id']);
                        $image_outlet = OutletImages::where('fboutlet_id', $dining['fboutlet_id'])->first();
                        if($image_outlet != null){
                            // $image_outlet = $image_outlet['filename'];
                            $dining['outlet_image'] = $image_outlet['filename'];
                        }else{
                            $dining['outlet_image'] = null;
                            // $image_outlet = null;
                        }
                        if(count($order_detail) != 0){
                            $data_detail = null;
                            foreach ($order_detail as $detail){
                                $order_sdishs = OrderDiningDetail::get_order_shdishs($detail['id']);
                                if($order_sdishs == null){
                                    $sdishs = ['order_sdishs' => null]; 
                                }else{
                                    $sdishs = ['order_sdishs' => $order_sdishs]; 
                                }
                                $detail = json_decode(json_encode($detail), true); 
                                $data_detail[] =  $detail + $sdishs;       
                            }
                            $dining = json_decode(json_encode($dining), true);
                            $data_order[] = $dining+['order_detail' => $data_detail]; 
                            
                        }else{
                            $dining = json_decode(json_encode($dining), true);
                            $data_order[] = $dining+['order_detail' => null];  
                        }
                    }
                    $response = [
                        'status' => true,
                        'code' => 200,
                        'message' => __('message.data_found' ),
                        'data' => $data_order,
                    ];
                    return response()->json($response, 200);
                }
                else{
                    $response = [
                        'status' => true,
                        'code' => 200,
                        'message' => __('message.data_not_found' ),
                        'data' => null,
                    ];
                    return response()->json($response, 200);
                }
            
        }
        catch (Throwable $e) 
        {        
            report($e);
            $error = ['modul' => 'get_order_dining_member',
                'actions' => 'get data order dining member',
                'error_log' => $e,
                'device' => "0" ];
            $report = $this->LogController->error_log($error);
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_not_found' ),
                'data' => null,
            ];
            return response()->json($response, 200);
        }
    }

    /**
	 * Function: replace data order dining failed
	 * body: transaction no
	 *	$request	: 
	*/
    public function replace_order_dining(Request $request)
    {
        try { 
            
            $validator = Validator::make($request->all(), [
                'transaction_no' => 'required',
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
            
                $day = date('d');
                // return response()->json($data, 200);
                    $cekSeq = OrderDining::latest('id')->first();
                    // dd($cekSeq);
                    // return response()->json($cekSeq, 200);
                    if($cekSeq == null)
                    {
                        $transaction_no = "THG/".date('Ymd')."/D-1";
                        $confirm_code = date('Ymd')."1";
                    }
                    else
                    {
                    $PecahCreated_at= explode(" ", $cekSeq['created_at']);
                    // $last_created=$PecahCreated_at[0]." ".$PecahCreated_at[1]." ".$PecahCreated_at[2];
                    $last_data = explode("-",$PecahCreated_at[0]);
                        if ($last_data[2] != $day)
                        {
                            $transaction_no = "THG/".date('Ymd')."/D-1";
                            $confirm_code = date('Ymd')."1";
                        }
                        else
                        {
                            $PecahTransaction= explode("D-", $cekSeq['transaction_no']);
                            // dd($PecahTransaction);
                            $seq_no = $PecahTransaction[1]+1;
                            // dd($PecahTransaction);
                            $transaction_no = "THG/".date('Ymd')."/D-".$seq_no;
                            $confirm_code = date('Ymd').$seq_no;
                           
                        }
                    }    
                $dining = OrderDining::where('transaction_no', $request['transaction_no'])->first();
            if($dining != null){
                    $order_detail = OrderDiningDetail::get_order_detail($dining['id']);
                    if(count($order_detail) != 0){
                        foreach ($order_detail as $detail){
                            $order_sdishs = OrderDiningDetail::get_order_shdishs($detail['id']);
                            $order_sdishs = ['order_sdishs' => $order_sdishs]; 
                            $detail = json_decode(json_encode($detail), true); 
                            $data_detail[] = $detail + $order_sdishs;         
                        }
                        $data = ['order' => $dining,
                                'order_detail' => $data_detail];

                        $data['order']['new_transaction_no'] = $transaction_no;
                        $data['order']['new_confirmation_code'] = $confirm_code;
                    }
                    $save_order = OrderDIning::new_order($data['order']);
                        if($save_order){                            
                            foreach ($data['order_detail'] as $order){
                                $id_parent= null;                                
                                if(empty($order['promo_id'])){
                                    $data_detail = $order+[
                                                "new_transaction_id" => $save_order['id']
                                                ];
                                    $save_detail = OrderDiningDetail::add_order_detail_failed($data_detail);
                                    // dd($save_detail);
                                    if($save_detail){
                                        $id_parent= $save_detail['id'];
                                        if(!empty($order['order_sdishs'])){
                                            foreach($order['order_sdishs'] as $sdish){
                                                $data_sdish = $sdish;
                                                $data_sdish["parent_id"] = $id_parent;
                                                $data_sdish["new_transaction_id"] = $save_detail['transaction_id'];
                                                $save_detail = OrderDiningDetail::add_order_sdish_failed($data_sdish);
                                            }
                                        }
                                    }
                                }
                                else{
                                    
                                    $data_detail = $order+[
                                                "new_transaction_id" => $save_order['id']
                                                ];
                                    $save_detail = OrderDiningDetail::add_order_detail_promo_failed($data_detail);
                                    if($save_detail){
                                        $id_parent= $save_detail['id'];
                                        if(!empty($order['order_sdishs'])){
                                            foreach($order['order_sdishs'] as $sdish){
                                                $data_sdish = $sdish;
                                                $data_sdish["parent_id"] = $id_parent;
                                                $data_sdish["new_transaction_id"] = $save_detail['transaction_id'];
                                                $save_detail = OrderDiningDetail::add_order_sdish_failed($data_sdish);
                                            }
                                        }
                                    }
                                }
                            }
                        $payment_source = Msystem::get_payment_source();
                        $dining = json_decode(json_encode($save_order), true); 
                        $order_detail = OrderDiningDetail::get_order_detail($dining['id']);
                        $data = OrderDining::where('id',$save_order['id'])->first();
                        $update_statusdining = OrderDining::update_status_failed($request->all());
                            if(count($order_detail) != 0){
                                $data_detail=[];
                                foreach ($order_detail as $detail){
                                    $data_order = null;
                                    $order_sdishs = OrderDiningDetail::get_order_shdishs($detail['id']);
                                    $order_sdishs = ['order_sdishs' => $order_sdishs]; 
                                    $detail = json_decode(json_encode($detail), true); 
                                    $data_order =  $detail + $order_sdishs; 
                                    array_push($data_detail, $data_order);    
                                }
                                $data = [
                                        'order' => $data,
                                        'order_detail' => $data_detail,
                                        'payment_source' => $payment_source];
                            }
                            else{
                                $data = [
                                        'order' => $data,
                                        'order_detail' => null,
                                        'payment_source' => $payment_source];
                            }
                            $approver = User::get_user_id($data['order']['approver_id']);
                            // $sendNotificationUser = $this->FcmController->send_notification_order_user($dataNotif);
                            $notif = [
                                'fboutlet_id' => $data['order']['fboutlet_id'],
                                'approver_id' => $data['order']['approver_id'],
                                'approver_name' => $approver[0]['full_name'],
                                'transaction_no' => $data['order']['transaction_no'],
                                'data' => $data['order']
                            ];
                            $sendNotification = $this->FcmController->send_notification_approve_order($notif);
                        $response = [
                            'status' => true,
                            'code' => 200,
                            'message' => __('message.data_saved_success' ),
                            'data' => $data
                        ];
                        return response()->json($response, 200);
                    }
            }
                
            $response = [
                'status' => false,
                'code' => 400,
                'message' => 'erorr', 
                'data' =>null,
                
            ];
            return response()->json($response, 200);
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'order_dining',
                'actions' => 'replace data order dining',
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

    public function cancel_order_otomatis(){
        try{
            
            $check_transaction = OrderDining::get();
            foreach($check_transaction as $dining_transaction){
                if($dining_transaction['payment_progress_sts'] === '0' or $dining_transaction['payment_progress_sts'] === '1'){
                    $awal  = date_create($dining_transaction['created_at']);
                    $akhir = date_create(); // waktu sekarang
                    $diff  = date_diff( $awal, $akhir );
                    if(($diff->d > 0)){
                        // echo $dining_transaction['id'];
                        
                        $update= OrderDining::where('id', $dining_transaction['id'])
                                ->update([
                                    'payment_progress_sts' => 5,
                                    'status_notification' => 0
                                    ] );
                    $data = OrderDining::where('transaction_no','=', $dining_transaction['transaction_no'])
                                        ->first();
                    // print_r($data['transaction_no']);
                    $sendNotificationUser = $this->FcmController->send_notification_order_user_canceled($data);
                    // dd($sendNotificationUser);
                    }elseif(($diff->d == 0)){
                        if($diff->h >= 3){
                            // echo $dining_transaction['id'];
                            $update= OrderDining::where('id', $dining_transaction['id'])
                                ->update([
                                    'payment_progress_sts' => 5,
                                    'status_notification' => 0
                                    ] );
                                    $data = OrderDining::where('transaction_no','=', $dining_transaction['transaction_no'])
                                    ->first();
                                    // print_r($data['transaction_no']);
                                    $sendNotificationUser = $this->FcmController->send_notification_order_user_canceled($data);
                                    // dd($sendNotificationUser);
                        }
                    }
                }
                
            }
        }catch(Exception $e) {
            report($e);
            $error = ['modul' => 'cron job cancel dining',
                'actions' => 'cron job cancel dining',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return;
        }
    }
}
