<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guest extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "full_name","phone",
        "email","address","city",
        "country","state_province",
        "postal_cd","guest_full_name",
        "guest_phone","guest_email",
        "guest_country","guest_state_province",
        "guest_city","guest_address","guest_postal_cd",
        "id_member"
    ];

     /*
	 * Function: add data guest
	 * Param: 
	 *	$request	: data guest
	 */
    public static function save_guest($request){
        if(empty($request['id_member'])){
            $request['id_member'] = null;
        }
        $result= Guest::create([
                                "full_name" => $request['full_name'],
                                "phone" => $request['phone'],
                                "email" => $request['email'],
                                // "address" => $request['address'],
                                // "city" => $request['city'],
                                // "country" => $request['country'],
                                // "state_province" => $request['state_province'],
                                // "postal_cd" => $request['postal_cd'],
                                "guest_full_name" => $request['guest_full_name'],
                                "guest_phone" => $request['guest_phone'],
                                "guest_email" => $request['guest_email'],
                                // "guest_country" => $request['guest_country'],
                                // "guest_state_province" => $request['guest_state_province'],
                                // "guest_city" => $request['guest_city'],
                                // "guest_address" => $request['guest_address'],
                                // "guest_postal_cd"=> $request['guest_postal_cd'],
                                "id_member" => $request['id_member'],
                                             ] );
        return $result;
    }
}
