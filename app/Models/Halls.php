<?php
/*
* Model Hall
* author : rangga.muharam@arkamaya.co.id
*	
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Halls extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mice_halls';
    protected $fillable = [
        'name','descriptions','capacity',
        'size','layout', 'seq', 'mice_offers',
        'created_by', 'updated_by'
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
	 * Function: add data Halls
	 * Param: 
	 *	$request	:
	 */
    public static function add_hall($request){
        if( (!empty($request['mice_offers'])) && ($request['mice_offers'] != null) ) {
            $result = Halls::create([
                                'name' => $request['name'],
                                'descriptions' => $request['description'],
                                'capacity' => $request['capacity'],
                                'size' => $request['size'],
                                'seq' => $request['seq'],
                                'layout' => $request['layout'],
                                'mice_offers' => $request['mice_offers'],
                                'created_by' => $request['created_by'],
                                'updated_by' => $request['created_by']
                            ]);
        } else {
            $result = Halls::create([
                                'name' => $request['name'],
                                'descriptions' => $request['description'],
                                'capacity' => $request['capacity'],
                                'size' => $request['size'],
                                'seq' => $request['seq'],
                                'layout' => $request['layout'],
                                'created_by' => $request['created_by'],
                                'updated_by' => $request['created_by']
                              ]);
        }
        return $result;
    }

     /*
	 * Function: update Halls
	 * Param: 
	 *	$request	: $data
	 */
    public static function update_hall($request)
    {
        if( (!empty($request['mice_offers'])) && ($request['mice_offers'] != null) ) {
            $result = Halls::where('id', $request['id'])
                                        ->update( [
                                            'name' => $request['name'],
                                            'descriptions' => $request['description'],
                                            'capacity' => $request['capacity'],
                                            'size' => $request['size'],
                                            'seq' => $request['seq'],
                                            'layout' => $request['layout'],
                                            'mice_offers' => $request['mice_offers'],
                                            'updated_by' => $request['updated_by']
                                        ]);
        } else {
            $result = Halls::where('id', $request['id'])
                                        ->update( [
                                            'name' => $request['name'],
                                            'descriptions' => $request['description'],
                                            'capacity' => $request['capacity'],
                                            'size' => $request['size'],
                                            'seq' => $request['seq'],
                                            'layout' => $request['layout'],
                                            'mice_offers' => null,
                                            'updated_by' => $request['updated_by'],
                                        ]);
        }
        return $result;
    }

    /*
	 * Function: get ALL Hall dan Hall Category for ADMIN
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_all_hall()
    {
        $result = Halls::select('mice_halls.*', 'hotels.name as hotel_name','hotels.mice_wa', 'hotels.mice_email','msystems.system_cd as category_id_msystems', 'msystems.system_value as category_name')->distinct()
                        ->join('hall_category', 'hall_category.hall_id', '=', 'mice_halls.id')
                        ->join('mice_category', 'mice_category.id','=', 'hall_category.mice_category_id')
                        ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                        // ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                        ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('msystems.system_type', '=', 'mice_category')
                        ->where('hall_category.deleted_at', '=', NULL)
                        // ->orderBy('mice_category.id','ASC')
                        ->orderBy('mice_halls.seq', 'ASC')
                        ->get();
        return $result;
    }

    /*
	 * Function: get ALL Hall dan Hall Category for ADMIN with Hotel ID
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_hall($request)
    {
        $result = Halls::select('mice_halls.*', 'hotels.name as hotel_name','hotels.mice_wa', 'hotels.mice_email','msystems.system_cd as category_id_msystems', 'msystems.system_value as category_name')->distinct()
                        ->join('hall_category', 'hall_category.hall_id', '=', 'mice_halls.id')
                        ->join('mice_category', 'mice_category.id','=', 'hall_category.mice_category_id')
                        ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                        // ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                        ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('msystems.system_type', '=', 'mice_category')
                        ->where('hotels.id', '=', $request['hotel_id'])
                        ->where('hall_category.deleted_at', '=', NULL)
                        ->orderBy('mice_halls.seq','ASC')
                        ->get();
        return $result;
    }

    /*
	 * Function: get ALL Mice Category from mysytem with Existing Hall for ADMIN
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_mice_category_with_all_hall()
    {
        $result = Halls::select('mice_halls.id','msystems.system_cd as category_id_msystems', 'msystems.system_value as category_name', 'mice_category.id as mice_category_id')->distinct()
                        ->join('hall_category', 'hall_category.hall_id', '=', 'mice_halls.id')
                        ->join('mice_category', 'mice_category.id','=', 'hall_category.mice_category_id')
                        ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                        // ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                        ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('msystems.system_type', '=', 'mice_category')
                        ->where('hall_category.deleted_at', '=', NULL)
                        ->orderBy('mice_category.id','ASC')
                        ->get();
        return $result;
    }

    /*
	 * Function: get ALL Mice Category from mysytem with Existing Hall for ADMIN with Hotel ID
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_mice_category_with_hall_hotel_id($request)
    {
        $result = Halls::select('mice_halls.id','msystems.system_cd as category_id_msystems', 'msystems.system_value as category_name', 'mice_category.id as mice_category_id')->distinct()
                        ->join('hall_category', 'hall_category.hall_id', '=', 'mice_halls.id')
                        ->join('mice_category', 'mice_category.id','=', 'hall_category.mice_category_id')
                        ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                        // ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                        ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('msystems.system_type', '=', 'mice_category')
                        ->where('hotels.id', '=', $request['hotel_id'])
                        ->where('hall_category.deleted_at', '=', NULL)
                        ->orderBy('mice_category.id','ASC')
                        ->get();
        return $result;
    }


    
    /*
	 * Function: get ALL Hall With User Hotel
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_all_hall_with_hotel_user($user_id)
    {
        $result = Halls::select('mice_halls.*', 'hotels.name as hotel_name','hotels.mice_wa', 'hotels.mice_email','msystems.system_cd as category_id_msystems', 'msystems.system_value as category_name')->distinct()
                        ->join('hall_category', 'hall_category.hall_id', '=', 'mice_halls.id')
                        ->join('mice_category', 'mice_category.id','=', 'hall_category.mice_category_id')
                        ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                        ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                        ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('hotel_users.user_id', '=', $user_id)
                        ->where('msystems.system_type', '=', 'mice_category')
                        ->where('hall_category.deleted_at', '=', NULL)
                        ->orderBy('mice_halls.seq','ASC')
                        ->get();
        return $result;
    }

    /*
	 * Function: get Hall ID With User Hotel
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_hall_with_hotel_user($request)
    {
        $result = Halls::select('mice_halls.*', 'hotels.name as hotel_name','hotels.mice_wa', 'hotels.mice_email','msystems.system_cd as category_id_msystems', 'msystems.system_value as category_name')->distinct()
                        ->join('hall_category', 'hall_category.hall_id', '=', 'mice_halls.id')
                        ->join('mice_category', 'mice_category.id','=', 'hall_category.mice_category_id')
                        ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                        ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                        ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('hotel_users.user_id', '=', $request['user_id'])
                        ->where('msystems.system_type', '=', 'mice_category')
                        ->where('hotels.id', '=', $request['hotel_id'])
                        ->where('hall_category.deleted_at', '=', NULL)
                        ->orderBy('mice_halls.seq','ASC')
                        ->get();
        return $result;
    }

    /*
	 * Function: get ALL Mice Category from mysytem with Existing Hall for User Hotel
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_mice_category_with_hotel_user($user_id)
    {
        $result = Halls::select('mice_halls.id','msystems.system_cd as category_id_msystems', 'msystems.system_value as category_name','hotel_users.user_id', 'hotels.mice_wa', 'hotels.mice_email', 'mice_category.id as mice_category_id')->distinct()
                        ->join('hall_category', 'hall_category.hall_id', '=', 'mice_halls.id')
                        ->join('mice_category', 'mice_category.id','=', 'hall_category.mice_category_id')
                        ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                        ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                        ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('hotel_users.user_id', '=', $user_id)
                        ->where('msystems.system_type', '=', 'mice_category')
                        ->where('hall_category.deleted_at', '=', NULL)
                        ->orderBy('mice_category.id','ASC')
                        ->get();
        return $result;
    }

     /*
	 * Function: get ALL Mice Category from mysytem with Existing Hall for User Hotel and Hotel ID
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_mice_category_with_hotel_user_hotel_id($request)
    {
        $result = Halls::select('mice_halls.id','msystems.system_cd as category_id_msystems', 'msystems.system_value as category_name','hotel_users.user_id','hotels.mice_wa', 'hotels.mice_email', 'mice_category.id as mice_category_id')->distinct()
                        ->join('hall_category', 'hall_category.hall_id', '=', 'mice_halls.id')
                        ->join('mice_category', 'mice_category.id','=', 'hall_category.mice_category_id')
                        ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                        ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                        ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('hotel_users.user_id', '=', $request['user_id'])
                        ->where('hotels.id', '=', $request['hotel_id'])
                        ->where('msystems.system_type', '=', 'mice_category')
                        ->where('hall_category.deleted_at', '=', NULL)
                        ->orderBy('mice_category.id','ASC')
                        ->get();
        return $result;
    }

    /*
	 * Function: get Hall Detail based from Hall ID
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_hall_detail($hall_id)
    {
        $result = Halls::select('mice_halls.*', 'hotels.id as hotel_id', 'hotels.name as hotel_name', 'hotels.mice_wa', 'hotels.mice_email')->distinct()
                        ->join('hall_category', 'hall_category.hall_id', '=', 'mice_halls.id')
                        ->join('mice_category', 'mice_category.id','=', 'hall_category.mice_category_id')
                        ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                        // ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                        ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('mice_halls.id', '=', $hall_id)
                        ->where('msystems.system_type', '=', 'mice_category')
                        ->where('hall_category.deleted_at', '=', NULL)
                        ->orderBy('mice_halls.seq','ASC')
                        ->get();
        return $result;
    }

    /*
	 * Function: get Hall Category
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_hall_category($hall_id)
    {
        $result = Halls::select('mice_halls.id', 'mice_category.id as mice_category_id','msystems.system_cd as category_id_msystems', 'msystems.system_value as category_name', 'mice_halls.seq as hall_seq')->distinct()
                        ->join('hall_category', 'hall_category.hall_id', '=', 'mice_halls.id')
                        ->join('mice_category', 'mice_category.id','=', 'hall_category.mice_category_id')
                        ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                        // ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                        ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('mice_halls.id', '=', $hall_id)
                        ->where('msystems.system_type', '=', 'mice_category')
                        ->where('hall_category.deleted_at', '=', NULL)
                        ->orderBy('mice_halls.seq','ASC')
                        ->get();
        return $result;
    }

    /*
	 * Function: get ALL Hall Data from Hotel ID
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_all_hotel_hall($request)
    {
        // $result = Halls::select('mice_halls.*', 'hotels.name as hotel_name','msystems.system_cd as category_id_msystems', 'msystems.system_value as category_name')->distinct()
        $result = Halls::select('mice_halls.*', 'hotels.name as hotel_name', 'hotels.address as hotel_address', 'hotels.mice_wa', 'hotels.mice_email')->distinct()
                        ->join('hall_category', 'hall_category.hall_id', '=', 'mice_halls.id')
                        ->join('mice_category', 'mice_category.id','=', 'hall_category.mice_category_id')
                        ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                        // ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                        // ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('mice_category.category_id', '=', $request['category_id'])
                        ->where('hall_category.deleted_at', '=', NULL)
                        ->where('hotels.id', '=', $request['hotel_id'])
                        ->orderBy('mice_halls.seq','ASC')
                        ->get();
        return $result;
    }

    /*
	 * Function: get ALL Hall Data Capacity
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_all_hall_capacity($request)
    {
        if(empty($request['hotel_id']) || ($request['hotel_id'] == null) ){
            // $result = Halls::select('mice_halls.*', 'hotels.name as hotel_name', 'hotels.address as hotel_address')->distinct()
            $result = Halls::select('mice_halls.id', 'mice_halls.name', 'mice_halls.capacity', 'mice_halls.size', 'mice_halls.seq', 'hotels.id as hotel_id', 'hotels.name as hotel_name', 'hotels.mice_wa', 'hotels.mice_email')->distinct()
                            ->join('hall_category', 'hall_category.hall_id', '=', 'mice_halls.id')
                            ->join('mice_category', 'mice_category.id','=', 'hall_category.mice_category_id')
                            ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                            ->where('mice_halls.deleted_at', '=', null)
                            ->where('hall_category.deleted_at', '=', NULL)
                            ->orderBy('mice_halls.seq','ASC')
                            ->get();
        } else {
            // $result = Halls::select('mice_halls.*', 'hotels.name as hotel_name', 'hotels.address as hotel_address')->distinct()
            $result = Halls::select('mice_halls.id', 'mice_halls.name', 'mice_halls.capacity', 'mice_halls.size', 'mice_halls.seq', 'hotels.id as hotel_id', 'hotels.name as hotel_name','hotels.mice_wa', 'hotels.mice_email')->distinct()
                            ->join('hall_category', 'hall_category.hall_id', '=', 'mice_halls.id')
                            ->join('mice_category', 'mice_category.id','=', 'hall_category.mice_category_id')
                            ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                            ->where('hotels.id', '=', $request['hotel_id'])
                            ->where('mice_halls.deleted_at', '=', null)
                            ->where('hall_category.deleted_at', '=', NULL)
                            ->orderBy('mice_halls.seq','ASC')
                            ->get();
        }
        return $result;
    }

    /*
	 * Function: get search Hall Data By Capacity
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_search_hall_capacity($request)
    {
        if(empty($request['hotel_id']) || ($request['hotel_id'] == null) ){
            // $result = Halls::select('mice_halls.*', 'hotels.name as hotel_name', 'hotels.address as hotel_address')->distinct()
            $result = Halls::select('mice_halls.id', 'mice_halls.name', 'mice_halls.capacity', 'mice_halls.size', 'mice_halls.seq','hotels.id as hotel_id', 'hotels.name as hotel_name','hotels.mice_wa', 'hotels.mice_email')->distinct()
                            ->join('hall_category', 'hall_category.hall_id', '=', 'mice_halls.id')
                            ->join('mice_category', 'mice_category.id','=', 'hall_category.mice_category_id')
                            ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                            ->where('mice_halls.deleted_at', '=', null)
                            ->where('hall_category.deleted_at', '=', NULL)
                            ->where('mice_halls.capacity', '>=', $request['capacity'])
                            ->orderBy('mice_halls.seq','ASC')
                            ->get();
        } else {
            // $result = Halls::select('mice_halls.*', 'hotels.name as hotel_name', 'hotels.address as hotel_address')->distinct()
            $result = Halls::select('mice_halls.id', 'mice_halls.name', 'mice_halls.capacity', 'mice_halls.size', 'mice_halls.seq', 'hotels.id as hotel_id', 'hotels.name as hotel_name','hotels.mice_wa', 'hotels.mice_email')->distinct()
                            ->join('hall_category', 'hall_category.hall_id', '=', 'mice_halls.id')
                            ->join('mice_category', 'mice_category.id','=', 'hall_category.mice_category_id')
                            ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                            ->where('mice_halls.deleted_at', '=', null)
                            ->where('hall_category.deleted_at', '=', NULL)
                            ->where('mice_halls.capacity', '>=', $request['capacity'])
                            ->where('hotels.id', '=', $request['hotel_id'])
                            ->orderBy('mice_halls.seq','ASC')
                            ->get();
        }
        return $result;
    }

    /*
	 * Function: get Mice Category Data with Hall Data from Hotel ID
	 * Param: 
	 *	$request	: $data
	*/
    public static function get_category_hotel($request)
    {
        $result = Halls::select('mice_halls.id', 'mice_halls.name', 'hotels.id as hotel_id', 'hotels.name as hotel_name', 'mice_category.category_id')//->distinct()
                        ->join('hall_category', 'hall_category.hall_id', '=', 'mice_halls.id')
                        ->join('mice_category', 'mice_category.id', '=', 'hall_category.mice_category_id')
                        ->join('hotels', 'hotels.id' , '=', 'mice_category.hotel_id')
                        ->where('mice_halls.deleted_at', '=', null)
                        ->where('hall_category.deleted_at', '=', NULL)
                        ->where('mice_category.hotel_id', '=', $request['hotel_id'])
                        ->orderBy('mice_category.category_id','ASC')
                        ->get();
        return $result;
    }

}
