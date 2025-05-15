<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Outlet;

/*
	 * Model Hotel
	 * author : ilhammaulanpratama@arkamaya.co.id
	 *	
	 */

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','address',
        'longitude','latitude',
        'description','be_hotel_id',
        'hotel_star','status',
        'mpg_merchant_id','mpg_api_key',
        'mpg_secret_key','be_api_key','be_secret_key',
        'created_by','updated_by','city','email_notification', 'mice_email', 'mice_wa'
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
	 * Function: get data all hotel
	 * Param: 
	 *	$request	: 
	 */
    public static function get_hotel_all(){
        $result =Hotel::orderBy('hotel_star','DESC')
                ->select('hotels.*',
                        'msystems.system_value as city'
                                )
                ->join('msystems','msystems.system_cd','=','hotels.city')
                ->where('msystems.system_type','=','city')
                ->orderBy('hotel_star','DESC')
        ->get();
        return $result;
    }
	
	/*
	 * Function: get data all hotel active
	 * Param: 
	 *	$request	: 
	 */
    public static function get_hotel_active(){
        $result =Hotel::orderBy('hotel_star','DESC')
                ->select('hotels.*',
                        'msystems.system_value as city'
                                )
                ->join('msystems','msystems.system_cd','=','hotels.city')
                ->where('msystems.system_type','=','city')
				->where('hotels.status', '1')
                ->orderBy('hotel_star','DESC')
        ->get();
        return $result;
    }
	
    /*
	 * Function: get data all hotel web app
	 * Param: 
	 *	$request	: 
	 */
    public static function get_hotel_all_web_app(){
        $result =Hotel::orderBy('hotel_star','DESC')
                ->select('hotels.*',
                        'msystems.system_value as city'
                                )
                ->join('msystems','msystems.system_cd','=','hotels.city')
                ->where('msystems.system_type','=','city')
                ->where('hotels.status',1)
                ->orderBy('hotel_star','DESC')
        ->get();
        return $result;
    }

     /*
	 * Function: get data all hotel mice web app
	 * Param: 
	 *	$request	: 
	 */
    public static function get_mice_hotel_all_web_app(){
        $result =Hotel::orderBy('hotels.hotel_star','DESC')
                ->select('hotels.id','hotels.name', 'hotels.address','hotels.latitude','hotels.longitude', 'hotels.hotel_star',
                        'mice_category.images as hotel_images', 'mice_category.category_id as category_id', 'mice_category.id as mice_category_id',
                        'msystems.system_value as city'
                                )->distinct()
                ->join('msystems','msystems.system_cd','=','hotels.city')
                ->join('mice_category','mice_category.hotel_id','=','hotels.id')
                ->where('msystems.system_type','=','city')
                ->where('mice_category.deleted_at','=', null)
                ->where('hotels.status',1)
                ->orderBy('hotels.hotel_star','DESC')
                ->orderBy('mice_category.category_id','ASC')
        ->get();
        return $result;
    }

        /*
	 * Function: search data hotel by name
	 * Param: 
	 *	$request	: name,page
	 */
    public static function get_hotel_mice_by_city($request){
        $result =Hotel::where('hotels.city',$request['city'])
                        ->where('hotels.status', 1)
                        ->select('hotels.id',
                                'hotels.name',
                                'hotels.address','hotels.latitude','hotels.longitude',
                                'mice_category.images as hotel_images', 
                                'mice_category.category_id as category_id', 
                                'mice_category.id as mice_category_id',
                                'msystems.system_value as city'
                                )
                        ->join('msystems','msystems.system_cd','=','hotels.city')
                        ->join('mice_category','mice_category.hotel_id','=','hotels.id')
                        ->where('msystems.system_type','=','city')
                        ->where('mice_category.deleted_at','=', null)
                        ->orderBy('mice_category.category_id','ASC')
                        ->get();
    return $result;
}

    /*
	 * Function: get data all hotel based from user outlet
	 * Param: 
	 *	$request	: 
	 */
    public static function get_hotel_all_with_user_outlet($user_id){
        $result = Hotel::select('hotels.*')->distinct()
                        ->join('fboutlets','fboutlets.hotel_id', '=', 'hotels.id')
                        ->join('fboutlet_users', 'fboutlet_users.fboutlet_id', '=', 'fboutlets.id')
                        ->where('fboutlet_users.deleted_at', '=', null)
                        ->where('fboutlet_users.user_id', '=', $user_id)
                        ->orderBy('hotel_star','DESC')
                        ->get();
        return $result;
    }
	
	/*
	 * Function: get data active hotel based from user outlet
	 * Param: 
	 *	$request	: 
	 */
    public static function get_hotel_active_with_user_outlet($user_id){
        $result = Hotel::select('hotels.*')->distinct()
                        ->join('fboutlets','fboutlets.hotel_id', '=', 'hotels.id')
                        ->join('fboutlet_users', 'fboutlet_users.fboutlet_id', '=', 'fboutlets.id')
                        ->where('fboutlet_users.user_id', '=', $user_id)
						->where('hotels.status', '1')
                        ->orderBy('hotel_star','DESC')
                        ->get();
        return $result;
    }

    /*
	 * Function: search data hotel by name
	 * Param: 
	 *	$request	: name
	 */
    public static function search_hotel($name){
        $result =Hotel::where('name', 'like', '%' . $name . '%')
                ->where('status',1)
                ->select('id',
                        'name',
                        'address',
                        'be_hotel_id',
                        'longitude',
                        'latitude',
                        'hotel_star',
                        'description'
                        )
                ->get();
        return $result;
    }

        /*
	 * Function: search data hotel by name
	 * Param: 
	 *	$request	: name,page
	 */
    public static function get_hotel($request){
            // $result =Hotel::where('hotels.id', 'like', '%' . $request['id'] . '%')
            $result =Hotel::where('hotels.name', 'like', '%' . $request['name'] . '%')
                ->where('hotels.status', 1)
                ->select('hotels.id',
                        'hotels.name',
                        'be_hotel_id',
                        'address',
                        'description',
                        'longitude',
                        'latitude',
                        'hotel_star',
                        'msystems.system_value as city'
                        )
                ->join('msystems','msystems.system_cd','=','hotels.city')
                ->where('msystems.system_type','=','city')
                ->orderBy('hotel_star','DESC')
                ->paginate(10);
        return $result;
    }

        /*
	 * Function: search data hotel by name
	 * Param: 
	 *	$request	: name,page
	 */
    public static function get_hotel_by_city($request){
        $result =Hotel::where('hotels.city',$request['city'])
                        ->where('hotels.status', 1)
                        ->select('hotels.id',
                                'hotels.name',
                                'be_hotel_id',
                                'address',
                                'description',
                                'longitude',
                                'latitude',
                                'hotel_star',
                                'msystems.system_value as city'
                                )
                        ->join('msystems','msystems.system_cd','=','hotels.city')
                        ->where('msystems.system_type','=','city')
                        ->orderBy('hotel_star','DESC')
                        ->get();
    return $result;
}

    public static function get_id_hotel(){
        $result =Hotel::select('id')
                    ->get();
            // ->LIMIT =  $request['limit'];
    return $result;
}

    /*
	 * Function: search data hotel by name and id
	 * Param: 
	 *	$request	: name,id,page
	 */
    public static function get_hotel_id($request){
        $result =Hotel::where('id', '=',$request['id'])
            ->where('name', 'like', '%' . $request['name'] . '%')
            
            ->paginate(10);
    return $result;
    }

    /*
	 * Function: search data hotel by user id
	 * Param: 
	 *	$request	: user_id
	 */
    public static function get_hotel_user_id($request){
        $result =Hotel::where('hotel_users.user_id', $request)
                    ->select('hotels.*')
                    ->join('hotel_users','hotel_users.hotel_id','=','hotels.id')
                    ->orderBy('id','ASC')
                    ->get();
        return $result;
    }

    /*
	 * Function: add data hotel
	 * Param: 
	 *	$request	: name,id
	 */
    public static function add_hotel($request){
        if(empty($request['mpg_merchant_id'])){
            $request['mpg_merchant_id'] = null;
        }
        $result= Hotel::create([
                                        'name' => $request['name'],
                                        'address' => $request['address'],
                                        'description' => $request['description'],
                                        'be_hotel_id' => $request['be_hotel_id'],
                                        'be_api_key' => $request['be_api_key'],
                                        'be_secret_key' => $request['be_secreet_key'],
                                        'hotel_star' => $request['hotel_star'],
                                        'status' => $request['status'],
                                        'mpg_merchant_id' => $request['mpg_merchant_id'],
                                        'mpg_api_key' => $request['mpg_api_key'],
                                        'mpg_secret_key' => $request['mpg_secreet_key'],
                                        'longitude' => $request['longitude'],
                                        'latitude' => $request['latitude'],
                                        'created_by' => $request['created_by'],
                                        'updated_by' =>  $request['created_by'], 
                                        'city' =>  $request['city'],
                                        'email_notification' =>  $request['email_notification'],                                         
                                        'mice_email' =>  $request['mice_email'],                                         
                                        'mice_wa' =>  $request['mice_wa']                                         
                                             ] );
        return $result;
    }

    /*
	 * Function: update Hotel
	 * Param: 
	 *	$request	: $data
	 */
    public static function update_hotel($request)
    {
        if(empty($request['mpg_merchant_id'])){
            $request['mpg_merchant_id'] = null;
        }
        $result= Hotel::where('id', $request['id'])
                                    ->update( [
                                                'name' => $request['name'],
                                                'address' => $request['address'],
                                                'description' => $request['description'],
                                                'be_hotel_id' => $request['be_hotel_id'],
                                                'be_api_key' => $request['be_api_key'],
                                                'be_secret_key' => $request['be_secreet_key'],
                                                'hotel_star' => $request['hotel_star'],
                                                'status' => $request['status'],
                                                'mpg_merchant_id' => $request['mpg_merchant_id'],
                                                'mpg_api_key' => $request['mpg_api_key'],
                                                'mpg_secret_key' => $request['mpg_secreet_key'],
                                                'longitude' => $request['longitude'],
                                                'latitude' => $request['latitude'], 
                                                 'city' =>  $request['city'], 
                                                'updated_by' => $request['updated_by'], 
                                                'email_notification' =>  $request['email_notification'],
                                                'mice_email' =>  $request['mice_email'],
                                                'mice_wa' =>  $request['mice_wa']
                                             ] );
		
		Hotel::set_outlet_status($request['id'], $request['status']);
		
        return $result;
    }

    /*
	 * Function: get ALL Mice Category for ADMIN
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_hotel_mice()
    {
        $result = Hotel::select('hotels.id as id', 'hotels.name as hotel_name')->distinct()
                        ->join('mice_category','mice_category.hotel_id', '=', 'hotels.id')
                        // ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                        ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('msystems.system_type', '=', 'mice_category')
                        ->orderBy('hotels.id','ASC')
                        ->get();
        return $result;
    }

    /*
	 * Function: get Mice Category with Hotel User
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_hotel_mice_with_hotel_user($user_id)
    {
        $result = Hotel::select('hotels.id', 'hotels.name as hotel_name')->distinct()
                        ->join('mice_category','mice_category.hotel_id', '=', 'hotels.id')
                        ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                        ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('hotel_users.user_id', '=', $user_id)
                        ->where('msystems.system_type', '=', 'mice_category')
                        ->orderBy('hotels.id','ASC')
                        ->get();
        return $result;
    }

	/*
	 * Function: Set active or no-active outlet status
	 * Param: 
	 *	$request	: $data
	 */
	public static function set_outlet_status($hotel_id = '', $status = '1')
	{
		$data = array('status' => $status);
		
		Outlet::where('hotel_id', $hotel_id)->update($data);
	}
}
