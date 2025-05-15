<?php
/*
* Model Outlet
* author : rangga.muharam@arkamaya.co.id
*	
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use League\CommonMark\Extension\Table\TableExtension;

class Outlet extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'fboutlets';
    protected $fillable = [
        'hotel_id','name','address',
        'longitude','latitude',
        'description','status',
        'mpg_merchant_id','mpg_api_key',
        'mpg_secret_key', 'seq_no','tax','service',
        'created_by', 'updated_by','open_at','close_at'
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

    public function getOpenAtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['open_at'])
        ->format('H:i');
    }

    public function getCloseAtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['close_at'])
        ->format('H:i');
    }

     /*
	 * Function: get data outlet all
	 * Param: 
	 *	$request	: 
	 */
    public static function get_outlet_all(){
        $result = DB::table('fboutlets')
                  ->select('fboutlets.*', 'hotels.name as hotel_name','msystems.system_value as city')
                  ->join('hotels','hotels.id','=','fboutlets.hotel_id')
                  ->join('msystems','msystems.system_cd','=','hotels.city')
                  ->where('msystems.system_type','=','city')
                  ->where('fboutlets.deleted_at','=',null)
                  ->orderBy('fboutlets.hotel_id','ASC')
                  ->orderBy('fboutlets.seq_no','ASC')                  
                  ->get();
        return $result;
    }
	
	/*
	 * Function: get data outlet all active
	 * Param: 
	 *	$request	: 
	 */
    public static function get_outlet_active(){
        $result = DB::table('fboutlets')
                  ->select('fboutlets.*', 'hotels.name as hotel_name','msystems.system_value as city')
                  ->join('hotels','hotels.id','=','fboutlets.hotel_id')
                  ->join('msystems','msystems.system_cd','=','hotels.city')
                  ->where('msystems.system_type','=','city')
                  ->where('fboutlets.deleted_at','=',null)
				  ->where('fboutlets.status', '1')
                  ->orderBy('fboutlets.hotel_id','ASC')
                  ->orderBy('fboutlets.seq_no','ASC')
                  ->get();
        return $result;
    }

    /*
	 * Function: get data outlet all
	 * Param: 
	 *	$request	: 
	 */
    public static function get_outlet_all_web(){
        $result = DB::table('fboutlets')
                  ->select('fboutlets.*', 'hotels.name as hotel_name','msystems.system_value as city')
                  ->join('hotels','hotels.id','=','fboutlets.hotel_id')
                  ->join('msystems','msystems.system_cd','=','hotels.city')
                  ->where('msystems.system_type','=','city')
                  ->where('hotels.status',1)
                  ->where('fboutlets.status',1)
                  ->where('fboutlets.deleted_at','=',null)
                  ->orderBy('fboutlets.hotel_id','ASC')
                  ->orderBy('fboutlets.seq_no','ASC')
                  ->get();
        return $result;
    }

    /*
	 * Function: get data outlet all with user
	 * Param: 
	 *	$request	: 
	 */
    public static function get_outlet_all_with_user($user_id){
        // dd($user_id);
        $result = DB::table('fboutlets')
                  ->select('fboutlets.*', 'hotels.name as hotel_name')->distinct()
                  ->join('hotels','hotels.id','=','fboutlets.hotel_id')
                  ->join('fboutlet_users', 'fboutlet_users.fboutlet_id', '=', 'fboutlets.id')
                  ->where('fboutlet_users.user_id', '=', $user_id)
                  ->where('fboutlets.status','=',1)
                  ->where('fboutlets.deleted_at','=',null)
                  ->where('fboutlet_users.deleted_at', '=', null)
                  ->orderBy('fboutlets.hotel_id','ASC')
                  ->orderBy('fboutlets.seq_no','ASC')
                  ->get();
        return $result;
    }
	
	/*
	 * Function: get data outlet active with user
	 * Param: 
	 *	$request	: 
	 */
    public static function get_outlet_active_with_user($user_id){
        // dd($user_id);
        $result = DB::table('fboutlets')
                  ->select('fboutlets.*', 'hotels.name as hotel_name')->distinct()
                  ->join('hotels','hotels.id','=','fboutlets.hotel_id')
                  ->join('fboutlet_users', 'fboutlet_users.fboutlet_id', '=', 'fboutlets.id')
                  ->where('fboutlet_users.user_id', '=', $user_id)
                  ->where('fboutlets.deleted_at','=',null)
				  ->where('fboutlets.status', '1')
                  ->orderBy('fboutlets.hotel_id','ASC')
                  ->orderBy('fboutlets.seq_no','ASC')
                  ->get();
        return $result;
    }

     /*
	 * Function: get data outlet berdasarkan outlet_id
	 * Param: 
	 *	$request	: 
	 */
    public static function get_outlet_detail($outlet_id){
        $result = DB::table('fboutlets')
                  ->select('fboutlets.*', 'hotels.name as hotel_name','msystems.system_value as city')
                  ->join('hotels','hotels.id','=','fboutlets.hotel_id')
                  ->join('msystems','msystems.system_cd','=','hotels.city')
                  ->where('msystems.system_type','=','city')
                  ->where('fboutlets.id','=',$outlet_id)
                  ->first();
        return $result;
    }

     /*
	 * Function: get data outlet berdasarkan hotel_id
	 * Param: 
	 *	$request	: 
	 */
    public static function get_hotel_outlet($hotel_id){
        $result = DB::table('fboutlets')
                  ->select('fboutlets.*', 'hotels.name as hotel_name')
                  ->join('hotels','hotels.id','=','fboutlets.hotel_id')
                  ->where('hotel_id','=',$hotel_id)
                  ->where('deleted_at','=',null)
                  ->orderBy('fboutlets.hotel_id','ASC')
                  ->orderBy('fboutlets.seq_no','ASC')
                  ->get();
        return $result;
    }

    /*
	 * Function: get data outlet berdasarkan hotel_id
	 * Param: 
	 *	$request	: 
	 */
    public static function get_hotel_outlet_with_user($request){
        $result = DB::table('fboutlets')
                  ->select('fboutlets.*', 'hotels.name as hotel_name')
                  ->join('hotels','hotels.id','=','fboutlets.hotel_id')
                  ->join('fboutlet_users', 'fboutlet_users.fboutlet_id', '=', 'fboutlets.id')
                  ->where('fboutlets.hotel_id','=', $request['hotel_id'])
                  ->where('fboutlet_users.user_id', '=', $request['user_id'])
                  ->where('fboutlets.status','=', 1)
                  ->where('fboutlets.deleted_at','=',null)
                  ->where('fboutlet_users.deleted_at', '=', null)
                  ->orderBy('fboutlets.hotel_id','ASC')
                  ->orderBy('fboutlets.seq_no','ASC')
                  ->get();
        return $result;
    }

    /*
	 * Function: add data outlet
	 * Param: 
	 *	$request	: id
	 */
    public static function add_outlet($request){
        $result= Outlet::create([
                                'hotel_id' => $request['hotel_id'],
                                'name' => $request['name'],
                                'address' => $request['address'],
                                'description' => $request['description'],
                                'status' => $request['status'],
                                'seq_no' => $request['seq_no'],
                                'mpg_merchant_id' => $request['mpg_merchant_id'],
                                'mpg_api_key' => $request['mpg_api_key'],
                                'mpg_secret_key' => $request['mpg_secret_key'],
                                'longitude' => $request['longitude'],
                                'latitude' => $request['latitude'],
                                'tax' => $request['tax'],
                                'service' => $request['service'],
                                'open_at' => $request['open_at'],
                                'close_at' => $request['close_at'],
                                'created_by' => $request['created_by'],
                                'updated_by' => $request['created_by']
                                ] );
        return $result;
    }

     /*
	 * Function: update Outlet
	 * Param: 
	 *	$request	: $data
	 */
    public static function update_outlet($request)
    {
        $result= Outlet::where('id', $request['id'])
                                    ->update( [
                                                'hotel_id' => $request['hotel_id'],
                                                'name' => $request['name'],
                                                'address' => $request['address'],
                                                'description' => $request['description'],
                                                'seq_no' => $request['seq_no'],
                                                'status' => $request['status'],
                                                'mpg_merchant_id' => $request['mpg_merchant_id'],
                                                'mpg_api_key' => $request['mpg_api_key'],
                                                'mpg_secret_key' => $request['mpg_secret_key'],
                                                'longitude' => $request['longitude'],
                                                'latitude' => $request['latitude'],   
                                                'tax' => $request['tax'],
                                                'service' => $request['service'], 
                                                'open_at' => $request['open_at'],
                                                'close_at' => $request['close_at'],
                                                'updated_by' => $request['updated_by']
                                             ] );
        return $result;
    }

    public function hotels()
    {
        return $this->hasMany(Hotel::class);
    }

     /*
	 * Function: get data outlet all
	 * Param: 
	 *	$request	: 
	 */
    public static function get_outlet_city($request){
        $result= DB::select(DB::raw("SELECT ot.id,ot.hotel_id,ot.name,ot.address,ot.description,ot.open_at,ot.close_at, ht.name as name_hotel,ms.system_value AS city 
                    FROM fboutlets ot
                    inner join hotels ht on (ht.id = ot.hotel_id)
                    INNER join msystems ms on (ms.system_type='city' and ms.system_cd=ht.city) 
                    WHERE ht.city = '{$request}' AND ht.status = 1 AND ot.status = 1 AND deleted_at IS NULL
                    ORDER BY ot.hotel_id ASC, ot.seq_no ASC"));
                    
        return $result;
    }
}
