<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDiningDetailSdishs extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'fb_transaction_sdishs';
    protected $fillable = [
        'fb_transaction_detail_id','fb_menu_id',
        'fb_menu_sdish_id'
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
	 * Function: add data fb-trx_sdish non promo
	 * Param: 
	 *	$request	: id
	 */
    public static function add_order_detail($request){
        // dd($request);
        $result= OrderDiningDetailSdishs::create([
                                'fb_transaction_detail_id' => $request['fb_transaction_detail_id'],
                                'fb_menu_id' => $request['fb_menu_id'],
                                'fb_menu_sdish_id' => $request['fb_menu_sdish_id'],
                                ] );
        return $result;
    }

     /* 
	 * Function: add data fb-trx_sdish non promo
	 * Param: 
	 *	$request	: id
	 */
    public static function get_order_shdishs($request){
        // dd($request);
        $result= OrderDiningDetailSdishs::where('fb_transaction_detail_id',$request)
                                        ->select('fb_transaction_sdishs.id','fb_transaction_sdishs.fb_transaction_detail_id',
                                                'fb_transaction_sdishs.fb_menu_id',
                                                'fb_transaction_sdishs.fb_menu_sdish_id', 'fboutlet_menus.name as name_sdish')
                                        ->join('fboutlet_mn_sdishs', 'fboutlet_mn_sdishs.id','=','fb_transaction_sdishs.fb_menu_sdish_id')
                                        ->join('fboutlet_menus', 'fboutlet_menus.id','=','fboutlet_mn_sdishs.fboutlet_mn_sdish_id')
                                        ->get();
        return $result;
    }
    
}
