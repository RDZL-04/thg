<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Facility;
use App\Models\HotelImages;
use App\Models\HotelFacility;
use App\Models\NearAttraction;
use App\Models\Guest;
use App\Models\Msystem;
use App\Models\Review;
use App\Models\Members;
use App\Models\OrderDining;
use App\Http\Controllers\LOG\ErorrLogController;
use App\Models\Reservation;
use App\Models\ReservationTmp;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\SendMailReservation;
use App\Mail\SendMailReservationPic;
use App\Http\Controllers\API\FcmController;
use App\Http\Controllers\API\AlloApiController;
use App\Http\Controllers\API\MemberController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Helpers\Custom\GAfour;
use GuzzleHttp\Exception\RequestException;

/*
|--------------------------------------------------------------------------
| reservation API Controller
|--------------------------------------------------------------------------
|
| Validate, Authorize api key
| This user controller will control data reservation
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: December , 2020 10:30
*/

class ReservationController extends Controller
{
    
    private Array $headers;
    private ErorrLogController $LogController;
    private FcmController $FcmController;
    private AlloApiController $AlloApiController;
    private MemberController $MemberController;
    private GAfour $GAfour;
    private Array $ruleContact = [
        'full_name' => 'required|max:25|',
        'email' => 'required|email|max:50',
        'phone' => 'required|numeric|digits_between:10,13',
    ];
    private Array $ruleContactMember = [
        'full_name' => 'required|max:25|',
        'id_member' =>'required',
        'email' => 'required|email|max:50',
        'phone' => 'required|numeric|digits_between:10,13',
    ];
    private Array $rulesGuest = [
        'guest_full_name' => 'required|max:25|',
        'guest_phone' => 'required|numeric|digits_between:10,13',
        'guest_email' => 'required|email|max:50',
    ];
    private Array $ruleReservation = [
        'hotel_id' => 'required|numeric',
        'be_hotel_id' => 'required',
        'be_room_type_nm' => 'required',
        'be_room_pkg_nm' => 'required',
        'checkin_dt' => 'required',
        'checkout_dt' => 'required',
        'ttl_adult' => 'required|numeric',
        'ttl_room' => 'required|numeric',
        'is_member' => 'boolean',
        'price' => 'required|numeric|regex:/^[0-9]+$/',
        'tax' => 'required|numeric',
        'be_rate_plan_code' => 'required',
        'be_rate_plan_name' => 'required',
        'be_rate_plan_type' => 'required',
        'be_room_id' => 'required',
        'be_amountAfterTax' => 'required|numeric',
        'be_amountAfterTaxRoom' => 'required|numeric',
        'be_discount' => 'required|numeric',
        'be_discountIndicator' => 'boolean',
        'be_discountIndicatorRoom' => 'boolean',
        'be_discountIndicatorServ' => 'boolean',
        'be_discountRoom' => 'required|numeric',
        'be_discountServ' => 'required|numeric',
        'be_grossAmountBeforeTaxRoom' => 'required|numeric',
        'be_grossAmountBeforeTaxServ' => 'required|numeric',
        'currency' => 'required',
        'be_uniqueId' => 'required',
        'be_reservationstatus' => 'required|max:1',
        'be_amountBeforeTaxRoom' => 'required',
        'os_type' => 'required',
        'hold_at' => 'required|date_format:Y-m-d H:i:s',
        // edited by Arka.Rangga, Special Request diganti, menjadi option dengan semicolon
        // Other
        'special_request' => 'max:1500'
    ];

