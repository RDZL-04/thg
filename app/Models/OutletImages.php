<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutletImages extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'fboutlet_images';
    protected $fillable = [
        'name','fboutlet_id',
        'filename','seq_no',
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
        
        $result =OutletImages::where('fboutlet_id', $request)
                ->select('id',
                        'name',
                        'filename',
                        'seq_no'
                        )
                ->orderBy('seq_no','ASC')
                ->get();
        return $result;
    }
    

    public static function get_image_outlet($request){
        
        $result =OutletImages::where('fboutlet_id', $request)
                ->select('id',
                        'name',
                        'filename',
                        'seq_no'
                        )
                ->orderBy('seq_no','ASC')
                ->first();
        return $result;
    }

    public static function add_image($request){
        $result= OutletImages::create([
                                        'name' => $request['name'],
                                        'fboutlet_id' => $request['fboutlet_id'],
                                        'filename' => $request['filename'],
                                        'seq_no' => $request['seq_no'],
                                             ] );
        return $result;
    }

    public static function update_image($request)
    {
        
        $result= OutletImages::updateOrCreate(['id' => $request['id']],
                                             [
                                                'name' => $request['name'],
                                                'fboutlet_id' => $request['fboutlet_id'],
                                                'filename' => $request['filename'],
                                                'seq_no' => $request['seq_no'],
                                             ] );
        return $result;
    }
}
