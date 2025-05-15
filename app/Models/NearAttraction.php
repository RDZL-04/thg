<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class NearAttraction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'hotel_id','attraction_nm',
        'category_id','distance',
        'created_at','updated_at',
        'created_by','updated_by',
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

    public static function get_near_hotels($request){
        // dd($request['id']);
        
        $result= DB::select(DB::raw("SELECT na.id,na.hotel_id,na.attraction_nm,ms.system_value as attr_category,na.distance 
        FROM near_attractions na
        inner join msystems ms on (ms.system_type='near_attr_cat' and ms.system_cd=na.category_id) WHERE na.hotel_id = '{$request}' AND na.deleted_at IS NULL"));
        // echo $request['id'];
        // dd($result);
        return $result;
    }

    public static function get_near($request){
        $result= DB::select(DB::raw("SELECT na.id,na.hotel_id,na.attraction_nm,ms.system_value as attr_category,na.distance 
        FROM near_attractions na
        inner join msystems ms on (ms.system_type='near_attr_cat' and ms.system_cd=na.category_id) WHERE na.hotel_id = '{$request['id_hotel']}' AND na.deleted_at IS NULL"));
        return $result;
    }

    public static function get_near_radius($request){
        $result= DB::select(DB::raw("SELECT na.id,na.hotel_id,na.attraction_nm,ms.system_value as attr_category,na.distance 
        FROM near_attractions na
        inner join msystems ms on (ms.system_type='near_attr_cat' and ms.system_cd=na.category_id) WHERE na.hotel_id = '{$request['id_hotel']}' AND na.distance <= {$request['radius']} AND na.deleted_at IS NULL"));
        return $result;
    }

    public static function get_near_by_hotels($request){
        $result= DB::select(DB::raw("SELECT na.id,na.hotel_id,na.attraction_nm,ms.system_value as attr_category,na.distance, na.created_by, na.updated_by, na.created_at, na.updated_at 
        FROM near_attractions na
        inner join msystems ms on (ms.system_type='near_attr_cat' and ms.system_cd=na.category_id) 
        WHERE na.hotel_id = '{$request['id_hotel']}' AND na.deleted_at IS NULL
        ORDER BY na.category_id ASC"));
        return $result;
    }

    /*
	 * Function: Add data msystem
	 * Param: data system
	 *	$request	: 
	 */
    public static function save_near_attraction($request){
        if(!empty($request['id'])){
            $result= NearAttraction::where('id', $request['id'])
                                        ->update( [
                                            'hotel_id' => $request['hotel_id'],
                                            'attraction_nm' => $request['attraction_nm'],
                                            'category_id' => $request['category_id'],
                                            'distance' => $request['distance'],
                                            'updated_by' => $request['created_by'],
                                                                ] );
        }else{
            $result= NearAttraction::create([
                'hotel_id' => $request['hotel_id'],
                'attraction_nm' => $request['attraction_nm'],
                'category_id' => $request['category_id'],
                'distance' => $request['distance'],
                'created_by' => $request['created_by'],
                'updated_by' => $request['created_by'],
                                     ] );
        }
        
        return $result;
    }
}
