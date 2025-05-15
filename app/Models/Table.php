<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'fboutlet_id', 'table_no','created_by','updated_by','deleted_flag'
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
    public function getUpdatedAtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['updated_at'])
        ->format('d, M Y H:i');
    }

    public static function get_table_all(){
        
        $result = Table::select('tables.*', 'hotels.name as name_hotel', 'fboutlets.name as name_outlet', 'fboutlets.id as fboutlets_id')->distinct()
                ->join('fboutlets','fboutlets.id','=','tables.fboutlet_id')
                ->join('hotels','hotels.id','=','fboutlets.hotel_id')
                ->where('fboutlets.deleted_at', '=', null)
                ->orderBy('tables.fboutlet_id','ASC')
                ->orderBy('fboutlets.id','ASC')
                // ->orderByRaw('CONVERT(tables.table_no, INT) ASC')
                ->orderByRaw('FLOOR(tables.table_no) ASC')
                ->get();
        return $result;
    }
    

    public static function get_table_user($request){
        if(!empty($request['hotel_id'])) {
                $result = Table::select('tables.*', 'hotels.name as name_hotel', 'fboutlets.name as name_outlet')
                        ->join('fboutlets','fboutlets.id','=','tables.fboutlet_id')
                        ->join('hotels','hotels.id','=','fboutlets.hotel_id')
                        ->join('fboutlet_users', 'fboutlet_users.fboutlet_id', '=', 'fboutlets.id')
                        ->where('fboutlet_users.user_id', '=', $request['user_id'])
                        ->where('hotels.id', '=', $request['hotel_id'])
                        ->where('fboutlets.deleted_at', '=', null)
                        ->where('fboutlet_users.deleted_at', '=', null)
                        ->orderBy('tables.fboutlet_id','ASC')
                        ->orderBy('tables.id','ASC')
                        ->get();
        } else {
                $result = Table::select('tables.*', 'hotels.name as name_hotel', 'fboutlets.name as name_outlet')
                        ->join('fboutlets','fboutlets.id','=','tables.fboutlet_id')
                        ->join('hotels','hotels.id','=','fboutlets.hotel_id')
                        ->join('fboutlet_users', 'fboutlet_users.fboutlet_id', '=', 'fboutlets.id')
                        ->where('fboutlet_users.user_id', '=', $request['user_id'])
                        ->where('fboutlets.deleted_at', '=', null)
                        ->where('fboutlet_users.deleted_at', '=', null)
                        ->orderBy('tables.fboutlet_id','ASC')
                        ->orderBy('tables.id','ASC')
                        ->get();
        }
        return $result;
    }

    
    public static function get_table_by_hotel($request){
        // dd($request['id_hotel']);
        $result = Table::select('tables.*', 'hotels.name as name_hotel', 'fboutlets.name as name_outlet')
                ->join('fboutlets','fboutlets.id','=','tables.fboutlet_id')
                ->join('hotels','hotels.id','=','fboutlets.hotel_id')
                ->where('fboutlets.hotel_id', $request['id_hotel'])
                ->where('fboutlets.deleted_at', '=', null)
                ->orderBy('tables.fboutlet_id','ASC')
                ->orderBy('tables.id','ASC')
                ->get();
        return $result;
    }

    public static function get_table_by_hotel_with_user($request){
        $result = Table::select('tables.*', 'hotels.name as name_hotel', 'fboutlets.name as name_outlet')
                ->join('fboutlets','fboutlets.id','=','tables.fboutlet_id')
                ->joing('fboutlet_users', 'fboutlet_users.fboutlet_id', '=', 'fboutlets.id')
                ->join('hotels','hotels.id','=','fboutlets.hotel_id')
                ->where('fboutlets.hotel_id', $request['id_hotel'])
                ->where('fboutlets.deleted_at', '=', null)
                ->orderBy('tables.fboutlet_id','ASC')
                ->orderBy('tables.id','ASC')
                ->get();
        return $result;
    }

    public static function get_table_by_outlet($request){
        // dd($request['id_hotel']);
        $result = Table::select('tables.*', 'hotels.name as name_hotel', 'fboutlets.name as name_outlet')
                ->join('fboutlets','fboutlets.id','=','tables.fboutlet_id')
                ->join('hotels','hotels.id','=','fboutlets.hotel_id')
                ->where('tables.fboutlet_id', $request['fboutlets_id'])
                ->where('fboutlets.deleted_at', '=', null)
                ->orderBy('tables.fboutlet_id','ASC')
                ->orderBy('tables.id','ASC')
                ->get();
        return $result;
    }
    

    public static function get_table_by_id($request){
        // dd($request['id_hotel']);
        $result = Table::select('tables.*', 'hotels.name as name_hotel', 'fboutlets.name as name_outlet')
                ->join('fboutlets','fboutlets.id','=','tables.fboutlet_id')
                ->join('hotels','hotels.id','=','fboutlets.hotel_id')
                ->where('tables.id', $request['id'])
                ->where('fboutlets.deleted_at', '=', null)
                ->orderBy('tables.fboutlet_id','ASC')
                ->orderBy('tables.id','ASC')
                ->get();
        return $result;
    }
    /*
	 * Function: add data promo
	 * Param: 
	 *	$request	: name,
	 */
    public static function add_table($request){
        $result= Table::create([
                                        'fboutlet_id' => $request['fboutlet_id'],
                                        'table_no' => $request['table_no'],
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
    public static function edit_table($request)
    {
        $result= Table::where('id', $request['id'])
                                    ->update( [
                                        'fboutlet_id' => $request['fboutlet_id'],
                                        'table_no' => $request['table_no'],
                                        'updated_by' => $request['updated_by'], 
                                             ] );
        return $result;
    }
}
