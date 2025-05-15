<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_no','hotel_id','be_hotel_id','be_room_pkg_id','customer_id',
        'be_room_type_nm','be_room_pkg_nm','checkin_dt','checkout_dt',
        'ttl_adult','ttl_children','ttl_room','is_member',
        'payment_sts','price','tax','allo_point','allo_coupons_id','allo_coupons_number','allo_access_token','be_rate_plan_code',
        'be_rate_plan_name','be_rate_plan_type','be_room_id','be_amountAfterTax',
        'be_amountAfterTaxRoom','be_amountBeforeTaxServ','be_discount','be_discountIndicator',
        'be_discountIndicatorRoom','be_discountIndicatorServ','be_discountRoom','be_discountServ',
        'be_grossAmountBeforeTax','be_grossAmountBeforeTaxRoom','be_grossAmountBeforeTaxServ',
        'currency','mpg_id','mpg_url','be_uniqueId','be_reservationstatus','payment_source',
        'special_request','duration','be_amountBeforeTaxRoom','be_promotionId','be_discountCode','os_type','status_notification','hold_at','device_id',
        'equipment_id'
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
        // ->format('Y-M-D');
    }

    public function getCheckinDtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['checkin_dt'])
        // ->format('d, M Y');
        ->format('Y-m-d');
    }
    public function getCheckoutDtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['checkout_dt'])
        // ->format('d, M Y');
        ->format('Y-m-d');
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

    public static function get_stay($request){
        // dd($request);
        $result =Reservation::where('customer_id', $request)
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                ->join('guests', 'guests.id', '=', 'reservations.customer_id')
                ->join('members', 'members.id', '=', 'guests.id_member')
                ->select('reservations.id','reservations.payment_sts',
                        'hotels.name',
                        'reservations.be_room_type_nm',
                        'reservations.ttl_room',
                        'reservations.checkin_dt',
                        'reservations.checkout_dt',
                        'reservation.hold_at',
                        )
                ->get();
        return $result;
    }

    public static function save_reservation($request)
    {
        try {
            if($request['be_amountBeforeTaxServ'] == null)
                $request['be_amountBeforeTaxServ'] = 0;
    
            if(empty($request['device_id']))
                $request['device_id'] = null;
    
            if(empty($request['equipment_id']))
                $request['equipment_id'] = null;

            $result= Reservation::updateOrCreate(
                [
                    'transaction_no' => $request['transaction_no']
                ],
                [
                    'hotel_id' => $request['hotel_id'],
                    'be_hotel_id' => $request['be_hotel_id'],
                    'be_room_pkg_id' => $request['be_room_pkg_id'],
                    'be_room_type_nm' => $request['be_room_type_nm'],
                    'be_room_pkg_nm' => $request['be_room_pkg_nm'],
                    'checkin_dt' => $request['checkin_dt'],
                    'checkout_dt' => $request['checkout_dt'],
                    'ttl_adult' => $request['ttl_adult'],
                    'ttl_children' => $request['ttl_children'],
                    'ttl_room' => $request['ttl_room'],
                    'is_member' => $request['is_member'],
                    'customer_id'=> $request['customer_id'],
                    'price' => $request['price'],
                    'tax' => $request['tax'],
                    'allo_point' => $request['allo_point'],
                    'allo_coupons_id' => $request['allo_coupons_id'],
                    'allo_coupons_number' => isset($request['allo_coupons_number']) ? $request['allo_coupons_number'] : null ,
                    'allo_access_token' => isset($request['allo_access_token']) ? $request['allo_access_token'] : null ,
                    'be_rate_plan_code' => $request['be_rate_plan_code'],
                    'be_rate_plan_name' => $request['be_rate_plan_name'],
                    'be_rate_plan_type' => $request['be_rate_plan_type'],
                    'be_room_id' => $request['be_room_id'],
                    'be_amountAfterTax' => $request['be_amountAfterTax'],
                    'be_amountAfterTaxRoom' => $request['be_amountAfterTaxRoom'],
                    'be_amountBeforeTaxServ'  => $request['be_amountBeforeTaxServ'],
                    'be_discount' => $request['be_discount'],
                    'be_discountIndicator' => $request['be_discountIndicator'],
                    'be_discountIndicatorRoom' => $request['be_discountIndicatorRoom'],
                    'be_discountIndicatorServ' => $request['be_discountIndicatorServ'],
                    'be_discountRoom' => $request['be_discountRoom'],
                    'be_discountServ' => $request['be_discountServ'],
                    'be_grossAmountBeforeTax' => $request['be_grossAmountBeforeTax'],
                    'be_grossAmountBeforeTaxRoom' => $request['be_grossAmountBeforeTaxRoom'],
                    'be_grossAmountBeforeTaxServ' => $request['be_grossAmountBeforeTaxServ'],
                    'currency' => $request['currency'],
                    'be_uniqueId' => $request['be_uniqueId'],
                    'be_reservationstatus' => $request['be_reservationstatus'],
                    'payment_source' => $request['payment_source'],
                    // 'payment_source' => self::setPaymentSourceByPkgName( $request['be_room_pkg_nm'] ),
                    'special_request' => $request['special_request'],
                    'duration' =>$request['duration'],
                    'be_amountBeforeTaxRoom' => $request['be_amountBeforeTaxRoom'],
                    'be_promotionId'=> $request['be_promotionId'],
                    'be_discountCode'=> $request['be_discountCode'],
                    'os_type' => $request['os_type'],
                    'hold_at' => $request['hold_at'],
                    'device_id' => $request['device_id'],
                    'equipment_id' => $request['equipment_id']
                ]);
            return $result;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::debug($e->getMessage());
            throw new \Exception("Failed to create or update reservation to table.");
        }
    }

    private static function setPaymentSourceByPkgName(string $pkgName) {
        switch ($pkgName) {
            case strpos($pkgName, 'Mega Card') !== false:
                $paymentSource = 'megacc';
                break;
            case strpos($pkgName, 'Mega VA') !== false:
                $paymentSource = 'megava';
                break;
            case strpos($pkgName, 'Mega QRIS') !== false:
                $paymentSource = 'megaqris';
                break;
            case strpos($pkgName, 'Mega Debit') !== false:
                $paymentSource = 'megadc';
                break;
            case strpos($pkgName, 'Mega Wallet') !== false:
                $paymentSource = 'megawallet';
                break;
            case strpos($pkgName, 'BNI VA') !== false:
                $paymentSource = 'bniva';
                break;
            case strpos($pkgName, 'BRI VA') !== false:
                $paymentSource = 'briva';
                break;
            case strpos($pkgName, 'Mandiri VA') !== false:
                $paymentSource = 'mandiriva';
                break;
            case strpos($pkgName, 'BCA VA') !== false:
                $paymentSource = 'bcava';
                break;
            case strpos($pkgName, 'Allo Pay') !== false:
                $paymentSource = 'allopay';
                break;
            case strpos($pkgName, 'Allo Paylater') !== false:
                $paymentSource = 'allopaylater';
                break;
            case strpos($pkgName, 'Allo Point') !== false:
                $paymentSource = 'allopoint';
                break;
            case strpos($pkgName, 'Alfamart') !== false:
                $paymentSource = 'alfamartotc';
                break;
            case strpos($pkgName, 'Indomaret') !== false:
                $paymentSource = 'indomaretotc';
                break;
            
            default:
                $paymentSource = '';
                break;
        }

        return $paymentSource;
    }

    public static function get_reservation($request){
        
        // dd($request['transaction_no']);
        $result =Reservation::where('transaction_no', $request['transaction_no'])
                // ->where('payment_sts', 'paid')
                // ->where('be_reservationstatus', 'w')
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                // ->join('users', 'users.id', '=', 'reservations.customer_id')
                ->select('reservations.*','hotels.name as name_hotel',)
                //         'hotels.name',
                //         'reservations.be_room_type_nm',
                //         'reservations.ttl_room',
                //         'reservations.checkin_dt',
                //         'reservations.checkout_dt'
                        // )
                ->first();
        return $result;
    }

    public static function get_reservation_mdcid($request){
        
        $result =Reservation::where('transaction_no', $request['transaction_no'])
                ->where('members.mdcid', $request['mdcid'])
                // ->where('be_reservationstatus', 'w')
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                ->join('guests', 'guests.id', '=', 'reservations.customer_id')
                ->join('members', 'members.id', '=', 'guests.id_member')
                ->select('reservations.*','hotels.name as name_hotel',)
                //         'hotels.name',
                //         'reservations.be_room_type_nm',
                //         'reservations.ttl_room',
                //         'reservations.checkin_dt',
                //         'reservations.checkout_dt'
                        // )
                // ->toSql();
                ->first();
        // dd($result);
        return $result;
    }


    public static function get_reservation_member($request){
        
        $date = date('Y-m-d', strtotime('-90 days', strtotime(date('Y-m-d'))));
        
        $result =Reservation::where('members.id', $request['id_member'])
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                ->join('guests', 'guests.id', '=', 'reservations.customer_id')
                ->join('members', 'members.id', '=', 'guests.id_member')
                ->select('reservations.*','hotels.name as name_hotel','members.id as id_member','guests.full_name as guest_name')
                ->where('reservations.created_at', '>=', $date )
                ->whereNotNull('payment_sts')
                ->orderBy('reservations.created_at','DESC')
                ->get();
        return $result;
    }

    public static function get_reservation_member_date($request){
        $dateTo = date('Y-m-d', strtotime("+1 day", strtotime($request['dtTo'])));
        $result =Reservation::where('members.id', $request['id_member'])
                ->whereBetween('reservations.created_at',[$request['dtFrom'],$dateTo])
                ->whereNotNull('payment_sts')
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                ->join('guests', 'guests.id', '=', 'reservations.customer_id')
                ->join('members', 'members.id', '=', 'guests.id_member')
                ->select('reservations.*','hotels.name as name_hotel','members.id as id_member','guests.full_name as guest_name')
                ->orderBy('reservations.created_at','DESC')
                ->get();
        return $result;
    }

    public static function get_reservation_member_sts($request){
        $date = date('Y-m-d', strtotime('-90 days', strtotime(date('Y-m-d'))));
        if($request['payment_sts'] == 'finish'){
            $dateFinish = date('Y-m-d');
            
            $result =Reservation::where('members.id', $request['id_member'])
                ->where('payment_sts', 'paid')
                ->where('checkout_dt','<=',$dateFinish)
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                ->join('guests', 'guests.id', '=', 'reservations.customer_id')
                ->join('members', 'members.id', '=', 'guests.id_member')
                ->select('reservations.*','hotels.name as name_hotel','members.id as id_member','guests.full_name as guest_name')
                ->where('reservations.created_at', '>=', $date )
                ->orderBy('reservations.created_at','DESC')
                ->get();
        return $result;
        }
        elseif($request['payment_sts'] == 'paid'){
            $date = date('Y-m-d');
            
            $result =Reservation::where('members.id', $request['id_member'])
                ->where('payment_sts', 'paid')
                ->where('checkin_dt','>=',$date)
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                ->join('guests', 'guests.id', '=', 'reservations.customer_id')
                ->join('members', 'members.id', '=', 'guests.id_member')
                ->select('reservations.*','hotels.name as name_hotel','members.id as id_member','guests.full_name as guest_name')
                ->orderBy('reservations.created_at','DESC')
                ->get();
        return $result;
        }
        $result =Reservation::where('members.id', $request['id_member'])
                ->where('payment_sts', $request['payment_sts'])
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                ->join('guests', 'guests.id', '=', 'reservations.customer_id')
                ->join('members', 'members.id', '=', 'guests.id_member')
                ->select('reservations.*','hotels.name as name_hotel','members.id as id_member','guests.full_name as guest_name')
                ->where('reservations.created_at', '>=', $date )
                ->orderBy('reservations.created_at','DESC')
                ->get();
        return $result;
    }

    public static function get_reservation_member_date_sts($request){
        $dateTo = date('Y-m-d', strtotime("+1 day", strtotime($request['dtTo'])));
        if($request['payment_sts'] == 'finish'){
            $date = date('Y-m-d');
            $result =Reservation::where('members.id', $request['id_member'])
                ->where('payment_sts', 'paid')
                ->where('checkout_dt','<=',$date)
                ->whereBetween('reservations.created_at',[$request['dtFrom'],$dateTo])
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                ->join('guests', 'guests.id', '=', 'reservations.customer_id')
                ->join('members', 'members.id', '=', 'guests.id_member')
                ->select('reservations.*','hotels.name as name_hotel','members.id as id_member','guests.full_name as guest_name')
                ->orderBy('reservations.created_at','DESC')
                ->get();
        return $result;
        }
        elseif($request['payment_sts'] == 'paid'){
            $date = date('Y-m-d');
            
            $result =Reservation::where('members.id', $request['id_member'])
                ->where('payment_sts', 'paid')
                ->where('checkin_dt','>=',$date)
                ->whereBetween('reservations.created_at',[$request['dtFrom'],$dateTo])
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                ->join('guests', 'guests.id', '=', 'reservations.customer_id')
                ->join('members', 'members.id', '=', 'guests.id_member')
                ->select('reservations.*','hotels.name as name_hotel','members.id as id_member','guests.full_name as guest_name')
                ->orderBy('reservations.created_at','DESC')
                ->get();
        return $result;
        }
        $result =Reservation::where('members.id', $request['id_member'])
                ->whereBetween('reservations.checkout_dt',[$request['dtFrom'],$dateTo])
                ->whereNotNull('payment_sts')
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                ->join('guests', 'guests.id', '=', 'reservations.customer_id')
                ->join('members', 'members.id', '=', 'guests.id_member')
                ->select('reservations.*','hotels.name as name_hotel','members.id as id_member','guests.full_name as guest_name')
                ->orderBy('reservations.created_at','DESC')
                ->get();
        return $result;
    }

    public static function get_reservation_email($request){
        
        // dd($request['transaction_no']);
        $result =Reservation::where('transaction_no', $request['transaction_no'])
                // ->where('payment_sts', 'paid')
                // ->where('be_reservationstatus', 'w')
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                // ->join('users', 'users.id', '=', 'reservations.customer_id')
                ->select('reservations.*','hotels.name as name_hotel','hotels.email_notification')
                //         'hotels.name',
                //         'reservations.be_room_type_nm',
                //         'reservations.ttl_room',
                //         'reservations.checkin_dt',
                //         'reservations.checkout_dt'
                        // )
                ->first();
        return $result;
    }

    /* 
	 * Function: update data status notifikasi
	 * Param: 
	 *	$request	: transaction_no
	 */
    public static function update_status_notif($request){
        $result = Reservation::where('transaction_no', $request)
                            ->update([
                                'status_notification' => 1,
                                ] );
        return $result;
    }

    /* 
	 * Function: update data status notifikasi
	 * Param: 
	 *	$request	: transaction_no
	 */
    public static function update_hold_at($request){
        $result = Reservation::where('transaction_no', $request['transaction_no'])
                            ->update([
                                'hold_at' => $request['hold_at'],
                                ] );
        return $result;
    }

    /* 
	 * Function: update data os type reservation
	 * Param: 
	 *	$request	: id
	 */
    public static function update_os_type_reservation($request){
        // dd($request['os_type']);
        $result = Reservation::where('transaction_no', $request['transaction_no'])
        ->update([
            'os_type' => $request['os_type']
            ] );
        return $result;
    }

    /* 
	 * Function: update data status payment
	 * Param: 
	 *	$request	: id
	 */
    public static function update_status_payment_reservation($request){
        // dd($request['os_type']);
        if(!empty($request['be_uniqueId'])){
            $result = Reservation::where('transaction_no', $request['transaction_no'])
                ->update([
                        'be_uniqueId' => $request['be_uniqueId'],
                        'be_reservationstatus' => $request['be_reservationstatus'],
                    ] );
        }else{
            $result = Reservation::where('transaction_no', $request['transaction_no'])
            ->update([
                    'transaction_no' => $request['transaction_no'],
                    'payment_sts' => $request['payment_sts'],
                    'pg_transaction_status' => $request['pg_transaction_status']
                ] );
        }
        return $result;        
    }


    public static function get_reservation_mac_transNo($request){
        
        $result =Reservation::where('transaction_no', $request['transaction_no'])
                ->where('reservations.device_id', $request['device_id'])
                // ->where('be_reservationstatus', 'w')
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                ->join('guests', 'guests.id', '=', 'reservations.customer_id')
                ->select('reservations.*','hotels.name as name_hotel',)
                //         'hotels.name',
                //         'reservations.be_room_type_nm',
                //         'reservations.ttl_room',
                //         'reservations.checkin_dt',
                //         'reservations.checkout_dt'
                        // )
                // ->toSql();
                ->first();
        // dd($result);
        return $result;
    }

    
    /* 
	 * Function: get data reservation by mac address
	 * Param: 
	 *	$request	: id
	 */
    public static function get_reservation_mac($request){
        
        $date = date('Y-m-d', strtotime('-90 days', strtotime(date('Y-m-d'))));
        
        $result =Reservation::where('reservations.device_id', $request['device_id'])
                ->where('reservations.is_member', 0)                
                ->whereNotNull('payment_sts')
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                ->join('guests', 'guests.id', '=', 'reservations.customer_id')
                ->select('reservations.*','hotels.name as name_hotel','guests.full_name as guest_name')
                ->where('reservations.created_at', '>=', $date )
                ->orderBy('reservations.created_at','DESC')
                ->get();
        return $result;
    }

    /* 
	 * Function: get data reservation by mac address with date
	 * Param: 
	 *	$request	: id
	 */
    public static function get_reservation_mac_date($request){
        $dateTo = date('Y-m-d', strtotime("+1 day", strtotime($request['dtTo'])));
        $result =Reservation::where('reservations.device_id', $request['device_id'])
                ->where('reservations.is_member', 0)
                ->whereBetween('reservations.created_at',[$request['dtFrom'],$dateTo])
                ->whereNotNull('payment_sts')
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                ->join('guests', 'guests.id', '=', 'reservations.customer_id')
                ->select('reservations.*','hotels.name as name_hotel','guests.full_name as guest_name')
                ->orderBy('reservations.created_at','DESC')
                ->get();
        return $result;
    }

    /* 
	 * Function: get data reservation by mac address with status
	 * Param: 
	 *	$request	: id
	 */
    public static function get_reservation_mac_sts($request){
        $date = date('Y-m-d', strtotime('-90 days', strtotime(date('Y-m-d'))));
        if($request['payment_sts'] == 'finish'){
            $dateFinish = date('Y-m-d');
            
            $result =Reservation::where('reservations.device_id', $request['device_id'])
                ->where('reservations.is_member', 0)
                ->where('payment_sts', 'paid')
                ->where('checkout_dt','<=',$dateFinish)
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                ->join('guests', 'guests.id', '=', 'reservations.customer_id')
                ->select('reservations.*','hotels.name as name_hotel','guests.full_name as guest_name')
                ->where('reservations.created_at', '>=', $date )
                ->orderBy('reservations.created_at','DESC')
                ->get();
        return $result;
        }
        elseif($request['payment_sts'] == 'paid'){
            $date = date('Y-m-d');
            
            $result =Reservation::where('reservations.device_id', $request['device_id'])
                ->where('payment_sts', 'paid')
                ->where('reservations.is_member', 0)
                ->where('checkin_dt','>=',$date)
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                ->join('guests', 'guests.id', '=', 'reservations.customer_id')
                ->select('reservations.*','hotels.name as name_hotel','guests.full_name as guest_name')
                ->orderBy('reservations.created_at','DESC')
                ->get();
        return $result;
        }
        $result =Reservation::where('reservations.device_id', $request['device_id'])
                ->where('payment_sts', $request['payment_sts'])
                ->where('reservations.is_member', 0)
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                ->join('guests', 'guests.id', '=', 'reservations.customer_id')
                ->select('reservations.*','hotels.name as name_hotel','guests.full_name as guest_name')
                ->where('reservations.created_at', '>=', $date )
                ->orderBy('reservations.created_at','DESC')
                ->get();
        return $result;
    }


    /* 
	 * Function: get data reservation by mac address with status and date
	 * Param: 
	 *	$request	: id
	 */
    public static function get_reservation_mac_date_sts($request){
        $dateTo = date('Y-m-d', strtotime("+1 day", strtotime($request['dtTo'])));
        if($request['payment_sts'] == 'finish'){
            $date = date('Y-m-d');
            $result =Reservation::where('reservations.device_id', $request['device_id'])
                ->where('payment_sts', 'paid')
                ->where('reservations.is_member', 0)
                ->where('checkout_dt','<=',$date)
                ->whereBetween('reservations.created_at',[$request['dtFrom'],$dateTo])
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                ->join('guests', 'guests.id', '=', 'reservations.customer_id')
                ->select('reservations.*','hotels.name as name_hotel','guests.full_name as guest_name')
                ->orderBy('reservations.created_at','DESC')
                ->get();
        return $result;
        }
        elseif($request['payment_sts'] == 'paid'){
            $date = date('Y-m-d');
            
            $result =Reservation::where('reservations.device_id', $request['device_id'])
                ->where('payment_sts', 'paid')
                ->where('reservations.is_member', 0)
                ->where('checkin_dt','>=',$date)
                ->whereBetween('reservations.created_at',[$request['dtFrom'],$dateTo])
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                ->join('guests', 'guests.id', '=', 'reservations.customer_id')
                ->select('reservations.*','hotels.name as name_hotel','guests.full_name as guest_name')
                ->orderBy('reservations.created_at','DESC')
                ->get();
        return $result;
        }
        $result =Reservation::where('reservations.device_id', $request['device_id'])
                ->where('reservations.is_member', 0)
                ->whereBetween('reservations.checkout_dt',[$request['dtFrom'],$dateTo])
                ->whereNotNull('payment_sts')
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                ->join('guests', 'guests.id', '=', 'reservations.customer_id')
                ->select('reservations.*','hotels.name as name_hotel','guests.full_name as guest_name')
                ->orderBy('reservations.created_at','DESC')
                ->get();
        return $result;
    }
    /* 
	 * Function: count notification by device id
	 * Param: 
	 *	$request	: id
	 */
    public static function get_total_notif_stay($request){
        
        $date = date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))));
        
        $result =Reservation::where('reservations.device_id', $request['device_id'])
                ->where('reservations.status_notification', 0)
                ->where('reservations.is_member', 0)                
                ->whereNotNull('payment_sts')
                ->where('reservations.created_at', '>=', $date )
                ->count();
        //dd($result);
        return $result;
    }
}
