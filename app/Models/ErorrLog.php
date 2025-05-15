<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ErorrLog extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'error_log';
    protected $fillable = [
        'ip_address', 'username',
         'modul','actions','error_log',
         'error_date','device'
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

    public static function save_log($request){
        if(empty($request['username'])){
            $request['username'] = null;
        }
        $result = ErorrLog::create([
            'ip_address' => $request['ip_address'],
            'username'=> $request['username'],
            'modul' => $request['modul'],
            'actions' => $request['actions'],
            'error_log' => $request['error_log'],
            'error_date'=> date("Y-m-d H:i:s"),
            'device' => $request['device'],
                                    ]);
        return $result;
    }
}
