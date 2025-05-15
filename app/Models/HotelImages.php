<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/*
	 * Model HotelImage
	 * author : ilhammaulanpratama@arkamaya.co.id
	 *	
	 */
    

class HotelImages extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','hotel_id',
        'file_name','seq_no',
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

    
    public static function get_image($request){
        
        $result =HotelImages::where('hotel_id', $request)
                ->select('id',
                        'name',
                        'file_name',
                        'seq_no'
                        )
                ->orderBy('seq_no','ASC')
                ->get();
        return $result;
    }
    

    public static function get_image_by_seq($request){
        
        $result =HotelImages::where('hotel_id', $request)
                ->where('seq_no', 1)
                ->select(
                        'file_name'
                        )
                ->first();
        return $result;
    }
    public static function add_image($request){
        // dd($request['hotel_id']);
        $result= HotelImages::create([
                                        'name' => $request['name'],
                                        'hotel_id' => $request['hotel_id'],
                                        'file_name' => $request['file_name'],
                                        'seq_no' => $request['seq_no'],
                                             ] );
        return $result;
    }

    public static function update_image($request)
    {
        
        $result= HotelImages::updateOrCreate(['id' => $request['id']],
                                             [
                                                'name' => $request['name'],
                                                'hotel_id' => $request['hotel_id'],
                                                'file_name' => $request['file_name'],
                                                'seq_no' => $request['seq_no'],
                                             ] );
        return $result;
    }
	
	//get last sequence
	public static function get_last_sequence($hotelId = null)
	{
		$result = HotelImages::select(DB::raw('max(`seq_no`) as `seq_no`'))->where('hotel_id', $hotelId)->first();
		return $result ? $result->seq_no : 0;
	}
}
