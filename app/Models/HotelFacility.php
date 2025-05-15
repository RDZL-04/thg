<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelFacility extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hotel_id','facility_id'
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
	 * Function: get data hotel_facility inner join facility sort by seq_no ASC
	 * Param: 
	 *	$request	: $request = id_hotel
	 */
    public static function get_facility($request){
        
        $result =HotelFacility::where('hotel_id', $request)
                ->join('facilities', 'facilities.id', '=', 'hotel_facilities.facility_id')
                ->select('facilities.id',
                        'facilities.name',
                        'facilities.icon',
                        'facilities.seq_no',
                        'hotel_facilities.created_at',
                        'hotel_facilities.updated_at'
                        )
                ->orderBy('seq_no','ASC')
                ->get();
        return $result;
    }
    public static function add_hotel_facility($request){
        $result =HotelFacility::updateOrCreate(['hotel_id' => $request['hotel_id'],
                                                'facility_id' => $request['facility_id']],
                                       ['hotel_id' => $request['hotel_id'],
                                        'facility_id' => $request['facility_id']]);
        return $result;
    }

    public static function delete_hotel_facility($request){
        $result =HotelFacility::where('hotel_id', $request['hotel_id'])
                                ->where('facility_id', $request['facility_id'])
                                ->delete();
        return $result;
    }

    public static function delete_hotel_facility_all($request){
        $result =HotelFacility::where('hotel_id', $request)
                                ->delete();
        return $result;
    }
}
