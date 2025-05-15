<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promo extends Model
{
    use HasFactory;
    use SoftDeletes;
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','description','value',
        'max_discount_price','valid_from',
        'valid_to','deleted_flag','created_by',
        'updated_by','fboutlet_id'
    ];
    protected $dates = ['deleted_at'];
    

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
    public function getValidFromAtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['valid_from'])
        ->format('d, M Y H:i');
    }

    /*
	 * Function: change format timestamp
	 * Param: 
	 *	$request	: 
	 */
    public function getValidToAtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['valid_to'])
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
    
    public static function get_promo_all(){
        // $result =Promo::orderBy('id','ASC')
        // ->get();
        $result = Promo::select('promos.*', 'fboutlets.name as name_outlet')
                ->join('fboutlets','fboutlets.id','=','promos.fboutlet_id')
                ->where('fboutlets.deleted_at','=', null)
                ->orderBy('promos.fboutlet_id','ASC')
                ->orderBy('promos.created_at','DESC')
                ->get();
    return $result;
    }

    public static function get_all_promo_with_hotel($request){
        if(!empty($request['user_id']) && $request['user_id'] != null) {
            $result = Promo::select('promos.*', 'fboutlets.name as name_outlet', 'fboutlets.id as id_outlet', 'hotels.name as name_hotel', 'hotels.id as id_hotel')
                            ->join('fboutlets','fboutlets.id','=','promos.fboutlet_id')
                            ->join('fboutlet_users', 'fboutlet_users.fboutlet_id', '=', 'fboutlets.id')
                            ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                            ->where('fboutlets.deleted_at','=', null)
                            ->where('hotels.id', '=', $request['hotel_id'])
                            ->where('fboutlet_users.user_id', '=', $request->user_id)
                            ->where('fboutlet_users.deleted_at', '=', null)
                            ->orderBy('promos.fboutlet_id','ASC')
                            ->orderBy('promos.created_at','DESC')
                            ->get();
        } else {
            $result = Promo::select('promos.*', 'fboutlets.name as name_outlet', 'fboutlets.id as id_outlet', 'hotels.name as name_hotel', 'hotels.id as id_hotel')
                            ->join('fboutlets','fboutlets.id','=','promos.fboutlet_id')
                            ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                            ->where('fboutlets.deleted_at','=', null)
                            ->where('hotels.id', '=', $request['hotel_id'])
                            ->orderBy('promos.fboutlet_id','ASC')
                            ->orderBy('promos.created_at','DESC')
                            ->get();
        }
        return $result;
    }

    public static function get_all_promo_with_outlet($request){
        if(!empty($request['user_id']) && $request['user_id'] != null) {
            $result = Promo::select('promos.*', 'fboutlets.name as name_outlet', 'fboutlets.id as id_outlet')
                            ->join('fboutlets','fboutlets.id','=','promos.fboutlet_id')
                            ->join('fboutlet_users', 'fboutlet_users.fboutlet_id', '=', 'fboutlets.id')
                            ->where('fboutlets.deleted_at','=', null)
                            ->where('fboutlets.id', '=', $request['outlet_id'])
                            ->where('fboutlet_users.user_id', '=', $request->user_id)
                            ->where('fboutlet_users.deleted_at', '=', null)
                            ->orderBy('promos.fboutlet_id','ASC')
                            ->orderBy('promos.created_at','DESC')
                            ->get();
        } else {
            $result = Promo::select('promos.*', 'fboutlets.name as name_outlet', 'fboutlets.id as id_outlet')
                            ->join('fboutlets','fboutlets.id','=','promos.fboutlet_id')
                            ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                            ->where('fboutlets.deleted_at','=', null)
                            ->where('fboutlets.id', '=', $request['outlet_id'])
                            ->orderBy('promos.fboutlet_id','ASC')
                            ->orderBy('promos.created_at','DESC')
                            ->get();
        }
        return $result;
    }


    public static function get_promo_id($request){
        // $result =Promo::orderBy('id','ASC')
        // ->get();
        $result = Promo::select('promos.*', 'fboutlets.name as name_outlet')
                ->join('fboutlets','fboutlets.id','=','promos.fboutlet_id')
                ->where('promos.id', $request) 
                ->where('fboutlets.deleted_at', '=', null)
                ->orderBy('promos.id','ASC')
                ->first();
    return $result;
    }

    public static function get_promo_outlet_with_user($request){
        $result = Promo::select('promos.*', 'fboutlets.name as name_outlet')->distinct()
                  ->join('fboutlets','fboutlets.id','=','promos.fboutlet_id')
                  ->join('fboutlet_users', 'fboutlet_users.fboutlet_id', '=', 'fboutlets.id')
                  ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                //   ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                  ->where('fboutlet_users.user_id', '=', $request->user_id)
                  ->where('fboutlet_users.deleted_at', '=', null)
                //   ->where('hotel_users.user_id', '=', $request->user_id)
                  ->where('fboutlets.deleted_at', '=', null)
                  ->orderBy('promos.fboutlet_id','ASC')
                  ->orderBy('promos.created_at','DESC')
                  ->get();
        return $result;
    }

    public static function check_promo($request){
        // dd($request);
        $result = Promo::Where('fboutlet_id',$request)
                        ->orderBy('valid_to', 'desc')
                        ->first();
        return $result;
    }

    /*
	 * Function: add data promo
	 * Param: 
	 *	$request	: name,
	 */
    public static function add_promo($request){
        if(empty($request['max_discount_price'])){
            $request['max_discount_price'] = null;
        }
        $result= Promo::create([
                                        'name' => $request['name'],
                                        'description' => $request['description'],
                                        'value' => $request['value'],
                                        'max_discount_price' => $request['max_discount_price'],
                                        'valid_from' => $request['valid_from'],
                                        'valid_to' => $request['valid_to'],
                                        'fboutlet_id' => $request['fboutlet_id'],
                                        'deleted_flag' => 0,
                                        'created_by' =>$request['created_by'],
                                        'updated_by' => $request['created_by'], 
                                             ] );
        return $result;
    }

    /*
	 * Function: update Promo
	 * Param: 
	 *	$request	: $data
	 */
    public static function edit_promo($request)
    {
        if(empty($request['max_discount_price'])){
            $request['max_discount_price'] = null;
        }
        $result= Promo::where('id', $request['id'])
                                    ->update( [
                                        'name' => $request['name'],
                                        'description' => $request['description'],
                                        'value' => $request['value'],
                                        'max_discount_price' => $request['max_discount_price'],
                                        'valid_from' => $request['valid_from'],
                                        'valid_to' => $request['valid_to'],
                                        'fboutlet_id' => $request['fboutlet_id'],
                                        'deleted_flag' => 0,
                                        'updated_by' => $request['updated_by'], 
                                             ] );
        return $result;
    }
    
    public static function getpromo_menu($request)
    {
        // dd($request);
        $date = date("Y-m-d");
        // dd($date);
        $result = Promo::where('fboutlet_id', $request)
                ->where('valid_to','>=',$date)
                ->orderBy('valid_to', 'desc')
                ->first();
        return $result;
    }

    // public static function getpromo_menuSdish($request)
    // {
    //     // dd($request);
    //     $date = date("Y-m-d");
    //     // dd($date);
    //     $result = Promo::select('promos.*')
    //             ->join('fboutlet_menus','fboutlet_menus.id',$request)
    //             ->where('promos.fboutlet_id', 'fboutlet_menus.fboutlet_id')
    //             ->where('promos.valid_to','>=',$date)
    //             ->orderBy('promos.valid_to', 'desc')
    //             ->first();
    //     return $result;
    // }
}
