<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Facility extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'icon', 'seq_no'
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

    public static function get_facility(){
        
        $result = Facility::select('facilities.id',
                                    'facilities.name',
                                    'facilities.icon',
                                    'facilities.seq_no',
                                    'hotel_facilities.hotel_id',
                                    'facilities.created_at',
                                    'facilities.updated_at'
        )
                ->Leftjoin('hotel_facilities', 'facilities.id', '=', 'hotel_facilities.facility_id')
                
                // ->whereNull('hotel_facilities.hotel_id')
                // ->orWhere('hotel_facilities.hotel_id', $request['id']) 
                // ->groupBy('facilities.id')
                // ->groupBy('facilities.name')
                ->get();
        
        return $result;
    }
    public static function get_facility_Byhotel($request){
        
        $result= DB::select(DB::raw("SELECT facilities.id, facilities.name, facilities.icon,facilities.seq_no, hotel_facilities.hotel_id, facilities.created_at, facilities.updated_at
                FROM facilities
                LEFT JOIN hotel_facilities
                ON facilities.id=hotel_facilities.facility_id 
                AND hotel_facilities.hotel_id = '{$request['id']}'
				ORDER BY facilities.seq_no
				"));
        return $result;
    }
    
    public static function add_facility($request){
        // dd($request);
        $result= Facility::create([
                                        'name' => $request['name'],
                                        'seq_no' => $request['seq_no'],
                                        'icon' => $request['icon'],
                                             ] );
        return $result;
    }

    

    /**
     * funcion sql for create or update facility
     */
    public static function update_facility($request)
    {
        $result= Facility::updateOrCreate(['id' => $request['id']],
                                             [
                                                'name' => $request['name'],
                                                'icon' => $request['icon'],
                                                'seq_no' => $request['seq_no'],
                                             ] );
        return $result;
    }
	
	//get last sequence
	public static function get_last_sequence()
	{
		$result = Facility::select(DB::raw('max(`seq_no`) as `seq_no`'))->first();
		return $result ? $result->seq_no : 0;
	}
}
