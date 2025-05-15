<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class OrderDining extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'fb_transactions';
    protected $fillable = [
        'transaction_no','is_member',
        'customer_id','customer_name',
        'payment_source','payment_progress_sts',
        'approver_id','table_no','total_price',
        'pg_payment_status','mpg_id','status_notification',
        'mpg_url','tax','confirmation_code','sub_total_price',
        'currency','fboutlet_id','os_type','note','device_id'
    ];

    /*
	 * Function: change format timestamp
	 * Param: 
	 *	$request	: 
	 */

    public function getCreatedAtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['created_at'])
        ->format('Y-m-d H:i');
    }
    /*
        * Function: change format timestamp
        * Param: 
        *	$request	: 
        */
    public function getUpdatedAtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['updated_at'])
        ->format('Y-m-d H:i');
    }

    /* 
	 * Function: add data fb-trxs non member
	 * Param: 
	 *	$request	: id
	 */
    // public static function add_order($request){
    //     $result= OrderDining::create([
    //                             'trx_no' => $request['trx_no'],
    //                             'customer_name' => $request['customer_name'],
    //                             'payment_method_id' => $request['payment_method_id'],
    //                             'payment_progress_sts' => $request['payment_progress_sts'],
    //                             'approver_id' => $request['approver_id'],
    //                             'table_no' => $request['table_no'],
    //                             ] );
    //     return $result;
    // }

    /* 
	 * Function: add data fb-trxs non member
	 * Param: 
	 *	$request	: id
	 */
    public static function add_order($request){
        if(empty($request['device_id'])){
            $request['device_id'] = null;
        }

        $result= OrderDining::create([
                                'transaction_no' => $request['transaction_no'],
                                'is_member' => $request['is_member'],
                                'customer_id' => $request['customer_id'],
                                'customer_name' => $request['customer_name'],
                                'fboutlet_id' => $request['fboutlet_id'],
                                'payment_source' => '',
                                'payment_progress_sts' => 0,
                                'approver_id' => null,
                                'table_no' => $request['table_no'],
                                'total_price' => $request['total_price'],
                                'tax' => $request['tax'],
                                'confirmation_code' => $request['confirmation_code'],
                                'sub_total_price' =>$request['sub_total_price'],
                                'os_type' => $request['os_type'],
                                'device_id' => $request['device_id'],
                                'status_notification' => 0
                                ] );
        return $result;
    }

    /* 
	 * Function: add data fb-trxs failed
	 * Param: 
	 *	$request	: id
	 */
    public static function new_order($request){
        // dd($request['new_transaction_no']);
        if(empty($request['customer_id'])){
            $request['customer_id'] = null;
        }
        if(empty($request['device_id'])){
            $request['device_id'] = null;
        }
        $result= OrderDining::create([
                                'transaction_no' => $request['new_transaction_no'],
                                'is_member' => $request['is_member'],
                                'customer_id' => $request['customer_id'],
                                'customer_name' => $request['customer_name'],
                                'fboutlet_id' => $request['fboutlet_id'],
                                'payment_source' => '',
                                'payment_progress_sts' => 1,
                                'approver_id' => $request['approver_id'],
                                'table_no' => $request['table_no'],
                                'total_price' => $request['total_price'],
                                'tax' => $request['tax'],
                                'confirmation_code' => $request['new_confirmation_code'],
                                'sub_total_price' =>$request['sub_total_price'],
                                'os_type' => $request['os_type'],
                                'device_id' => $request['device_id'],
                                'status_notification' => 0
                                ] );
        return $result;
    }

    /* 
	 * Function: add data fb-trxs failed
	 * Param: 
	 *	$request	: id
	 */
    public static function replace_order($request){
        // dd($request['new_transaction_no']);
        if(empty($request['customer_id'])){
            $request['customer_id'] = null;
        }
        if(empty($request['device_id'])){
            $request['device_id'] = null;
        }
        $result= OrderDining::create([
                                'transaction_no' => $request['new_transaction_no'],
                                'is_member' => $request['is_member'],
                                'customer_id' => $request['customer_id'],
                                'customer_name' => $request['customer_name'],
                                'fboutlet_id' => $request['fboutlet_id'],
                                'payment_source' => '',
                                'payment_progress_sts' => $request['payment_progress_sts'],
                                'approver_id' => $request['approver_id'],
                                'table_no' => $request['table_no'],
                                'total_price' => $request['total_price'],
                                'tax' => $request['tax'],
                                'confirmation_code' => $request['new_confirmation_code'],
                                'sub_total_price' =>$request['sub_total_price'],
                                'os_type' => $request['os_type'],
                                'device_id' => $request['device_id'],
                                'status_notification' => 0
                                ] );
        return $result;
    }

    /* 
	 * Function: update data fb-trxs approver
	 * Param: 
	 *	$request	: id
	 */
    public static function update_approver($request){
        // dd($request['payment_progress_sts']);
        $result= OrderDining::where('transaction_no', $request['transaction_no'])
                            ->update([
                                'payment_progress_sts' => $request['payment_progress_sts'],
                                'approver_id' => $request['approver_id'],
                                'status_notification' => 0
                                ] );
        return $result;
    }

    /* 
	 * Function: update data fb-trxs approver
	 * Param: 
	 *	$request	: id
	 */
    public static function update_status_failed($request){
        // dd($request['payment_progress_sts']);
        $result= OrderDining::where('transaction_no', $request['transaction_no'])
                            ->update([
                                'payment_progress_sts' => 4
                                ] );
        return $result;
    }
    
     /* 
	 * Function: update data fb-trxs approver
	 * Param: 
	 *	$request	: id
	 */
    public static function update_approver_with_order($request){
        // dd($request['transaction_no']);
        if(empty($request['note'])){
            $request['note'] = null;
        }
        $result= OrderDining::where('transaction_no', $request['transaction_no'])
                            ->update([
                                'payment_progress_sts' => $request['payment_progress_sts'],
                                'approver_id' => $request['approver_id'],
                                'total_price'=> $request['total_price'],
                                'sub_total_price'=> $request['sub_total_price'],
                                'tax'=> $request['tax'],
                                'note' => $request['note'],
                                'status_notification' => 0
                                ] );
        return $result;
    }

    /* 
	 * Function: update data fb-trxs payment
	 * Param: 
	 *	$request	: id
	 */
    public static function update_payment_source($request){
        // dd($request);
        $result= OrderDining::where('transaction_no', $request['transaction_no'])
                            ->update([
                                // 'payment_source' => $request['payment_source'],
                                'payment_source' => '',
                                'currency' => $request['currency']
                                ] );
        return $result;
    }

    /* 
	 * Function: update data status fb-trxs
	 * Param: 
	 *	$request	: id
	 */
    public static function update_status_dining($request){
        if(!empty($request['pg_payment_status']) || $request['pg_payment_status']  != null){
            $result= OrderDining::where('transaction_no', $request['transaction_no'])
                                ->update([
                                    'payment_progress_sts' => $request['payment_progress_sts'],
                                    'pg_payment_status' => $request['pg_payment_status'],
                                    'pg_transaction_status' => $request['pg_transaction_status'],
                                    'status_notification' => 0
                                    ] );
        }else{
            $result= OrderDining::where('transaction_no', $request['transaction_no'])
                                ->update([
                                    'payment_progress_sts' => $request['payment_progress_sts'],
                                    'status_notification' => 0
                                    ] );
        }
        
        return $result;
    }

    /* 
	 * Function: update data os type fb-trxs
	 * Param: 
	 *	$request	: id
	 */
    public static function update_os_type_dining($request){
        // dd($request);
        if(!empty($request['ddevice_id'])){
            $result= OrderDining::where('transaction_no', $request['transaction_no'])
                                        ->update([
                                            'os_type' => $request['os_type'],
                                            'device_id' => $request['device_id']
                                            ] );
        }else{
            $result= OrderDining::where('transaction_no', $request['transaction_no'])
                                        ->update([
                                            'os_type' => $request['os_type']
                                            ] );
        }
        
        return $result;
    }

    /* 
	 * Function: update data approver fb-trxs payment
	 * Param: 
	 *	$request	: transaction_ni
	 */
    public static function add_approver($request){
        // dd($request);
        $result= OrderDining::where('transaction_no', $request['transaction_no'])
                            ->update([
                                'approver_id' => $request['approver_id'],
                                'status_notification' => 0
                                ] );
        return $result;
    }

    /* 
	 * Function: get data fb-transaction non member
	 * Param: 
	 *	$request	: id
	 */
    public static function get_order($request){
        // dd($request);
        $result= OrderDining::select('fb_transactions.*','fboutlets.name as name_outlet','fboutlets.tax as outlet_tax','fboutlets.service as outlet_service','hotels.name as name_hotel')
                            ->join('fboutlets', 'fboutlets.id','=','fb_transactions.fboutlet_id')
                            ->join('hotels', 'hotels.id','=','fboutlets.hotel_id')
                                ->where('transaction_no' , $request)
                                ->first();
        return $result;
    }

    public static function get_order_customer_sts($request){
        if(empty($request['confirmation_code'])){
            $result= OrderDining::where('customer_id' , $request['customer_id'])
                                ->where('payment_progress_sts' , $request['payment_progress_sts'])
                                ->join('fboutlets', 'fboutlets.id', '=', 'fb_transactions.fboutlet_id')
                                ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                                ->select('fb_transactions.*','hotels.name as name_hotel','fboutlets.name as outlet_name','fboutlets.tax as outlet_tax','fboutlets.service as outlet_service')
                                ->orderBy('fb_transactions.created_at','DESC')
                                ->get();
        }
        else{
            $result= OrderDining::where('customer_id' , $request['customer_id'])
                                ->where('payment_progress_sts' , $request['payment_progress_sts'])
                                ->where('confirmation_code' , $request['confirmation_code'])
                                ->join('fboutlets', 'fboutlets.id', '=', 'fb_transactions.fboutlet_id')
                                ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                                ->select('fb_transactions.*','hotels.name as name_hotel','fboutlets.name as outlet_name','fboutlets.tax as outlet_tax','fboutlets.service as outlet_service')
                                ->orderBy('fb_transactions.created_at','DESC')
                                ->get();
        }
        
        return $result;
    }

    public static function get_order_customer_all($request){
        if(empty($request['confirmation_code'])){
            $result= OrderDining::where('customer_id' , $request['customer_id'])
                                ->join('fboutlets', 'fboutlets.id', '=', 'fb_transactions.fboutlet_id')
                                ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                                ->select('fb_transactions.*','hotels.name as name_hotel','fboutlets.name as outlet_name','fboutlets.tax as outlet_tax','fboutlets.service as outlet_service')
                                ->orderBy('fb_transactions.created_at','DESC')
                                ->get();
        }
        else{
            $result= OrderDining::where('customer_id' , $request['customer_id'])
                                ->where('confirmation_code' , $request['confirmation_code'])
                                ->join('fboutlets', 'fboutlets.id', '=', 'fb_transactions.fboutlet_id')
                                ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                                ->select('fb_transactions.*','hotels.name as name_hotel','fboutlets.name as outlet_name','fboutlets.tax as outlet_tax','fboutlets.service as outlet_service')
                                ->orderBy('fb_transactions.created_at','DESC')
                                ->get();
        }
        
        return $result;
    }

    public static function get_order_customer_date($request){
        $date = date('Y-m-d', strtotime("+1 day", strtotime($request['dtTo'])));
        if(empty($request['confirmation_code'])){
            $result= OrderDining::where('customer_id' , $request['customer_id'])
                                ->where('payment_progress_sts' , $request['payment_progress_sts'])
                                ->whereBetween('fb_transactions.created_at',[$request['dtFrom'],$date])
                                ->join('fboutlets', 'fboutlets.id', '=', 'fb_transactions.fboutlet_id')
                                ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                                ->select('fb_transactions.*','hotels.name as name_hotel','fboutlets.name as outlet_name','fboutlets.tax as outlet_tax','fboutlets.service as outlet_service')
                                ->orderBy('fb_transactions.created_at','DESC')
                                ->get();
        }else{
            $result= OrderDining::where('customer_id' , $request['customer_id'])
                                ->where('payment_progress_sts' , $request['payment_progress_sts'])
                                ->where('confirmation_code' , $request['confirmation_code'])
                                ->whereBetween('fb_transactions.created_at',[$request['dtFrom'],$date])
                                ->join('fboutlets', 'fboutlets.id', '=', 'fb_transactions.fboutlet_id')
                                ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                                ->select('fb_transactions.*','hotels.name as name_hotel','fboutlets.name as outlet_name','fboutlets.tax as outlet_tax','fboutlets.service as outlet_service')
                                ->orderBy('fb_transactions.created_at','DESC')
                                ->get();
        }        
        return $result;
    }

    public static function get_order_customer_all_date($request){
        $date = date('Y-m-d', strtotime("+1 day", strtotime($request['dtTo'])));
        if(empty($request['confirmation_code'])){
            $result= OrderDining::where('fb_transactions.customer_id' , $request['customer_id'])
                                ->whereBetween('fb_transactions.created_at',[$request['dtFrom'],$date])
                                ->join('fboutlets', 'fboutlets.id', '=', 'fb_transactions.fboutlet_id')
                                ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                                ->select('fb_transactions.*','hotels.name as name_hotel','fboutlets.name as outlet_name','fboutlets.tax as outlet_tax','fboutlets.service as outlet_service')
                                ->orderBy('fb_transactions.created_at','DESC')
                                ->get();
        }else{
            $result= OrderDining::where('customer_id' , $request['customer_id'])
                                ->where('confirmation_code' , $request['confirmation_code'])
                                ->whereBetween('created_at',[$request['dtFrom'],$date])
                                ->join('fboutlets', 'fboutlets.id', '=', 'fb_transactions.fboutlet_id')
                                ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                                ->select('fb_transactions.*','hotels.name as name_hotel','fboutlets.name as outlet_name','fboutlets.tax as outlet_tax','fboutlets.service as outlet_service')
                                ->orderBy('fb_transactions.created_at','DESC')
                                ->get();
        }        
        return $result;
    }

    public static function get_order_non_memeber($request){
        // dd($request);
        $result= OrderDining::where('uniqueid' , $request['uniqueid'])
                                ->get();
        return $result;
    }

    /* 
	 * Function: get data fb-transaction non member
	 * Param: 
	 *	$request	: id
	 */
    public static function get_order_id($request){
        // dd($request);
        $result= OrderDining::where('transaction_no' , $request)
                                ->first();
        return $result;
    }


    /* 
	 * Function: get data fb-transaction on progress
	 * Param: 
	 *	$request	:
	 */
    public static function get_order_progress(){
        $result= OrderDining::where('payment_progress_sts' , 0)
                                ->orWhere('payment_progress_sts' , 1)
                                ->where('pg_payment_status' , null)
                                ->get();
        return $result;
    }

    /* 
	 * Function: get data fb-transaction on done
	 * Param: 
	 *	$request	:
	 */
    public static function get_order_done(){
        $result= OrderDining::where('payment_progress_sts' , 6)
                                ->where('pg_payment_status' , 'paid')
                                ->get();
        return $result;
    }
    /* 
	 * Function: get data fb-transaction on failed
	 * Param: 
	 *	$request	:
	 */
    public static function get_order_failed(){
        $result= OrderDining::where('payment_progress_sts' , 2)
                                ->orWhere('payment_progress_sts' , 4)
                                ->orWhere('payment_progress_sts' , 5)
                                ->where('pg_payment_status' , null)
                                ->orWhere('pg_payment_status' , 'unpaid')
                                ->get();
        return $result;
    }

    /* 
	 * Function: get data fb-transaction 
	 * Param: 
	 *	$request	:
	 */
    public static function get_order_fb($request){
        // dd($request['page']);
      if(empty($request['page'])){
        $start = 0;
        $finish = 10;
      }else{
        $finish = $request['page']*10;
        $start = $finish - 10;
      }
    //   dd($start);

            if(strtoupper($request['status']) == 'NEW'){
                $data= DB::select("SELECT *
                                    FROM fb_transactions 
                                    WHERE transaction_no IN
                                    (
                                        SELECT transaction_no
                                        FROM fb_transactions 
                                        WHERE payment_progress_sts = 0
                                    )
                                    AND fboutlet_id = '{$request['fboutlet_id']}'
                                    AND approver_id IS NULL
                                    ORDER BY created_at DESC LIMIT $start, $finish");
                $total= DB::select("SELECT *
                                    FROM fb_transactions 
                                    WHERE transaction_no IN
                                    (
                                        SELECT transaction_no
                                        FROM fb_transactions 
                                        WHERE payment_progress_sts = 0
                                    )
                                    AND fboutlet_id = '{$request['fboutlet_id']}'
                                    AND approver_id IS NULL
                                    ORDER BY created_at DESC");
            }elseif(strtoupper($request['status']) == 'ON_PROGRESS'){
                $data= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 1
                                                    -- OR payment_progress_sts = 1
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            ORDER BY created_at DESC LIMIT $start, $finish");
                $total= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 1
                                                    -- OR payment_progress_sts = 1
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            ORDER BY created_at DESC");                                                 
                // dd($total);
            }elseif(strtoupper($request['status']) == 'ORDER_PICKUP'){
                $data= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 0
                                                    -- OR payment_progress_sts = 1
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            ORDER BY created_at DESC LIMIT $start, $finish");
                $total= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 0
                                                    -- OR payment_progress_sts = 1
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            ORDER BY created_at DESC");                                                 
                // dd($total);
            }elseif(strtoupper($request['status']) == 'DONE'){
                $data= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 6
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            ORDER BY created_at DESC LIMIT $start, $finish");
                $total= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 6
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            ORDER BY created_at DESC");                                           
            }elseif(strtoupper($request['status']) == 'PAID'){
                $data= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 3
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            ORDER BY created_at DESC LIMIT $start, $finish");
                $total= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 3
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            ORDER BY created_at DESC");                                           
            }elseif(strtoupper($request['status']) == 'FAILED'){
                $data= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 2
                                                    OR payment_progress_sts = 4
                                                    OR payment_progress_sts = 5
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            ORDER BY created_at DESC LIMIT $start, $finish");
                $total= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 2
                                                    OR payment_progress_sts = 4
                                                    OR payment_progress_sts = 5
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            ORDER BY created_at DESC");             
            }           
        // dd(count($total));
        if(count($data) == 0){
            $data = null;
        }
        $result = ['data' => $data,
                    'total_data' => count($total)];
        return $result;
    }

    /* 
	 * Function: get data fb-transaction by date
	 * Param: 
	 *	$request	:
	 */
    public static function get_order_fb_by_date($request){
        if(empty($request['page'])){
            $start = 0;
            $finish = 10;
          }else{
            $finish = $request['page']*10;
            $start = $finish - 10;
          }
          $date = date('Y-m-d', strtotime("+1 day", strtotime($request['dtTo'])));
        //   dd($date);
            if(strtoupper($request['status']) == 'NEW'){
                
                $data= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                WHERE payment_progress_sts = 0
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id IS NULL
                                            AND created_at BETWEEN '{$request['dtFrom']}' AND '{$date}'
                                            ORDER BY created_at DESC LIMIT $start, $finish");
                $total= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 0
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = 1
                                            AND created_at BETWEEN '{$request['dtFrom']}' AND '{$date}'
                                            ORDER BY created_at DESC");
                                                
            }elseif(strtoupper($request['status']) == 'ORDER_PICKUP'){
                $data= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 0
                                                    -- OR payment_progress_sts = 1
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            AND created_at BETWEEN '{$request['dtFrom']}' AND '{$date}'
                                            ORDER BY created_at DESC LIMIT $start, $finish");
                $total= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 0
                                                    -- OR payment_progress_sts = 1
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            AND created_at BETWEEN '{$request['dtFrom']}' AND '{$date}'
                                            ORDER BY created_at DESC");                                                 
                // dd($total);
            }elseif(strtoupper($request['status']) == 'ON_PROGRESS'){
                $data= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 1
                                                    -- OR payment_progress_sts = 1
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            AND created_at BETWEEN '{$request['dtFrom']}' AND '{$date}'
                                            ORDER BY created_at DESC LIMIT $start, $finish");
                $total= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 1
                                                    -- OR payment_progress_sts = 1
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            AND created_at BETWEEN '{$request['dtFrom']}' AND '{$date}'
                                            ORDER BY created_at DESC");                                                 
                // dd($total);
            }elseif(strtoupper($request['status']) == 'PAID'){
                $data= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 3
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            AND created_at BETWEEN '{$request['dtFrom']}' AND '{$date}'
                                            ORDER BY created_at DESC LIMIT $start, $finish");
                $total= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 3
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            AND created_at BETWEEN '{$request['dtFrom']}' AND '{$date}'
                                            ORDER BY created_at DESC");                                           
            }elseif(strtoupper($request['status']) == 'DONE'){
                $data= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 6
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            AND created_at BETWEEN '{$request['dtFrom']}' AND '{$date}'
                                            ORDER BY created_at DESC LIMIT $start, $finish");
                $total= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 6
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            AND created_at BETWEEN '{$request['dtFrom']}' AND '{$date}'
                                            ORDER BY created_at DESC");                                           
            }elseif(strtoupper($request['status']) == 'FAILED'){
                $data= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 2
                                                    OR payment_progress_sts = 4
                                                    OR payment_progress_sts = 5
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            AND created_at BETWEEN '{$request['dtFrom']}' AND '{$date}'
                                            ORDER BY created_at DESC LIMIT $start, $finish");
                $total= DB::select("SELECT *
                                            FROM fb_transactions 
                                            WHERE transaction_no IN
                                            (
                                                SELECT transaction_no
                                                FROM fb_transactions 
                                                    WHERE payment_progress_sts = 2
                                                    OR payment_progress_sts = 4
                                                    OR payment_progress_sts = 5
                                            )
                                            AND fboutlet_id = '{$request['fboutlet_id']}'
                                            AND approver_id = '{$request['approver_id']}'
                                            AND created_at BETWEEN '{$request['dtFrom']}' AND '{$date}'
                                            ORDER BY created_at DESC");             
            }           
        // dd(count($total));
        if(count($data) == 0){
            $data = null;
        }
        $result = ['data' => $data,
                    'total_data' => count($total)];
        return $result;
    }
    /* 
	 * Function: update data status notifikasi
	 * Param: 
	 *	$request	: transaction_no
	 */
    public static function update_status_notif($request){
        $result = OrderDining::where('transaction_no', $request)
                            ->update([
                                'status_notification' => 1,
                                ] );
        return $result;
    }

    /* 
	 * Function: get data order by mac address and status
	 * Param: 
	 *	$request	: mac address
	 */
    public static function get_order_mac_address_sts($request){
            $result= OrderDining::where('device_id' , $request['device_id'])
                                ->where('payment_progress_sts' , $request['payment_progress_sts'])
                                ->where('fb_transactions.is_member', 0)
                                ->join('fboutlets', 'fboutlets.id', '=', 'fb_transactions.fboutlet_id')
                                ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                                ->select('fb_transactions.*','hotels.name as name_hotel','fboutlets.name as outlet_name','fboutlets.tax as outlet_tax','fboutlets.service as outlet_service')
                                ->orderBy('fb_transactions.created_at','DESC')
                                ->get();
        return $result;
    }

    /* 
	 * Function: get data order by mac address and status and date
	 * Param: 
	 *	$request	: mac_address
	 */
    public static function get_order_mac_address_date($request){
        $date = date('Y-m-d', strtotime("+1 day", strtotime($request['dtTo'])));
        
            $result= OrderDining::where('device_id' , $request['device_id'])
                                ->where('payment_progress_sts' , $request['payment_progress_sts'])
                                ->where('fb_transactions.is_member', 0)
                                ->whereBetween('fb_transactions.created_at',[$request['dtFrom'],$date])
                                ->join('fboutlets', 'fboutlets.id', '=', 'fb_transactions.fboutlet_id')
                                ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                                ->select('fb_transactions.*','hotels.name as name_hotel','fboutlets.name as outlet_name','fboutlets.tax as outlet_tax','fboutlets.service as outlet_service')
                                ->orderBy('fb_transactions.created_at','DESC')
                                ->get();    
        return $result;
    }

    /* 
	 * Function: update data status payment
	 * Param: 
	 *	$request	: id
	 */
    public static function update_status_payment_dining($request){
        // dd($request['os_type']);
            $result = OrderDining::where('transaction_no', $request['transaction_no'])
                ->update([
                        'pg_payment_status' => $request['pg_payment_status'],
                        'pg_transaction_status' => $request['pg_transaction_status'],
                        'payment_progress_sts' => $request['payment_progress_sts'],
                    ] );
        return $result;        
    }

    /* 
	 * Function: get data order by mac address and date
	 * Param: 
	 *	$request	: mac_address
	 */
    public static function get_order_mac_address_all_date($request){
        $date = date('Y-m-d', strtotime("+1 day", strtotime($request['dtTo'])));
           $result= OrderDining::where('fb_transactions.device_id' , $request['device_id'])
                                ->where('fb_transactions.is_member', 0)
                                ->whereBetween('fb_transactions.created_at',[$request['dtFrom'],$date])
                                ->join('fboutlets', 'fboutlets.id', '=', 'fb_transactions.fboutlet_id')
                                ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                                ->select('fb_transactions.*','hotels.name as name_hotel','fboutlets.name as outlet_name','fboutlets.tax as outlet_tax','fboutlets.service as outlet_service')
                                ->orderBy('fb_transactions.created_at','DESC')
                                ->get();
           
        return $result;
    }

    /* 
	 * Function: get data order by mac address
	 * Param: 
	 *	$request	: mac_address
	 */
    public static function get_order_mac_address_all($request){
       
            $result= OrderDining::where('device_id' , $request['device_id'])
                                ->where('fb_transactions.is_member', 0)
                                ->join('fboutlets', 'fboutlets.id', '=', 'fb_transactions.fboutlet_id')
                                ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                                ->select('fb_transactions.*','hotels.name as name_hotel','fboutlets.name as outlet_name','fboutlets.tax as outlet_tax','fboutlets.service as outlet_service')
                                ->orderBy('fb_transactions.created_at','DESC')
                                ->get();
        
        return $result;
    }

    /* 
	 * Function: count notification by device id
	 * Param: 
	 *	$request	: id
	 */
    public static function get_total_notif_dining_all($request){
       $result= OrderDining::where('device_id' , $request['device_id'])
                ->where('fb_transactions.is_member', 0)
                ->where('fb_transactions.status_notification', 0)
                ->count();
        return $result;
    }
}
// [$request['dtFrom'],$request['dtTo']