    public function __construct() {
        // Set header for API Request
        $this->headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'x-api-key' => env('API_KEY'),
          ];
        $this->LogController = new ErorrLogController;
        $this->FcmController = new FcmController;
        $this->AlloApiController = new AlloApiController;
        $this->MemberController = new MemberController;
        // $this->GAfour = new GAfour();
    }
    /*
	 * Function: get data promo from msystem
	 * body: 
	 * $request	: json
	 **/
      public function get_promo(){
        try
        {
            $data = Msystem::get_promo();
            if(count($data) != 0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $data,
                ];
                return response()->json($response, 200);  
            }
            else{
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null
                ];
                return response()->json($response, 200);  
            }
        } catch ( \Throwable $e) {
            report($e);
            $error = ['modul' => 'get_promo',
                'actions' => 'get data promo',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    public function tc_hold_reservation(Request $request){
        try {
            $api_key = $request->header('x-api-key');
            if($api_key == null && ( !isset($device_id) || $device_id == null ) ){
                $response = [
                    'status' => false,
                    'message' => 'The api key in header is required.',
                    'code' => 500,
                    'data' => null, 
                ];
                return response()->json($response, 500);
            }
            $data = $request->getContent();
            $data = json_decode($data, true);
            
            $ruleData = [
                'hotelCode' => 'required',
            ];
            
            $validator = Validator::make($data, $ruleData);
            $accessToken = $request->header('accessToken');
            $refreshToken = $request->header('refreshToken');
            $device_id = $request->header('deviceId');
            
            $is_member = 0;
            
            if($accessToken == null && $device_id == null ){
                $response = [
                    'status' => false,
                    'message' => 'The access token or device id in header is required.',
                    'code' => 500,
                    'data' => null, 
                ];
                return response()->json($response, 500);
            }else if($accessToken != null){
                if($device_id == null ){
                    $response = [
                        'status' => false,
                        'message' => 'The device id in header is required.',
                        'code' => 500,
                        'data' => null, 
                    ];
                    return response()->json($response, 500);
                }else if($refreshToken == null){
                    $response = [
                        'status' => false,
                        'message' => 'The refresh token in header is required.',
                        'code' => 500,
                        'data' => null, 
                    ];
                    return response()->json($response, 500);
                }else{
                    $response_refresh_token = $this->MemberController->get_mdcid(
                        [
                            'accessToken' => $accessToken
                            , 'refreshToken' => $refreshToken
                        ]
                    );
                    // Cek response success
                    if(strtoupper($response_refresh_token['message']) == 'SUCCESS') {
                        $response_refresh_token = (array)$response_refresh_token['data'];
                        $mdcid = $response_refresh_token['mdcId'];
                        $accessToken = $response_refresh_token['accessToken'];
                        $refreshToken = $response_refresh_token['refreshToken'];                    
                        $is_member = 1;
                    }  else {
                        $error = [
                            'modul' => 'tc_hold_reservation',
                            'actions' => 'Hit Get Reservation mdcid API',
                            'error_log' => json_encode($response_refresh_token),
                            'device' => "0" 
                            ];
                        $report = $this->LogController->error_log($error);
                        $response = [
                            'status' => false,
                            'message' => $response_refresh_token['message'],
                            'code' => 400,
                            'data' => $response_refresh_token
                        ];
                        return response()->json($response, 400);
                    }
                }
            }
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(), 
                    'data' =>null
                ];
                return response()->json($response, 200);
            }else{
                $tc_login = $this->tc_login($data['hotelCode']);
                if(isset($tc_login) && $tc_login['status']){
                    $get_URL = Msystem::where('system_type', 'tc_url')
                                        ->where('system_cd', 'hold_reservation')->first();
                            
                    if(!empty($get_URL)){
                        $holdReservationURL = str_replace('{hotelCode}', $data['hotelCode'], $get_URL['system_value']);
                    }else{
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => 'Error: tc_url hold_reservation not found in msystem.',
                            'data' =>null,
                        ];
                        return response()->json($response, 200);
                    }         
                    $headers = [
                        'Authorization' => 'Bearer '.$tc_login['data'],
                        'Content-Type' => 'application/json'
                    ];

                    $client = new Client();    
                    $response = $client->request('POST', $holdReservationURL,[
                        'verify' => false,
                        'headers' => $headers,
                        'Accept' => 'application/json',
                        'headers' => $headers,
                        '/delay/5',
                        'json' => $data,
                        ['connect_timeout' => 3.14]
                    ]);
                    
                    if(isset($response) && $response->getStatusCode() == 200) {
                        $body = $response->getBody();

                        $response = json_decode($body, true);
                        $guestCounts = $response['resGlobalInfo']['guestCounts'];
                        $timeSpan = $response['resGlobalInfo']['timeSpan'];
                        $roomStays = $response['roomStays'][0];
                        $ratePlans = $response['roomStays'][0]['ratePlans'][0];
                        $roomRates = $response['roomStays'][0]['roomRates'][0];
                        $total = $response['roomStays'][0]['total'];
                        
                        $adult = 0;
                        $child = 0;
                        foreach($guestCounts as $dt){
                            if($dt['ageQualifyingCode'] == '10'){
                                $adult = $dt['count'];
                            }else if($dt['ageQualifyingCode'] == '8'){
                                $child = $dt['count'];
                            }
                        }

                        $tax = 0;
                        if(isset($response['roomStays'][0]['prodTaxes'])){
                            $tax = $response['roomStays'][0]['prodTaxes']['outTotalInclusiveTaxes']['totalTax'];
                        }
                            
                        $data = [
                            'api_key' => $api_key,
                            'adult' => $adult,
                            'child' => $child,
                            'tax' => $tax,
                            'is_member' => $is_member,
                
                            'uniqueId' => $response['uniqueId'],
                            'hotelCode' => $response['hotelCode'],
                            'reservationStatus' => $response['reservationStatus'],
                            'promotionId' => (!isset($response['promotionId'])) ? null : $response['promotionId'],
                            'discountCode' => (!isset($roomStays['discountCode'])) ? null : $roomStays['discountCode'],
                            
                            'roomTypeCode' => $roomRates['roomTypeCode'],
                            'roomTypeName' => $roomRates['roomTypeName'],
                            
                            'start' => $timeSpan['start'],
                            'end' => $timeSpan['end'],
                            'duration' => $timeSpan['duration'],
                
                            'ratePlanName' => $ratePlans['ratePlanName'],
                            'ratePlanCode' => $ratePlans['ratePlanCode'],
                            'ratePlanType' => $ratePlans['ratePlanType'],
                
                            'price' => $total['amountAfterTax'],
                            'amountAfterTax' => $total['amountAfterTax'],
                            'amountAfterTaxRoom' => $total['amountAfterTaxRoom'],
                            'discount' => $total['discount'],
                            'discountIndicator' => (!$total['discountIndicator'] || $total['discountIndicator'] == '') ? 0 : 1,
                            'discountIndicatorRoom' => (!$total['discountIndicatorRoom'] || $total['discountIndicatorRoom'] == '') ? 0 : 1,
                            'discountIndicatorServ' => (!$total['discountIndicatorServ'] || $total['discountIndicatorServ'] == '') ? 0 : 1,
                            'discountRoom' => $total['discountRoom'],
                            'discountServ' => $total['discountServ'],
                            'grossAmountBeforeTax' => $total['grossAmountBeforeTax'],
                            'grossAmountBeforeTaxRoom' => $total['grossAmountBeforeTaxRoom'],
                            'grossAmountBeforeTaxServ' => $total['grossAmountBeforeTaxServ'],
                
                            'room' => $request['resGlobalInfo']['rooms'],
                            'currency' => 'IDR',
                            'device_id' => $device_id,
                        ];
                        $ruleReservation = [
                            'adult' => 'required|numeric',
                            'child' => 'required|numeric',
                            'tax' => 'required|numeric',
                            'is_member' => 'boolean',
                            'room' => 'required|numeric',
                            'currency' => 'required',
                        ];
                        $validator = Validator::make($data, $ruleReservation);
                        if ($validator->fails()) {
                            // return response gagal
                            $response = [
                                'status' => false,
                                'code' => 400,
                                'message' => $validator->errors()->first(), 
                                'data' =>null,
                                
                            ];
                            return response()->json($response, 200);
                        }else{
                            $save_reservation = ReservationTmp::save_reservation_tmp($data);
                            // dd($save_reservation);
                            if($save_reservation){
                                $getSystem = Msystem::where('system_type', 'reservation')
                                    ->where('system_cd', 'exp')->first();
                                $response = $response + [
                                    'accessToken' => $accessToken
                                    , 'refreshToken' => $refreshToken
                                    , 'reservationExp' => isset($getSystem['system_value']) ? $getSystem['system_value'] : 10
                                ];
                                return response()->json($response, 200);
                            }else{
                                $response = [
                                    'status' => true,
                                    'code' => 200,
                                    'message' => __('message.failed_save_data' ),
                                    'data' => null,
                                ];
                                return response()->json($response, 200);
                            }
                        }
                    }else{
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => 'Error: hold reservation failed.',
                            'data' =>null,
                            
                        ];
                        return response()->json($response, 200);
                    }
                }else{
                    return response()->json($tc_login, 200);
                }
                // dd($tc_login);
            }

        } catch (RequestException $e) {
            $response = $e->getResponse();
            $resBodyContents =  json_decode($response->getBody()->getContents());
            $errContent = $resBodyContents->errors[0];
            $responseCode = $response->getStatusCode();
            return response()->json([
                'status' => false,
                'code' => $responseCode,
                'message' => $errContent->errorMessage,
                'data' =>null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => 400,
                'message' => $e->getMessage(),
                'data' =>null,
            ], 200);
        }
    }
    /*
	 * Function: add data hold reservation
	 * body: 
	 * $request	: json
	 **/
    public function hold_reservation(Request $request)
    {
        try {
            $data = $request->getContent();
            $data = json_decode($data);

            $reservation = $data->reservation;
            $guest = $data->guest;
            $contact = $data->contact_info;
            $alloCode = null;
            $alloCodeVerifier = null;
            $accessToken = null; 
            $refreshToken = null; 

            $guest = json_decode(json_encode($guest), true);
            $reservation = json_decode(json_encode($reservation), true);
            $contact = json_decode(json_encode($contact), true);

            $validator = Validator::make($contact, $reservation['is_member'] == 1 ? $this->ruleContactMember : $this->ruleContact);
            if ($validator->fails()) 
                throw new ValidationException($validator->errors()->first(), 400);

            $validatorGuest = Validator::make($guest, $this->rulesGuest);
            if($validatorGuest->fails()) 
                throw new ValidationException($validatorGuest->errors()->first(), 400);

            $validatorReservation = Validator::make($reservation, $this->ruleReservation);
            if($validatorReservation->fails()) 
                throw new ValidationException($validatorReservation->errors()->first(), 400);

            $data = $request->getContent();
            $data = json_decode($data);
            $data_reservation = $data->reservation;
            $data_guest = $contact + $guest;
            if($reservation['is_member']==1){

                $cek_member = Members::where('id', $data_guest['id_member'])->first();
                if(empty($cek_member)){
                    $response = [
                        'status' => true,
                        'message' => __('message.not_members'),
                        'code' => 200,
                        'data' => null, 
                    ];
                    return response()->json($response, 500);
                }
            }

            $save_guest = Guest::save_guest($data_guest);
            if (!$save_guest)
                return response()->json([
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.failed_save_data' ),
                    'data' => null,
                ], 200);

            $data_reservation = json_decode(json_encode($data_reservation), true); 
            if(empty($data_reservation['transaction_no'])){
                //generate transaction_no
                $day = date('d');
                $cekSeq = Reservation::latest('id')->first();
                if(empty($cekSeq)){
                    $transaction_no = "THG/".date('Ymd')."/S-1";
                }else{
                    $PecahCreated_at= explode(" ", $cekSeq['created_at']);
                    $last_created=$PecahCreated_at[0]." ".$PecahCreated_at[1]." ".$PecahCreated_at[2];
                    $last_data = explode(",",$PecahCreated_at[0]);
                    if ($last_data[0] != $day){
                        $transaction_no = "THG/".date('Ymd')."/S-1";
                    }else{
                        $PecahTransaction= explode("S-", $cekSeq['transaction_no']);
                        $seq_no = $PecahTransaction[1]+1;
                        $transaction_no = "THG/".date('Ymd')."/S-".$seq_no;
                    }
                }
                $data_reservation = $data_reservation+["transaction_no" => $transaction_no];
            }

            # send custom event purchase to GA
            // $hotel = Hotel::where('id', $data_reservation['hotel_id'])->first();
            // $this->GAfour->clientId = $save_guest['id'];
            // $this->GAfour->event = 'purchase';
            // $this->GAfour->params = [
            //     'transaction_id' => $transaction_no,
            //     'value' => $data_reservation['price'],
            //     'tax' => $data_reservation['tax'],
            //     'currency' => $data_reservation['currency'],
            //     'coupon' => 'thg',
            //     'items' => [
            //         [
            //             'item_id' => $data_reservation['be_room_id'],
            //             'item_name' => $data_reservation['be_room_type_nm'],
            //             'index' => 0,
            //             'item_brand' => $hotel['name'],
            //             'price' => $data_reservation['price'],
            //             'quantity' => $data_reservation['duration'],
            //         ]
            //     ],
            // ];
            // $this->GAfour->send();
            # end send custom event purchase to GA

            $data_reservation = $data_reservation+["customer_id" => $save_guest['id']];
            if($data_reservation['is_member'] == 0 || empty($data_reservation['be_promotionId'])){
                $data_reservation['be_promotionId'] = null;
            }

            $compare_reservation = $this->compare_reservation($data_reservation);
            if(!$compare_reservation['status']){
                return response()->json($compare_reservation, 200);
            }

            if ($data->accessToken) {
                $req_hit_allo = new Request();
                $req_hit_allo->merge(['accessToken' => $data->accessToken ]);
                $response = $this->AlloApiController->auth_by_token($req_hit_allo);
                $response = json_decode($response->getContent(), true);

                if(!$response['status']) 
                    throw new \Exception("Failed to get auth by token");
                        
                $alloCode = $response['data']['responseData']['code'];
                $alloCodeVerifier = $response['data']['alloCodeVerifier'];
                $data_reservation['allo_access_token'] = $data->accessToken;
            }

            $data_reservation['payment_source'] = self::setPaymentSourceByPkgName( $data_reservation['be_room_pkg_nm'] );
            $save_reservation = Reservation::save_reservation($data_reservation);
            if(!$save_reservation)
                return response()->json([
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.failed_save_data' ),
                    'data' => null,
                ], 200);

            $get_guest = Guest::where('id', $save_reservation['customer_id'])->first();

            $resInquery = $this->reqInqiery($save_reservation['transaction_no'], $alloCode, $alloCodeVerifier);
            $this->FcmController->send_notification_stay_user($save_reservation);

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => __('message.data_saved_success' ),
                'data' => [
                    "contact" => $get_guest,
                    "reservation" => $save_reservation,
                    "inquiry" => $resInquery,
                    "accessToken" => $accessToken,
                    "refreshToken" => $refreshToken
                ],
            ], 200);
            
        } catch ( ValidationException $e ) {
            return response()->json([
                'status' => false,
                'message' => $e->validator,
                'code' => $e->status,
                'data' => null, 
            ], 200);
        } catch ( \Throwable $e ) {
            Log::info($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'code' => 500,
                'data' => null, 
            ], 500);
        }
    }

    private static function setPaymentSourceByPkgName(string $pkgName) {
        $paymentSource = '';
        $paymentSources = Msystem::where('system_type', 'paymentSources')->get();
        foreach ($paymentSources as $source) {
            if (strpos($pkgName, $source->system_cd) !== false)
                $paymentSource = $source->system_value;
        }

        return $paymentSource;
        // switch ($pkgName) {
        //     case strpos($pkgName, 'Mega Card') !== false:
        //         $paymentSource = 'megacc';
        //         break;
        //     case strpos($pkgName, 'Mega VA') !== false:
        //         $paymentSource = 'megava';
        //         break;
        //     case strpos($pkgName, 'Mega QRIS') !== false:
        //         $paymentSource = 'megaqris';
        //         break;
        //     case strpos($pkgName, 'Mega Debit') !== false:
        //         $paymentSource = 'megadc';
        //         break;
        //     case strpos($pkgName, 'Mega Wallet') !== false:
        //         $paymentSource = 'megawallet';
        //         break;
        //     case strpos($pkgName, 'BNI VA') !== false:
        //         $paymentSource = 'bniva';
        //         break;
        //     case strpos($pkgName, 'BRI VA') !== false:
        //         $paymentSource = 'briva';
        //         break;
        //     case strpos($pkgName, 'Mandiri VA') !== false:
        //         $paymentSource = 'mandiriva';
        //         break;
        //     case strpos($pkgName, 'BCA VA') !== false:
        //         $paymentSource = 'bcava';
        //         break;
        //     case strpos($pkgName, 'Allo Pay') !== false:
        //         $paymentSource = 'allopay';
        //         break;
        //     case strpos($pkgName, 'Allo Paylater') !== false:
        //         $paymentSource = 'allopaylater';
        //         break;
        //     case strpos($pkgName, 'Allo Point') !== false:
        //         $paymentSource = 'allopoint';
        //         break;
        //     case strpos($pkgName, 'Alfamart') !== false:
        //         $paymentSource = 'alfamartotc';
        //         break;
        //     case strpos($pkgName, 'Indomaret') !== false:
        //         $paymentSource = 'indomaretotc';
        //         break;
            
        //     default:
        //         $paymentSource = '';
        //         break;
        // }

        // return $paymentSource;
    }

    private function reqInqiery($transNo, $alloCode = null, $alloVerifier = null) {
        $client = new Client(); //GuzzleHttp\Client
        $response = $client->request('POST', url('api').'/inquiry',[
            'verify' => false,
            'form_params'   => [
                'transaction_no' => $transNo, 
                'alloCode' => $alloCode, 
                'alloCodeVerifier' => $alloVerifier 
            ],
            'headers'       => $this->headers,
            '/delay/5',
            ['connect_timeout' => 3.14]
        ]);
        $res = json_decode($response->getBody());

        if (!$res->status || !$res->data)
            throw new \Exception('Failed to request inquery to payment gateway');

        return $res->data;
    }

    public function tc_login($be_hotel_id){
        if(isset($be_hotel_id)) {
			$hotel = Hotel::where('be_hotel_id', $be_hotel_id)->first();
		}else{
			$hotel = null;
		}
		
		if(!$hotel) {
            $res = [
                'status' => false,
                'message' => 'Error: Reservation hotel id not valid',
                'code' => 500,
                'data' => null, 
            ];
			return $res;
		}

        $getTcLoginURL = Msystem::where('system_type', 'tc_url')
                                ->where('system_cd', 'oauth')->get();
        if(!empty($getTcLoginURL)){
            foreach($getTcLoginURL as $d)
            {
                if($d['system_type'] === 'tc_url' && $d['system_cd'] === 'oauth'){
                    $tcLoginURL = $d['system_value'];
                }
            }
        }else{
            $res = [
                'status' => false,
                'message' => 'Error: tc_url oauth not found in msystem.',
                'code' => 500,
                'data' => null, 
            ];
			return $res;
        }
        
        $oauthHeader = base64_encode($hotel['be_api_key'].':'.$hotel['be_secret_key']);
            
        $headers = [
                'Authorization' => 'Basic '.$oauthHeader,
                'Content-Type' => 'application/json'
        ];
            $client = new Client();    
            $response = $client->request('POST', $tcLoginURL,[
                                        'verify' => false,
                                        'headers' => $headers,
                                        '/delay/5',
                                            // ['connect_timeout' => 3.14]
                                        ]);
        // dd($response);
        if(isset($response) && $response->getStatusCode() == 200) {
            
            $body = $response->getBody();
            $jwt = json_decode($body, true);
        }else{
            $jwt = null;
        }
        if(!$jwt) {
            $res = [
                'status' => false,
                'message' => 'Error: Auth TC failed.',
                'code' => 500,
                'data' => null, 
            ];
			return $res;
        }else{
            $res = [
                'status' => true,
                'message' => 'Auth TC success.',
                'code' => 200,
                'data' => $jwt['access_token'], 
            ];
			return $res;
        }
        // dd($jwt);
    }

    public function compare_reservation($reservation){
        // dd($reservation);
        $get_reservation_tmp = ReservationTmp::where('uniqueId', $reservation['be_uniqueId'])->first();
        $res = [
            'status' => true,
            'message' => 'Data reservation same.',
            'code' => 200,
            'data' => $get_reservation_tmp, 
        ];
        $get_hotel = Hotel::where('be_hotel_id', $get_reservation_tmp['hotelCode'])->first();
        $rule = [
            'be_uniqueId' => 'required|in:' . $get_reservation_tmp['uniqueId'],
            'hotel_id' => 'required|in:' . $get_hotel['id'],
            'be_hotel_id' => 'required|in:' . $get_reservation_tmp['hotelCode'],
            'be_room_pkg_id' => 'in:' . $get_reservation_tmp['roomTypeCode'],
            //'be_room_type_nm' => 'required|in:' . $get_reservation_tmp['roomTypeName'],
            //'be_room_pkg_nm' => 'required|in:' . trim($get_reservation_tmp['ratePlanName']),
            'checkin_dt' => 'required|in:' . $get_reservation_tmp['start'],
            'checkout_dt' => 'required|in:' . $get_reservation_tmp['end'],
            'ttl_adult' => 'in:' . $get_reservation_tmp['adult'],
            'ttl_children' => 'in:' . $get_reservation_tmp['child'],
            'ttl_room' => 'in:' . $get_reservation_tmp['room'],
            'is_member' => 'in:' . $get_reservation_tmp['is_member'],
            // 'price' => 'required|in:' . $get_reservation_tmp['price'],
            'tax' => 'in:' . $get_reservation_tmp['tax'],
            'be_rate_plan_code' => 'in:' . $get_reservation_tmp['ratePlanCode'],
            //'be_rate_plan_name' => 'in:' . trim($get_reservation_tmp['ratePlanName']),
            'be_rate_plan_type' => 'in:' . $get_reservation_tmp['ratePlanType'],
            // 'be_amountAfterTax' => 'in:' . $get_reservation_tmp['amountAfterTax'],
            'be_room_id' => 'in:' . $get_reservation_tmp['roomTypeCode'],
            //'be_amountAfterTaxRoom' => 'in:' . $get_reservation_tmp['amountAfterTaxRoom'],
            //'be_amountBeforeTaxServ' => 'in:' . $get_reservation_tmp['grossAmountBeforeTaxServ'],
            //'be_discount' => 'in:' . $get_reservation_tmp['discount'],
            //'be_discountIndicator' => 'in:' . $get_reservation_tmp['discountIndicator'],
            //'be_discountIndicatorRoom' => 'in:' . $get_reservation_tmp['discountIndicatorRoom'],
            //'be_discountIndicatorServ' => 'in:' . $get_reservation_tmp['discountIndicatorServ'],
            //'be_discountRoom' => 'in:' . $get_reservation_tmp['discountRoom'],
            //'be_discountServ' => 'in:' . $get_reservation_tmp['discountServ'],
            //'be_grossAmountBeforeTax' => 'in:' . $get_reservation_tmp['grossAmountBeforeTax'],
            //'be_grossAmountBeforeTaxRoom' => 'in:' . $get_reservation_tmp['grossAmountBeforeTaxRoom'],
            //'be_grossAmountBeforeTaxServ' => 'in:' . $get_reservation_tmp['grossAmountBeforeTaxServ'],
            'be_reservationstatus' => 'required|in:' . $get_reservation_tmp['reservationStatus'],
            'currency' => 'in:' . $get_reservation_tmp['currency'],
            'duration' => 'in:' . $get_reservation_tmp['duration'],
            //'be_amountBeforeTaxRoom' => 'in:0',
            'device_id' => 'in:' . $get_reservation_tmp['device_id'],
            'os_type' => 'in:MOBILE,WEB,android,ios',
        ];
        if($reservation['os_type'] == 'MOBILE' || $reservation['os_type'] == 'ios' || $reservation['os_type'] == 'android'){
            $rule = $rule + [
                'be_promotionId' => 'in:' . $get_reservation_tmp['promotionId'],
                'be_discountCode' => 'in:' . $get_reservation_tmp['discountCode'],
            ];
        }
        $validator = Validator::make($reservation, $rule);
        
        if ($validator->fails()) {
            // return response gagal
            $response = [
                'status' => false,
                'code' => 400,
                'message' => $validator->errors()->first(), 
                'data' =>null,
            ];
        }else{
            $response = [
                'status' => true,
                'code' => 200,
                'message' => '', 
                'data' =>null,
            ];
        }
        return $response;
    }
    /*
	 * Function: search data hotel by name
	 * body: 
	 * $request	: string name
	 **/
    public function get_hotel_by_name($name)
    {
        
            $input=['name' => rawurldecode($name)] ;
            $validator = Validator::make($input, [
            'name' => 'required|regex:/^[a-zA-Z]+(-_ [a-zA-Z]+)*/',
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(), 
                    'data' => null,
                ];
                return response()->json($response, 200);
            }  
            try {
            //get data hotel
            $get = Hotel::search_hotel($name);
            /*
             * count data if data 0 send response data not found
             * if data > 0 next process
            **/
    
            $count = count($get);
            
            if($count == 0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }else{
                /*
                 * input data hotel image to data hotel
                **/
                foreach ($get as $hotel) {
                    /**
                     * search data image by  id hotel
                     * and search data facility by id hotel
                     */
                    $hotel_image = HotelImages::get_image($hotel->id);
                    $count_img = count($hotel_image);
                    //convert data to array object
                    $hotel = json_decode(json_encode($hotel), true); 
                    if ($count_img==0){
                        $hotel_image = null;
                    }
                    $hotel_image = ['hotel_image' => $hotel_image];
                        //input data to array
                        $get_hotel[] = $hotel+$hotel_image;
                }
                /**
                 * send response data hotel, image, facility
                 */
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => $get_hotel,
                ];
                return response()->json($response, 200);    
                
            }
        } catch ( \Throwable $e ) {
            report($e);
            $error = ['modul' => 'get_hotel_by_name',
                'actions' => 'get data hotel by name',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    /**
	 * Function: get data hotel main page
	 * body: param[id/name,page]
	 *	$request	: 
	*/
    
    public function get_hotel_main(Request $request)
    {
        try {
        /*
        * check id if null search by name
        * if not null search by id and name
        **/
        if(!empty($request->city)){
            $get = Hotel::get_hotel_by_city($request);
                            
        }else{
            $get = Hotel::get_hotel($request);
        }
        //    dd($get); 
            /*
             * count data if data 0 send response data not found
             * if data > 0 next process
            **/
            // dd($get);
            $count = count($get);
            if($count == 0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => ['total_page' => null,
                               'current_page' => null,
                               'data' => null],
                ];
                return response()->json($response, 200);
            }else{
               
                /*
                 * input data hotel image to data hotel
                **/
                foreach ($get as $hotel) {
                    /**
                     * search data image by  id hotel
                     * and search data facility by id hotel
                     */
                    $hotel_image = HotelImages::get_image($hotel->id);
                    $hotel_facility = HotelFacility::get_facility($hotel->id);
                    $hotel_near = NearAttraction::get_near_hotels($hotel->id);
                    $get_ranting = Review::get_ranting($hotel->id);
                    //get data top review hotel
                    $get_top_review = Review::get_top_review($hotel->id);
                    $rating = $get_ranting['0'];
                        if(empty($get_top_review)){
                            $get_top_review=null;
                        }
                        else if(empty($get_ranting)){
                            $get_ranting=null;
                        }
                    $review = ['rating' => $rating->rating,
                    'top_review' => $get_top_review,];
                    $count_img = count($hotel_image);
                    $count_facility = count($hotel_facility);
                    //convert data to array object
                    $hotel = json_decode(json_encode($hotel), true); 
                    // dd($get);
                    if ($count_img==0){
                        $hotel_image = null;
                    }
                    if($count_facility == 0){
                        $hotel_facility = null;
                    }
                    if (empty($hotel_near)) { 
                        $hotel_near = null;
                    }
                    $hotel_near = ['hotel_near' => $hotel_near];
                    $hotel_image = ['hotel_image' => $hotel_image];
                    $hotel_facility = ['hotel_facility' => $hotel_facility];
                    $hotel_review = ['hotel_review' => $review];
                        //input data to array
                        $get_hotel[] = $hotel+$hotel_image+$hotel_facility+$hotel_near+$hotel_review;
                }
                /**
                 * send response data hotel, image, facility
                 */
                if(!empty($request->city)){
                    $response = [
                        'status' => true,
                        'code' => 200,
                        'message' => __('message.data_found' ),
                        'data' => [ 'total_hotel' => $count,
                                    'data' => $get_hotel]
                    ];
                    return response()->json($response, 200);  
                }else{
                    $response = [
                        'status' => true,
                        'code' => 200,
                        'message' => __('message.data_found' ),
                        'data' => [ 'total_hotel' => $count,
                                    'total_page' => $get->lastPage(),
                                    'current_page' => $get->currentPage(),
                                    'data' => $get_hotel],
                    ];
                    return response()->json($response, 200);
                }       
            }        
        } catch ( \Throwable $e ) {
            report($e);
            $error = ['modul' => 'get_hotel_main',
                'actions' => 'get data hotel',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    /**
	 * Function: get data reservation callback
	 * body: param[no_transaction, status_payment, reservationstatus]
	 *	$request	: 
	*/
    
    public function get_data_reservation_callback(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'transaction_no' => 'required',
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            $reservation = Reservation::get_reservation($request->all());
            if($reservation == null){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }else{
                $guest = Guest::where('id', $reservation['customer_id'])
                                ->first();
                $faicility = HotelFacility::get_facility($reservation['hotel_id']);
                $faicility_hotel = ['hotel_facility' => $faicility];
                if(count($faicility)==0){
                    $faicility_hotel = ['hotel_facility' => null];
                }  
                $url =['url_pdf' => url('/payment_reservation?transaction_no='.$reservation['transaction_no'])];
                // dd($faicility_hotel);
                $reservation = json_decode(json_encode($reservation), true); 
                $reservation = $reservation + $url + $faicility_hotel;
                if($reservation['payment_sts'] == null || $reservation['payment_sts'] == "")
                {
                    $status = 'unpaid';
                    $reservation['status'] = $status;
                }
                else if($reservation['payment_sts'] == 'unpaid' || $reservation['payment_sts'] == 'pending')
                {
                    $status = 'unpaid';
                    $reservation['status'] = $status;
                }
                else if ($reservation['payment_sts']== 'paid')
                {
                    if($reservation['checkin_dt'] <= date('Y-m-d')){
                        $status = 'finish';
                        $reservation['status'] = $status;
                    }
                    if($reservation['checkin_dt'] >= date('Y-m-d')){
                        $status = 'paid';
                        $reservation['status'] = $status;
                    }
                }
                else{
                    $status = 'failed';
                    $reservation['status'] = $status;
                }
            //    dd($reservation);
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => ['reservation' => $reservation,
                               'guest' => $guest],
                ];
                return response()->json($response, 200);    
                
            }        
        } catch ( \Throwable $e ) {
            report($e);
            $error = ['modul' => 'get_data_reservation',
                'actions' => 'get data reservation hotel',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    /**
	 * Function: get data reservation
	 * body: param[no_transaction, status_payment, reservationstatus]
	 *	$request	: 
	*/
    
    public function get_data_reservation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'transaction_no' => 'required',
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            // edit : arka.moharifrifai 2022-06-03
            $accessToken = $request->header('accessToken');
            $refreshToken = $request->header('refreshToken');
            $device_id = $request->header('deviceId');
            if($accessToken == null && $device_id == null ){
                $response = [
                    'status' => false,
                    'message' => 'The access token or device id in header is required.',
                    'code' => 500,
                    'data' => null, 
                ];
                return response()->json($response, 500);
            }else if($accessToken != null){
                if($refreshToken == null){
                    $response = [
                        'status' => false,
                        'message' => 'The refresh token in header is required.',
                        'code' => 500,
                        'data' => null, 
                    ];
                    return response()->json($response, 500);
                }else{
                    $response_refresh_token = $this->MemberController->get_mdcid(
                        [
                            'accessToken' => $accessToken
                            , 'refreshToken' => $refreshToken
                        ]
                    );
                    // Cek response success
                    if(strtoupper($response_refresh_token['message']) == 'SUCCESS') {
                        $response_refresh_token = (array)$response_refresh_token['data'];
                        $mdcid = $response_refresh_token['mdcId'];
                        $accessToken = $response_refresh_token['accessToken'];
                        $refreshToken = $response_refresh_token['refreshToken'];
                        $reservation = Reservation::get_reservation_mdcid(
                            [
                                'transaction_no' => $request->transaction_no
                                , 'mdcid' => $mdcid
                            ]
                        );
                    }  else {
                        $error = [
                            'modul' => 'edit_member',
                            'actions' => 'Hit Get Reservation mdcid API',
                            'error_log' => json_encode($response_refresh_token),
                            'device' => "0" 
                            ];
                        $report = $this->LogController->error_log($error);
                        $response = [
                            'status' => false,
                            'message' => $response_refresh_token['message'],
                            'code' => 400,
                            'data' => $response_refresh_token
                        ];
                        return response()->json($response, 400);
                    }
                }
            }else if($device_id != null){
                $reservation = Reservation::get_reservation_mac_transNo(
                    [
                        'transaction_no' => $request->transaction_no
                        , 'device_id' => $device_id
                    ]
                );
            }
            // edit : arka.moharifrifai 2022-06-03
            
            // $reservation = Reservation::get_reservation_mdcid($request->all());
            if($reservation == null){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }else{
                
                if($reservation['payment_sts'] != 'paid' && $reservation['pg_transaction_status'] != 'captured'){
                    $configURL = Msystem::where('system_type', 'url')->where('system_cd', 'mpg_inquiries')->first();
                    $url_mpg =$configURL['system_value'];
                    $datahotel = Hotel::where('id',$reservation['hotel_id'])->first();
                    $mpg_apiKey = $datahotel['mpg_api_key'];
                    $payment_status_mpg = $this->payment_status_mpg($url_mpg,$mpg_apiKey,$reservation['mpg_id']);
                    $transaction_status_mpg = $this->transaction_status_mpg($url_mpg,$mpg_apiKey,$reservation['mpg_id']);
                    // dd($transaction_status_mpg);
                    if($transaction_status_mpg != null){
                        $transaction_status = $transaction_status_mpg['status'];
                        $transaction_mpg_code_status = $transaction_status_mpg['mpg_status_code'];
                        if($transaction_status === 'authenticate' && $transaction_mpg_code_status === '-1'){
                          $transaction_status_mpg = 'failed';
                          // dd('ada');
                        }else{
                            $transaction_status_mpg = $transaction_status;
                        }
                    }
                    if($reservation['payment_sts'] != $payment_status_mpg || $transaction_status_mpg != $reservation['pg_transaction_status']){
                        $data = ['transaction_no' => $reservation['transaction_no'],
                                'pg_transaction_status' => $transaction_status_mpg,
                                'payment_sts' => $payment_status_mpg];
                        $update_statusReservations = Reservation::update_status_payment_reservation($data);
                        if($payment_status_mpg =='paid'){
                            $booking_tc = $this->update_booking_engine($reservation['transaction_no']);
                            $check_booking_tc = $this->check_booking_tc($reservation['transaction_no']);
                        }                        
                        $reservation = Reservation::get_reservation($request->all());
                    }
                }               
                
                $guest = Guest::where('id', $reservation['customer_id'])
                                ->first();
                $faicility = HotelFacility::get_facility($reservation['hotel_id']);
                $faicility_hotel = ['hotel_facility' => $faicility];
                if(count($faicility)==0){
                    $faicility_hotel = ['hotel_facility' => null];
                }  
                $url =['url_pdf' => url('/payment_reservation?transaction_no='.$reservation['transaction_no'])];
                // dd($faicility_hotel);
                $reservation = json_decode(json_encode($reservation), true); 
                $reservation = $reservation + $url + $faicility_hotel;
                if($reservation['payment_sts'] == null || $reservation['payment_sts'] == "")
                {
                    $status = 'unpaid';
                    $reservation['status'] = $status;
                }
                else if($reservation['payment_sts'] == 'unpaid' || $reservation['payment_sts'] == 'pending')
                {
                    $status = 'unpaid';
                    $reservation['status'] = $status;
                }
                else if ($reservation['payment_sts']== 'paid')
                {
                    if($reservation['checkin_dt'] <= date('Y-m-d')){
                        $status = 'finish';
                        $reservation['status'] = $status;
                    }
                    if($reservation['checkin_dt'] >= date('Y-m-d')){
                        $status = 'paid';
                        $reservation['status'] = $status;
                    }
                }
                else{
                    $status = 'failed';
                    $reservation['status'] = $status;
                }
            //    dd($reservation);
            
                $getSystem = Msystem::where('system_type', 'reservation')
                ->where('system_cd', 'exp')->first();
                $reservation['reservationExp'] = isset($getSystem['system_value']) ? $getSystem['system_value'] : 10;

                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_found' ),
                    'data' => ['reservation' => $reservation,
                               'guest' => $guest],
                ];
                return response()->json($response, 200);    
                
            }        
        } catch ( \Throwable $e ) {
            report($e);
            $error = ['modul' => 'get_data_reservation',
                'actions' => 'get data reservation hotel',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    /*
	 * Function: Update Booking Engine
	 * @param  string $transaction_no
	 */
	private function check_booking_tc($transaction_no){
        try{
            $reservation = Reservation::where('transaction_no',$transaction_no)->first();
            $guest = Guest::where('id', $reservation['customer_id'])
                                    ->first();
            // $reservation = $reservation['reservation'];
                if(isset($reservation['hotel_id'])) {
            
            $hotel = Hotel::where('id',$reservation['hotel_id'])->first();
        
            }else{
                $hotel = null;
            }
            if(!isset($reservation['hotel_id']) && !$hotel) {
                return false;
            }
            $getTcLoginURL = Msystem::where('system_type', 'tc_url')
                                ->where('system_cd', 'oauth')->get();
            if(!empty($getTcLoginURL)){
                foreach($getTcLoginURL as $d)
                {
                    if($d['system_type'] === 'tc_url' && $d['system_cd'] === 'oauth'){
                    $tcLoginURL = $d['system_value'];
                    }
                }
            }else{
                return;
            }
            
                $oauthHeader = base64_encode($hotel['be_api_key'].':'.$hotel['be_secret_key']);
                
            $headers = [
                    'Authorization' => 'Basic '.$oauthHeader,
                    'Content-Type' => 'application/json'
            ];
                $client = new Client();    
                $response = $client->request('POST', $tcLoginURL,[
                                            'verify' => false,
                                            'headers' => $headers,
                                            '/delay/5',
                                                // ['connect_timeout' => 3.14]
                                            ]);
            // dd($response);
            if(isset($response) && $response->getStatusCode() == 200) {
                
                $body = $response->getBody();
                $jwt = json_decode($body, true);
            }else{
                $jwt = null;
            }
            if(!$jwt) {
                return false;
            }
            $reservationURL = Msystem::where('system_type', 'tc_url')
                            ->where('system_cd', 'reservation')->first();

            $reservationURL['system_value'] = str_replace('{hotelCode}', $reservation['be_hotel_id'], $reservationURL['system_value']);
            $reservationURL['system_value'] = str_replace('{be_uniqueId}', $reservation['be_uniqueId'], $reservationURL['system_value']);
            $reservationURL['system_value'] = str_replace('{guest_full_name}', $guest['guest_full_name'], $reservationURL['system_value']);
            $headers = [
                    'Authorization' => 'Bearer '.$jwt['access_token'],
                    'Content-Type' => 'application/json'
            ];

            $response = $client->request('GET', $reservationURL['system_value'],[
            'verify' => false,
            'headers' => $headers,

            ]);
            if($response->getStatusCode() == 200) {
                return;
            }else{
                $dataMail = ['transaction_no' => $reservation['transaction_no']];
		        $this->send_email_notification($dataMail);
                return;
            }

        } catch ( \Throwable $e ) {
            report($e);
            $error = ['modul' => 'get_data_reservation',
                'actions' => 'get data reservation hotel',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }
	

    /*
	 * Function: Update Booking Engine
	 * @param  string $transaction_no
	 */
	private function update_booking_engine($transaction_no)
	{
    
        $reservation = Reservation::where('transaction_no',$transaction_no)->first();
        $guest = Guest::where('id', $reservation['customer_id'])
                                ->first();
        // $reservation = $reservation['reservation'];
            if(isset($reservation['hotel_id'])) {
        
        $hotel = Hotel::where('id',$reservation['hotel_id'])->first();
        
            }else{
                $hotel = null;
            }
            if(!isset($reservation['hotel_id']) && !$hotel) {
        
                // $this->logs('--update booking engine failed--\n');
                // $this->logs('--Error: Reservation hotel id not valid--\n');
                return false;
            }
            //make request to TravelClick Login API 
        // $tcLoginURL = $this->get_tc_url_login();
        $getTcLoginURL = Msystem::where('system_type', 'tc_url')
                            ->where('system_cd', 'oauth')->get();
        if(!empty($getTcLoginURL)){
            foreach($getTcLoginURL as $d)
            {
                if($d['system_type'] === 'tc_url' && $d['system_cd'] === 'oauth'){
                $tcLoginURL = $d['system_value'];
                }
            }
        }else{
            return;
        }
        
            $oauthHeader = base64_encode($hotel['be_api_key'].':'.$hotel['be_secret_key']);
            
        $headers = [
                'Authorization' => 'Basic '.$oauthHeader,
                'Content-Type' => 'application/json'
        ];
        // dd($tcLoginURL);
            $client = new Client();    
            $response = $client->request('POST', $tcLoginURL,[
                                        'verify' => false,
                                        'headers' => $headers,
                                        '/delay/5',
                                            // ['connect_timeout' => 3.14]
                                        ]);
        // dd($response);
        if(isset($response) && $response->getStatusCode() == 200) {
            
            $body = $response->getBody();
            $jwt = json_decode($body, true);
        }else{
            $jwt = null;
        }
        // dd($jwt);
        //LOG DI MATIIN DULU
            // $this->logs('--JWT auth response--\n');
            // $this->logs($body);
            
            if(!$jwt) {
        //LOG DI MATIIN DULU
                // $this->logs('--Login oauth to booking engine failed--\n');
                return false;
            }
            
            //get Language code
            // $languageCode = $this->get_msystem_by_system_cd_type('language','code');
            $languageCode = Msystem::where('system_type', 'language')
                                    ->where('system_cd', 'code')->get();
        //build comments data
            $comments = "guestReq: reservations.spesial_req | paymentInfo: reservations.transaction_no | guest.fullname | reservations.payment_sts";
            $comments = str_replace('reservations.spesial_req', $reservation['special_request'], $comments);
            $comments = str_replace('reservations.transaction_no', $reservation['transaction_no'], $comments);
            $comments = str_replace('guest.fullname', $guest['full_name'], $comments);
            $comments = str_replace('reservations.payment_sts', $reservation['payment_sts'], $comments);
            
            //get payment card informations
        // $paymentCard = $this->get_msystem_by_system_cd('paymentCard');
        $paymentCard = Msystem::where('system_type', 'paymentCard')->get();
		foreach($paymentCard as $pc) {
			if($pc['system_cd'] == 'cardCode') $cardCode = $pc['system_value'];
			if($pc['system_cd'] == 'cardHolderName') $cardHolderName = $pc['system_value'];
			if($pc['system_cd'] == 'cardNumber') $cardNumber = $pc['system_value'];
			if($pc['system_cd'] == 'cardType') $cardType = $pc['system_value'];
			if($pc['system_cd'] == 'expireDate') $expireDate = $pc['system_value'];
		}
		
		$checkin_dt = date_create_from_format("Y-m-d", $reservation['checkin_dt']);
		$checkout_dt = date_create_from_format("Y-m-d", $reservation['checkout_dt']);
  
		///==============================
		$data = array(
			"uniqueId" => $reservation['be_uniqueId'],
			"posSource" => [
				"requestorIds" => []
			],
			"resGlobalInfo" => [
				"comments" => [[
						"comment" => $comments
					]
				],
				"guestCounts" => [[
						"ageQualifyingCode" => "10",
						"count" => $reservation['ttl_adult']
					],
					[
						"ageQualifyingCode" => "8",
						"count" => $reservation['ttl_children']
					]
				],
				"guaranteesAccepted" => [[
						"paymentCard" => [
							"cardCode" => $cardCode,
							"cardHolderInfoRequired" => false,
							"cardHolderName" => $cardHolderName,
							"cardNumber" => $cardNumber,
							"cardType" => $cardType,
							"expireDate" => $expireDate
						]
					]
				],
				"timeSpan" => [
					"start" =>  $checkin_dt->format('Y-m-d'),
					"end" => $checkout_dt->format('Y-m-d'),
					"duration" => $reservation['duration']
				]
			],
			"resGuests" => [[
					"profile" => [
						"customer" => [
							"givenName" => $guest['guest_full_name'],
							"surName"=> $guest['guest_full_name'],
							"telephone" => [[
									"phoneUseType" => "1",
									"phoneNumber" => $guest['guest_phone']
								]
							],
							"email" => $guest['guest_email'],
							"address" => [[
									"useType" => "1",
									"countryCode" => $guest['guest_country'],
									"stateName" => $guest['guest_state_province'],
									"cityName" => $guest['guest_city'],
									"postalCode" => $guest['guest_postal_cd'],
									"addressLine1" => $guest['guest_address']
								]
							]
						]
					]
				]
			],
			"roomStays" => [[
					"ratePlans" => [[
							"ratePlanCode" => $reservation['be_rate_plan_code'],
							"ratePlanType" => $reservation['be_rate_plan_type']
						]
					],
					"roomRates" => [[
							"roomTypeCode" => $reservation['be_room_id'],
							"numberOfUnits" => $reservation['ttl_room']
						]
					]
				]
			],
			"reservationStatus" => $reservation['be_reservationstatus'],
			"selected" => true
		);
		///==============================
		
    //DI MATIIN DULU
		// $this->logs('--Dump post data to TravelClick--\n'); 
		// $this->logs($data); 
		
		//get reservation url
    $reservationURL = Msystem::where('system_type', 'tc_url')
                            ->where('system_cd', 'reservation')->first();
        
    $reservationURL['system_value'] = str_replace('{hotelCode}', $reservation['be_hotel_id'], $reservationURL['system_value']);
    // dd($reservationURL['system_value']);
    $headers = [
			'Authorization' => 'Bearer '.$jwt['access_token'],
			'Content-Type' => 'application/json'
    ];

    $response = $client->request('POST', $reservationURL['system_value'],[
      'verify' => false,
      'headers' => $headers,
      'json' => $data,

      ]);
      if(isset($response) && $response->getStatusCode() == 200) {
        $body = $response->getBody();
        $response = json_decode($body, true);
        $statusBooking = true;
        // dd($result);
      }else{
        //DI MATIIN DULU
        // $this->logs('--Reservation success--\n'); 
				// $this->logs($response); 
        $statusBooking = false;
        $response = null;
      }
		if($statusBooking) 
		{

      //DI MATIIN DULU
			// if($this->createLogs) {
			// 	$this->logs('--Reservation success--\n'); 
			// 	$this->logs($response); 
			// }

			//update reservations data
			$data = array(
				'be_uniqueId' => $response['uniqueId'],
				'be_reservationstatus' => $response['reservationStatus'],
                'transaction_no' => $reservation['transaction_no']
			);
			$update_statusReservations = Reservation::update_status_payment_reservation($data);
      
        if($update_statusReservations == false){
            $dataMail = ['transaction_no' => $transaction_no];
		    // $this->send_email_notification($dataMail);
        //   $this->send_email_notification($transaction_no);
          //DI MATIIN DULU
          // $this->logs('--Reservation to Booking Engine is failed--\n'); 
          // $this->logs($result); 
          
          return $response;
        }
			if(strtoupper($response['reservationStatus']) !== 'W')
			{
				$dataMail = ['transaction_no' => $transaction_no];
		        // $this->send_email_notification($dataMail);
                
				//DI MATIIN DULU
				// $this->logs('--Reservation to Booking Engine is failed--\n'); 
				// $this->logs($result); 
				
				return $response;
			}
			

      //DI MATIIN DULU
			// if($this->createLogs) {
			// 	$this->logs('--Reservation update data success--\n'); 
			// 	$this->logs($data); 
			// }
			
			return $response;
		}else{
        //DI MATIIN DULU
		// $this->logs('--Post reservation to Booking Engine is failed--\n'); 
		// $this->logs('--Post url: '.$reservationURL.' \n'); 
		// $this->logs($response); 
		$dataMail = ['transaction_no' => $transaction_no];
		// $this->send_email_notification($dataMail);
		
		return $response;
        }

		
	}

    

    /*
	 * Function: Get Transaction Status MPG
	 * @param  URL MPG, API key outlet, inquiry no
	 */
	private function transaction_status_mpg($url_mpg,$mpg_apiKey,$inquiry_no)
	{
    $client = new Client();
    $pgInquiryURL = $url_mpg . '/' . $inquiry_no . '/transactions';
    
      $headers = array(
        "authorization: ".$mpg_apiKey,
        "cache-control: no-cache",
        "content-type: application/json"
      );
      $response = $client->request('GET', $pgInquiryURL,[
        'verify' => false,
        'headers'       => $headers,
        // '/delay/5',
        //   ['connect_timeout' => 3.14]
        ]);
        $code = $response->getStatusCode();
        $body = $response->getBody();
        
        if($code == 200){
          $response = json_decode($body, true);
          if(!empty($response)){
            foreach($response as $data)
            {
                $transaction_status_mpg = ['status' => $data['status'],
                                        'mpg_status_code' => $data['statusCode']];
            }
          }else{
            $transaction_status_mpg = null;
            }

        }
        else{
        $transaction_status_mpg = null;
        }
			return $transaction_status_mpg;
		
	}

    /*
	 * Function: Get Status payment mpg
	 * @param  url mpg, apikey outlet, inquiry no
	 */
	private function payment_status_mpg($url_mpg,$mpg_apiKey,$inquiry_no)
	{
    $client = new Client();
    $pgInquiryURL = $url_mpg . '/' . $inquiry_no;
    
      $headers = array(
        "authorization: ".$mpg_apiKey,
        "cache-control: no-cache",
        "content-type: application/json"
      );
      $response = $client->request('GET', $pgInquiryURL,[
        'verify' => false,
        'headers'       => $headers,
        // '/delay/5',
        //   ['connect_timeout' => 3.14]
        ]);
        $code = $response->getStatusCode();
        $body = $response->getBody();
        
        if($code == 200){
          $response = json_decode($body, true);
        //   dd($response);
          $payment_status_mpg = $response['status'];
        }
        else{
        $payment_status_mpg = null;
        }
			return $payment_status_mpg;
		
	}



    /**
	 * Function: get data reservation
	 * body: param[customer_id]
	 *	$request	: 
	*/
    
    public function get_data_reservation_by_member(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_member' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            if(!empty($request['dtFrom']))
            {
                $validator = Validator::make($request->all(), [
                    'id_member' => 'required|numeric',
                    'dtTo' => 'required',
                    ]);
                if ($validator->fails()) {
                    // return response gagal
                    $response = [
                        'status' => false,
                        'code' => 400 ,
                        'message' => $validator->errors()->first(),
                    ];
                    return response()->json($response, 200);
                }
            }
            if(!empty($request['payment_sts'])){
                if(!empty($request['dtFrom']))
                {
                    $reservation = Reservation::get_reservation_member_date_sts($request->all());
                    // dd($data);
                }else{
                    $reservation = Reservation::get_reservation_member_sts($request->all());
                }
            }else{
                if(!empty($request['dtFrom']))
                {
                    $reservation = Reservation::get_reservation_member_date($request->all());
                    // dd($data);
                }else{
                    $reservation = Reservation::get_reservation_member($request->all());
                }
            }
            
            if(count($reservation)==0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            
            foreach ($reservation as $data) {
                // dd($data['hotel_id']);
                $hotel_image = HotelImages::where('hotel_id',$data['hotel_id'])->first();
                if($hotel_image != null){
                    $hotel_image = $hotel_image['file_name'];
                }else{
                    $hotel_image = null;
                }
                if($data['payment_sts']== 'unpaid' || $data['payment_sts']== 'pending')
                {
                    $status = 'unpaid';
                }
                else if ($data['payment_sts']== 'paid')
                {
                    if($data['checkin_dt'] <= date('Y-m-d')){
                        $status = 'finish';
                    }
                    if($data['checkin_dt'] >= date('Y-m-d')){
                        $status = 'paid';
                    }
                }
                else{
                    $status = 'failed';
                }
                $data = json_decode(json_encode($data), true); 
                
                $getSystem = Msystem::where('system_type', 'reservation')
                    ->where('system_cd', 'exp')
                    ->first();
                
                $data = $data +
                            [
                                'status' => $status,
                                'hotel_image' => $hotel_image,
                                'reservationExp' => isset($getSystem['system_value']) ? $getSystem['system_value'] : 10
                            ];
                $data_reservation[] =$data;
            }
            // dd($data_reservation);
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_found' ),
                'data' => $data_reservation,
            ];
            return response()->json($response, 200);
                    
        } catch ( \Throwable $e ) {
            report($e);
            $error = ['modul' => 'get_data_reservation_member',
                'actions' => 'get data reservation hotel by member',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    public function get_room(){
        try
        {
            $path = storage_path() . "/json/result.json"; // ie: /var/www/laravel/app/storage/json/filename.json
            $json = json_decode(file_get_contents($path), true); 
            
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_found' ),
                'data' => $json,
            ];
            return response()->json($response, 200);
        } catch ( \Throwable $e ) {
            report($e);
            $error = ['modul' => 'get_room',
                'actions' => 'get data room hotel',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    public function send_mail_reservation(Request $request){
        try
        {
            $validator = Validator::make($request->all(), [
                'transaction_no' => 'required',
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            $reservation = Reservation::get_reservation_email($request->all());
            if($reservation == null){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }else{
                $guest = Guest::where('id', $reservation['customer_id'])
                                ->first();
                $faicility = HotelFacility::get_facility($reservation['hotel_id']);
                $faicility_hotel = ['hotel_facility' => $faicility];
                if(count($faicility)==0){
                    $faicility_hotel = ['hotel_facility' => null];
                }  
                // dd($guest);
                // dd($faicility_hotel);
                $subjectPic= Msystem::Where('system_type','email_notification')
                                 ->Where('system_cd','hotel')
                                    ->first();
                $subjectGuest= Msystem::Where('system_type','email_notification')
                                 ->Where('system_cd','guest')
                                    ->first();
                // dd($subjectPic['system_value']);
                // dd();
                $reservation = json_decode(json_encode($reservation), true); 
                $reservation = $reservation + $faicility_hotel;
                $subjectHotel = str_replace("confirmation_no",$reservation['be_uniqueId'],$subjectPic['system_value']);
                $data = ['reservation' => $reservation,
                'guest' => $guest,
                'subjectpic' => $subjectHotel,
                'subjectGuest' => $subjectGuest['system_value']
                ];
                // dd($reservation['email_notification']);
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.email_success' ),
                    'data' => $data,
                ];
                $SendMailUser = Mail::to($guest->email)->send(new SendMailReservation($data));
                $SendMailUser = Mail::to($guest->guest_email)->send(new SendMailReservation($data));
                $SendMailPic = Mail::to($reservation['email_notification'])->send(new SendMailReservationPic($data));
                return response()->json($response, 200);    
                
            }
        } catch ( \Throwable $e ) {
            report($e);
            $error = ['modul' => 'send_mail_reservation',
                'actions' => 'send data mail reservation',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    public function update_status_notif(Request $request){
        try
        {
            $validator = Validator::make($request->all(), [
                'transaction_no' => 'required',
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            $trCode = $this->get_transaction_code($request->transaction_no);
              if(strtoupper($trCode) === 'S')
              {
                $reservation = Reservation::where('transaction_no', $request->transaction_no)->first();
              }
              elseif(strtoupper($trCode) === 'D')
              {
                $reservation = OrderDining::where('transaction_no', $request->transaction_no)->first();
              }
              else
              {
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => __('message.data_update_failed' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
              }
            // dd($reservation);
            if($reservation == null){
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => __('message.data_update_failed' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }else{
                $trCode = $this->get_transaction_code($request->transaction_no);
                if(strtoupper($trCode) === 'S')
                {
                    $reservation = Reservation::update_status_notif($request->transaction_no);
                    if($reservation){
                        $data = Reservation::where('transaction_no', $request->transaction_no)->first();
                    }else{
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => __('message.data_update_failed' ),
                            'data' => null,
                        ];
                        return response()->json($response, 200);
                    }
                }
                elseif(strtoupper($trCode) === 'D')
                {
                    $reservation = OrderDining::update_status_notif($request->transaction_no);
                    if($reservation){
                        $data = OrderDining::where('transaction_no', $request->transaction_no)->first();
                    }else{
                        $response = [
                            'status' => false,
                            'code' => 400,
                            'message' => __('message.data_update_failed' ),
                            'data' => null,
                        ];
                        return response()->json($response, 200);
                    }
                }
                else
                {
                    $response = [
                        'status' => false,
                        'code' => 400,
                        'message' => __('message.data_update_failed' ),
                        'data' => null,
                    ];
                    return response()->json($response, 200);
                }
                
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_update_succcess' ),
                    'data' => $data,
                ];
                return response()->json($response, 200);    
                
            }
        } catch ( \Throwable $e ) {
            report($e);
            $error = ['modul' => 'update notification transaction',
                'actions' => 'update notification transaction',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    public function update_status_hold_at(Request $request){
        try
        {
            $validator = Validator::make($request->all(), [
                'transaction_no' => 'required',
                'hold_at' => 'required'
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            $trCode = $this->get_transaction_code($request->transaction_no);
              if(strtoupper($trCode) === 'S')
              {
                $reservation = Reservation::where('transaction_no', $request->transaction_no)->first();
                if($reservation != null){
                    $reservation = Reservation::update_hold_at($request->all());
                    if($reservation){
                        $response = [
                            'status' => true,
                            'code' => 200,
                            'message' => __('message.data_update_succcess' ),
                            'data' => null,
                        ];
                        return response()->json($response, 200);
                    }
                }else{
                    $response = [
                        'status' => false,
                        'code' => 400,
                        'message' => __('message.data_update_failed' ),
                        'data' => null,
                    ];
                    return response()->json($response, 200);
                }
              }
              else
              {
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => __('message.data_update_failed' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
              }
        
        } catch ( \Throwable $e ) {
            report($e);
            $error = ['modul' => 'update notification transaction',
                'actions' => 'update notification transaction',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

     /*
	 * Function: Get Transaction Code
	 * @param  $transaction_no
	 */
	private function get_transaction_code($transaction_no)
	{
		$tr_no = explode('/', $transaction_no);
		if(count($tr_no) === 3) 
		{
			return substr($tr_no[2], 0, 1);
		}
		else
		{
			return false;
		}
	}

    /**
	 * Function: update data os type Reservation
	 * body: 
	 *	$request	: 
	*/
    public function update_os_type_reservation(Request $request){
        try{
            // dd($request->all()); 
            $validator = Validator::make($request->all(), [
                'transaction_no' => 'required',
                'os_type' => 'required'
            ]);

            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(), 
                    'data' =>null,
                    
                ];
                return response()->json($response, 200);
            }
            $update_order = Reservation::update_os_type_reservation($request->all());
            // dd($update_order);
            if($update_order){
                $data = Reservation::get_reservation($request->all());
                $response = [
                    'status' => true,
                    'message' => __('message.data_saved_success'),
                    'code' => 200,
                    'data' =>$data,
                    ];
                return response()->json($response, 200);
            }
            else{
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => __('message.failed_save_data' ),
                    'data' => null,
                    ];
                return response()->json($response, 200);
            }
        } catch ( \Throwable $e ) {
            report($e);
            $error = ['modul' => 'update_status_transaction_reservation',
                'actions' => 'update status order Reservation',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'code' => 400,
                'message' => __('message.data_not_found' ),
                'data' => null,
            ];
            return response()->json($response, 200);
        }
    }

    /**
	 * Function: update data os type Reservation
	 * body: 
	 *	$request	: 
	*/
    public function update_status_payment_reservation(Request $request){
        try{
            // dd($request->all()); 
            $validator = Validator::make($request->all(), [
                'transaction_no' => 'required',
            ]);

            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => $validator->errors()->first(), 
                    'data' =>null,
                    
                ];
                return response()->json($response, 200);
            }
            $update_order = Reservation::update_status_payment_reservation($request->all());
            // dd($update_order);
            if($update_order){
                $data = Reservation::get_reservation($request->all());
                $response = [
                    'status' => true,
                    'message' => __('message.data_saved_success'),
                    'code' => 200,
                    'data' =>$data,
                    ];
                return response()->json($response, 200);
            }
            else{
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => __('message.failed_save_data' ),
                    'data' => null,
                    ];
                return response()->json($response, 200);
            }
        } catch ( \Throwable $e ) {
            report($e);
            $error = ['modul' => 'update_status_transaction_reservation',
                'actions' => 'update status order Reservation',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'code' => 400,
                'message' => __('message.data_not_found' ),
                'data' => null,
            ];
            return response()->json($response, 200);
        }
    }

    /**
	 * Function: get data reservation by mac address
	 * body: param[mac address]
	 *	$request	: 
	*/
    
    public function get_data_reservation_by_device_id(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_id' => 'required',
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response, 200);
            }
            if(!empty($request['dtFrom']))
            {
                $validator = Validator::make($request->all(), [
                    'device_id' => 'required',
                    'dtTo' => 'required',
                    ]);
                if ($validator->fails()) {
                    // return response gagal
                    $response = [
                        'status' => false,
                        'code' => 400 ,
                        'message' => $validator->errors()->first(),
                    ];
                    return response()->json($response, 200);
                }
            }
            if(!empty($request['payment_sts'])){
                if(!empty($request['dtFrom']))
                {
                    $reservation = Reservation::get_reservation_mac_date_sts($request->all());
                    // dd($data);
                }else{
                    $reservation = Reservation::get_reservation_mac_sts($request->all());
                }
            }else{
                if(!empty($request['dtFrom']))
                {
                    $reservation = Reservation::get_reservation_mac_date($request->all());
                    // dd($data);
                }else{
                    $reservation = Reservation::get_reservation_mac($request->all());
                }
            }
            
            if(count($reservation)==0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            
            foreach ($reservation as $data) {
                $hotel_image = HotelImages::where('hotel_id',$data['hotel_id'])->first();
                if($hotel_image != null){
                    $hotel_image = $hotel_image['file_name'];
                }else{
                    $hotel_image = null;
                }
                if($data['payment_sts'] == 'unpaid' || $data['payment_sts'] == 'pending')
                {
                    $status = 'unpaid';
                }
                else if ($data['payment_sts']== 'paid')
                {
                    if($data['checkin_dt'] <= date('Y-m-d')){
                        $status = 'finish';
                    }
                    if($data['checkin_dt'] >= date('Y-m-d')){
                        $status = 'paid';
                    }
                }
                else{
                    $status = 'failed';
                }
                $data = json_decode(json_encode($data), true); 
                
                $getSystem = Msystem::where('system_type', 'reservation')
                ->where('system_cd', 'exp')->first();
                $reservation['reservationExp'] = isset($getSystem['system_value']) ? $getSystem['system_value'] : 10;
                             
                $data = $data +
                                [
                                    'status' => $status,
                                    'hotel_image' => $hotel_image,
                                    'reservationExp' => isset($getSystem['system_value']) ? $getSystem['system_value'] : 10
                                ];

                $data_reservation[] =$data;
            }
            // dd($data_reservation);
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_found' ),
                'data' => $data_reservation,
            ];
            return response()->json($response, 200);
                    
        } catch ( \Throwable $e ) {
            report($e);
            $error = ['modul' => 'get_data_reservation_mac_address',
                'actions' => 'get data reservation hotel by mac address',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

     /*
	 * Function: get special Request utk List di Payment Page
	 * body: 
	 * $request	:
	 **/
    public function get_system_special_request()
    {
        try{
            $data = Msystem::where('system_type','room')
                            ->first();
            if($data !== null)
                $data = explode(";",$data->system_value);
                
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_found' ),
                'data' => $data,
            ];
            return response()->json($response, 200);
        } catch ( \Throwable $e ) {
            report($e);
            $error = ['modul' => 'get_system_id',
                'actions' => 'get data msystem',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }
}
