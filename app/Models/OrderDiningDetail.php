<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDiningDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'fb_transaction_details';
    protected $fillable = [
        'transaction_id','fb_menu_id',
        'price','discount',
        'promo_id','promo_value',
        'max_discount_price','quantity',
        'note','parent_id','is_sidedish','amount'
    ];

    /*
	 * Function: change format timestamp
	 * Param: 
	 *	$request	: 
	 */
    public function getCreatedAtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['created_at'])
        ->format('d, M Y H:i');
    }
    /*
        * Function: change format timestamp
        * Param: 
        *	$request	: 
        */
    public function getUpdatedAtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['updated_at'])
        ->format('d, M Y H:i');
    }

    /* 
	 * Function: add data fb-trx_detail non promo
	 * Param: 
	 *	$request	: id
	 */
    public static function add_order_detail($request){
        if(empty($request['id'])){
            $request['id'] = null;
        }   
        $result= OrderDiningDetail::updateOrCreate(['id' => $request['id']],
                                [
                                'transaction_id' => $request['transaction_id'],
                                'fb_menu_id' => $request['fb_menu_id'],
                                'price' => $request['price'],
                                'amount' => $request['amount'],
                                'quantity' => $request['quantity'],
                                'note' => $request['note']
                                ] );
        return $result;
    }

    /* 
	 * Function: add data fb-trx_detail non promo
	 * Param: 
	 *	$request	: id
	 */
    public static function add_order_detail_failed($request){
        // return response()->json($request, 200);
        if(empty($request['note'])){
            $request['note'] = 'null';
        }
        $result= OrderDiningDetail::Create([
                                'transaction_id' => $request['new_transaction_id'],
                                'fb_menu_id' => $request['fb_menu_id'],
                                'price' => $request['price'],
                                'amount' => $request['amount'],
                                'quantity' => $request['quantity'],
                                'note' => $request['note']
                                ] );
        return $result;
    }

    public static function add_order_sdish_failed($request){
        // dd($request);
        if(empty($request['discount']) ){
           
            $request['discount'] = null;
            $request['promo_id'] = null;
            $request['promo_value'] = null;
            $request['max_discount_price'] = null;
        }
        $result= OrderDiningDetail::Create(
                                [
                                'transaction_id' => $request['new_transaction_id'],
                                'fb_menu_id' => $request['fb_menu_sdish_id'],
                                'parent_id' => $request['parent_id'],
                                'is_sidedish' => $request['is_sidedish'],
                                'price' => $request['price'],
                                'amount' => $request['amount'],
                                'quantity' => $request['quantity'],
                                'discount' => $request['discount'],
                                'promo_id' => $request['promo_id'],
                                'promo_value' => $request['promo_value'],
                                'max_discount_price' => $request['max_discount_price'],
                                'note' => null
                                ] );
        return $result;
    }

    /* 
	 * Function: add data fb-trx_detail promo
	 * Param: 
	 *	$request	: id
	 */
    public static function add_order_detail_promo_failed($request){
        if(empty($request['note'])){
            $request['note'] = 'null';
        }
        $result= OrderDiningDetail::Create(
                                [
                                'transaction_id' => $request['new_transaction_id'],
                                'fb_menu_id' => $request['fb_menu_id'],
                                'price' => $request['price'],
                                'amount' => $request['amount'],
                                'discount' => $request['discount'],
                                'promo_id' => $request['promo_id'],
                                'promo_value' => $request['promo_value'],
                                'max_discount_price' => $request['max_discount_price'],
                                'quantity' => $request['quantity'],
                                'note' => $request['note']
                                ] );
        return $result;
    }

    public static function add_order_sdish($request){
        // dd($request);
        if(empty($request['discount']) ){
           
            $request['discount'] = null;
            $request['promo_id'] = null;
            $request['promo_value'] = null;
            $request['max_discount_price'] = null;
        }
        if(empty($request['id'])){
            $request['id'] = null;
        }   
        $result= OrderDiningDetail::updateOrCreate(['id' => $request['id']],
                                [
                                'transaction_id' => $request['transaction_id'],
                                'fb_menu_id' => $request['fb_menu_sdish_id'],
                                'parent_id' => $request['parent_id'],
                                'is_sidedish' => $request['is_sidedish'],
                                'price' => $request['price'],
                                'amount' => $request['amount'],
                                'quantity' => $request['quantity'],
                                'discount' => $request['discount'],
                                'promo_id' => $request['promo_id'],
                                'promo_value' => $request['promo_value'],
                                'max_discount_price' => $request['max_discount_price'],
                                'note' => null
                                ] );
        return $result;
    }
    /* 
	 * Function: add data fb-trx_detail promo
	 * Param: 
	 *	$request	: id
	 */
    public static function add_order_detail_promo($request){
        if(empty($request['id'])){
            $request['id'] = null;
        }   
        $result= OrderDiningDetail::updateOrCreate(['id' => $request['id']],
                                [
                                'transaction_id' => $request['transaction_id'],
                                'fb_menu_id' => $request['fb_menu_id'],
                                'price' => $request['price'],
                                'amount' => $request['amount'],
                                'discount' => $request['discount'],
                                'promo_id' => $request['promo_id'],
                                'promo_value' => $request['promo_value'],
                                'max_discount_price' => $request['max_discount_price'],
                                'quantity' => $request['quantity'],
                                'note' => $request['note']
                                ] );
        return $result;
    }

    /* 
	 * Function: get data fb_transaction_detail promo
	 * Param: 
	 *	$request	: id
	 */
    public static function get_order_detail($request){
        $result= OrderDiningDetail::select('fb_transaction_details.id','fb_transaction_details.transaction_id','fb_transaction_details.fb_menu_id',
                                            'fboutlet_menus.name as menu_name','fb_transaction_details.price',
                                            'fb_transaction_details.discount','fb_transaction_details.promo_id','fb_transaction_details.amount', 
                                            'fb_transaction_details.promo_value','fb_transaction_details.max_discount_price',
                                             'fb_transaction_details.quantity','fb_transaction_details.note')
                                    ->join('fboutlet_menus', 'fboutlet_menus.id','=','fb_transaction_details.fb_menu_id')
                                    ->where('transaction_id' , $request)
                                    ->where('parent_id' , null)
                                    ->get();
        return $result;
    }

    

     /* 
	 * Function: get data fb_transaction_detail promo
	 * Param: 
	 *	$request	: id
	 */
    public static function get_order_shdishs($request){
        $result= OrderDiningDetail::select('fb_transaction_details.id','fb_transaction_details.transaction_id','fb_transaction_details.fb_menu_id as fb_menu_sdish_id',
                                            'fboutlet_menus.name as menu_name','fb_transaction_details.price',
                                            'fb_transaction_details.discount','fb_transaction_details.promo_id','fb_transaction_details.amount',
                                            'fb_transaction_details.is_sidedish',
                                            'fb_transaction_details.promo_value','fb_transaction_details.max_discount_price',
                                             'fb_transaction_details.quantity','fb_transaction_details.note')
                                    ->join('fboutlet_menus', 'fboutlet_menus.id','=','fb_transaction_details.fb_menu_id')
                                    ->where('parent_id' , $request)
                                    ->get();
        return $result;
    }

    

    /* 
	 * Function: get data fb_transaction_detail promo by outlet
	 * Param: 
	 *	$request	: id_transaction & id_outlet
	 */
    public static function get_order_detail_outlet($request){
        // dd($request['transaction_id']);
        $result= OrderDiningDetail::select('fb_transaction_details.id','fb_transaction_details.transaction_id','fb_transaction_details.fb_menu_id',
                                            'fboutlet_menus.name as menu_name','fb_transaction_details.price',
                                            'fb_transaction_details.discount','fb_transaction_details.promo_id', 
                                            'fb_transaction_details.promo_value','fb_transaction_details.max_discount_price',
                                             'fb_transaction_details.quantity','fb_transaction_details.note',
                                             'fb_transaction_details.amount','fb_transaction_details.discount',
                                             'fb_transaction_details.promo_id','fb_transaction_details.promo_value',
                                             'fb_transaction_details.max_discount_price')
                                    ->join('fboutlet_menus', 'fboutlet_menus.id','=','fb_transaction_details.fb_menu_id')
                                    ->where('transaction_id' , $request['transaction_id'])
                                    ->where('fboutlet_menus.fboutlet_id' , $request['fboutlet_id'])
                                    ->where('fb_transaction_details.parent_id' , null)
                                    ->get();
        return $result;
    }

    
    
    
}
