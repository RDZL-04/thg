<?php
/*
* Model Mice Category
* author : rangga.muharam@arkamaya.co.id
*	
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MiceCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mice_category';
    protected $fillable = [
        'hotel_id','category_id','descriptions',
        'created_by', 'updated_by', 'images'
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
	 * Function: add data Mice Category
	 * Param: category_id dari tabel msystems
	 *	$request	: 
	 */
    public static function add_mice_category($request){
        $result= MiceCategory::create([
                                'hotel_id' => $request['hotel_id'],
                                'category_id' => $request['category_id'],
                                'descriptions' => $request['description'],
                                'images' => $request['images'],
                                'created_by' => $request['created_by'],
                                'updated_by' => $request['created_by']
                                ] );
        return $result;
    }

     /*
	 * Function: update Mice Category
	 * Param: 
	 *	$request	: $data
	 */
    public static function update_mice_category($request)
    {
        $result= MiceCategory::where('id', $request['id'])
                                    ->update( [
                                                'hotel_id' => $request['hotel_id'],
                                                'category_id' => $request['category_id'],
                                                'descriptions' => $request['description'],
                                                'images' => $request['images'],
                                                'updated_by' => $request['updated_by']
                                             ] );
        return $result;
    }

    /*
	 * Function: get ALL Mice Category for ADMIN
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_all_mice()
    {
        $result = MiceCategory::select('mice_category.*', 'hotels.name as hotel_name','msystems.system_cd as category_id_msystems', 'msystems.system_value as category_name', 'hotels.id as hotel_id')->distinct()
                        ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                        // ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                        ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('msystems.system_type', '=', 'mice_category')
                        ->where('mice_category.deleted_at', '=', NULL)
                        // ->orderBy('mice_category.id','ASC')
                        ->orderBy('hotels.id','ASC')
                        ->get();
        return $result;
    }

    /*
	 * Function: get Mice Category with Hotel User
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_all_mice_with_hotel_user($user_id)
    {
        $result = MiceCategory::select('mice_category.*', 'hotels.name as hotel_name', 'msystems.system_cd as category_id_msystems', 'msystems.system_value as category_name', 'hotels.id as hotel_id')
                        ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                        ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                        ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('hotel_users.user_id', '=', $user_id)
                        ->where('msystems.system_type', '=', 'mice_category')
                        ->where('mice_category.deleted_at', '=', NULL)
                        // ->orderBy('mice_category.id','ASC')
                        ->orderBy('hotels.id','ASC')
                        ->get();
        return $result;
    }

    /*
	 * Function: get Mice Category Detail for ADMIN
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_mice_detail($request)
    {
        $result = MiceCategory::select('mice_category.*', 'hotels.name as hotel_name','msystems.system_cd as category_id_msystems', 'msystems.system_value as category_name', 'hotels.id as hotel_id')->distinct()
                        ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                        // ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                        ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('msystems.system_type', '=', 'mice_category')
                        ->where('mice_category.id', '=', $request['mice_category_id'])
                        ->where('mice_category.deleted_at', '=', NULL)
                        // ->orderBy('mice_category.id','ASC')
                        ->orderBy('hotels.id','ASC')
                        ->get();
        return $result;
    }

    /*
	 * Function: get Mice Category with Hotel User
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_mice_detail_with_hotel_user($request)
    {
        $result = MiceCategory::select('mice_category.*', 'hotels.name as hotel_name', 'msystems.system_cd as category_id_msystems', 'msystems.system_value as category_name', 'hotels.id as hotel_id')
                        ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                        ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                        ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('hotel_users.user_id', '=', $request['user_id'])
                        ->where('msystems.system_type', '=', 'mice_category')
                        ->where('mice_category.id', '=', $request['mice_category_id'])
                        // ->orderBy('mice_category.id','ASC')
                        ->orderBy('hotels.id','ASC')
                        ->get();
        return $result;
    }

    /*
	 * Function: get Mice Category with Hotel User
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_hotel_mice_msystem($hotel_id)
    {
        $result = MiceCategory::select('mice_category.id as mice_category_id', 'mice_category.category_id as category_id', 'msystems.system_value as category_name', 'hotels.id as hotel_id','hotels.name as hotel_name', 'hotels.id as hotel_id')
                        ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                        ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('msystems.system_type', '=', 'mice_category')
                        ->where('mice_category.hotel_id', '=', $hotel_id)
                        ->where('mice_category.deleted_at', '=', null)
                        // ->orderBy('mice_category.id','ASC')
                        ->orderBy('hotels.id','ASC')
                        ->get();
        return $result;
    }

    /*
	 * Function: get Mice Category with Hotel User
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_all_mice_category_hotel_user_filter($request)
    {
        if(!empty($request->user_id) || ($request->user_id) != null) {
            $result = MiceCategory::select('mice_category.*', 'hotels.name as hotel_name','msystems.system_cd as category_id_msystems', 'msystems.system_value as category_name', 'hotels.id as hotel_id')->distinct()
                        ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                        ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                        ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('msystems.system_type', '=', 'mice_category')
                        ->where('hotel_users.user_id', '=', $request['user_id'])
                        ->where('mice_category.hotel_id', '=', $request['hotel_id'])
                        ->where('mice_category.deleted_at', '=', NULL)
                        // ->orderBy('mice_category.id','ASC')
                        ->orderBy('hotels.id','ASC')
                        ->get();
        } else {
            $result = MiceCategory::select('mice_category.*', 'hotels.name as hotel_name','msystems.system_cd as category_id_msystems', 'msystems.system_value as category_name', 'hotels.id as hotel_id')->distinct()
                            ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                            // ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                            ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                            ->where('msystems.system_type', '=', 'mice_category')
                            ->where('mice_category.hotel_id', '=', $request['hotel_id'])
                            ->where('mice_category.deleted_at', '=', NULL)
                            // ->orderBy('mice_category.id','ASC')
                            ->orderBy('hotels.id','ASC')
                            ->get();
        }
        return $result;
    }

    /*
	 * Function: get Mice Category with Hotel ID dan Mice Category ID
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_mice_category_detail($request)
    {
        $result = MiceCategory::select('mice_category.*', 'hotels.name as hotel_name','msystems.system_cd as category_id_msystems', 'msystems.system_value as category_name', 'hotels.id as hotel_id')->distinct()
                        ->join('hotels','hotels.id', '=', 'mice_category.hotel_id')
                        // ->join('hotel_users', 'hotel_users.hotel_id', '=', 'hotels.id')
                        ->join('msystems', 'msystems.system_cd', '=', 'mice_category.category_id')
                        ->where('msystems.system_type', '=', 'mice_category')
                        ->where('mice_category.hotel_id', '=', $request['hotel_id'])
                        ->where('mice_category.category_id', '=', $request['category_id'])
                        ->where('mice_category.deleted_at', '=', NULL)
                        // ->orderBy('mice_category.id','ASC')
                        ->orderBy('hotels.id','ASC')
                        ->get();
        return $result;
    }
    

}
