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
use App\Models\Reservation;
use Validator;
use GuzzleHttp\Client;



class CommitReserveController extends Controller
{
    public function __construct() {
        // Set header for API Request
        $this->headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'x-api-key' => env('API_KEY'),
          ];
    }

    public function commit_reservation(Request $request)
	{
        //dd($request->post('transaction_no'));

        $transaction_no = $request->post('transaction_no');

        //dd($transaction_no);

		$reservation = Reservation::where('transaction_no', $request->transaction_no)->first();
		if(isset($reservation->hotel_id)) {
			$hotel = Hotel::whereId($reservation->hotel_id)->where('be_hotel_id', $reservation->be_hotel_id)->first();
		}else{
			$hotel = null;
		}
		
        dd($reservation);

		if(!isset($reservation->hotel_id) && !$hotel) {
			$this->logs('--update booking engine failed--\n');
			$this->logs('--Error: Reservation hotel id not valid--\n');
			return false;
		}
		
		//make request to TravelClick Login API 
		$tcLoginURL = Msystem::where('system_type', 'tc_url')->where('system_cd', 'oauth')->first();
		$oauthHeader = base64_encode($hotel->be_api_key.':'.$hotel->be_secret_key);
		
		$auth = Http::withHeaders([
			'Authorization' => 'Basic '.$oauthHeader,
			'Content-Type' => 'application/json'
		])->post($tcLoginURL->system_value);
		
		if($auth->successful()) {
			$jwt = json_decode($auth->responseBody());
		}else{
			$jwt = null;
		}
		
		if(!$jwt) {
			$this->logs('--Login oauth to booking engine failed--\n');
			return false;
		}
		
		//get Language code
		$languageCode = Msystem::where('system_type', 'language')->where('system_cd', 'code')->first();
		
		//get Guest data
		$guest = Guest::whereId($reservation->customer_id)->first();
		
		//build comments data
		$comments = "guestReq: reservations.spesial_req | paymentInfo: reservations.transaction_no | guest.fullname | reservations.payment_sts";
		$comments = str_replace('reservations.spesial_req', $reservation->special_request, $comments);
		$comments = str_replace('reservations.transaction_no', $reservation->transaction_no, $comments);
		$comments = str_replace('guest.fullname', $guest->full_name, $comments);
		$comments = str_replace('reservations.payment_sts', $reservation->payment_sts, $comments);
		
		//get payment card informations
		$paymentCard = Msystem::where('system_type', 'paymentCard')->get();
		foreach($paymentCard as $pc) {
			if($pc->system_cd == 'cardCode') $cardCode = $pc->system_value;
			if($pc->system_cd == 'cardHolderName') $cardHolderName = $pc->system_value;
			if($pc->system_cd == 'cardNumber') $cardNumber = $pc->system_value;
			if($pc->system_cd == 'cardType') $cardType = $pc->system_value;
			if($pc->system_cd == 'expireDate') $expireDate = $pc->system_value;
		}
		
		//preparing data for posting to booking engine		
		$data = array(
			"languageCode" => $languageCode->system_value,
			"uniqueId" => $reservation->be_uniqueId,
			"posSource" => [
				"requestorIds" => []
			],
			"resGlobalInfo" => [
				"comments" => [[
								"comment" => $comments
							]
						],
				"guaranteesAccepted" => [[
						"alternatePayment" => [],
						"paymentCard" => [
							"cardCode" => $cardCode,
							"cardHolderInfoRequired" => false,
							"cardHolderName" => $cardHolderName,
							"cardNumber" => $cardNumber,
							"cardType" => $cardType,
							"expireDate" => $expireDate
						],
						"variantId" => 0
					]
				],
				"guestCounts" => [[
						"ageQualifyingCode" => "10",
						"count" => $reservation->ttl_adult
					]
				],
				"timeSpan" => [
					"duration" => $reservation->duration,
					"start" => $reservation->checkin_dt,
					"end" => $reservation->checkout_dt
				]
			],
			"resGuests" => [[
					"profile" => [
						"customer" => [
							"address" => [[
									"countryCode" => $guest->country,
									"useType" => "1"
								]
							],
							"email" => $guest->email,
							"givenName" => $guest->full_name,
							"namePrefix" => "",
							"surName" => $guest->full_name,
							"telephone" => [[
									"phoneNumber" => $guest->phone,
									"phoneUseType" => "1"
								]
							]
						]
					]
				]
			],
			"reservationStatus" => $reservation->be_reservationstatus,
			"roomStays" => [[
					"depositPayments" => [],
					"ratePlans" => [[
							"ratePlanCode" => $reservation->be_rate_plan_code,
							"ratePlanName" => $reservation->be_rate_plan_name,
							"ratePlanType" => $reservation->be_rate_plan_type
						]
					],
					"roomRates" => [[
							"numberOfUnits" => $reservation->ttl_room,
							"roomTypeCode" => $reservation->be_room_id,
							"roomTypeName" => $reservation->be_room_type_nm
						]
					],
					"total" => [
						"amountAfterTax" => $reservation->be_amountAfterTax,
						"amountAfterTaxRoom" => $reservation->be_amountAfterTaxRoom,
						"amountBeforeTax" => $reservation->price,
						"amountBeforeTaxRoom" => $reservation->be_amountBeforeTaxRoom,
						"amountBeforeTaxServ" => $reservation->be_amountBeforeTaxServ,
						"discount" => $reservation->be_discount,
						"discountIndicator" => $reservation->be_discountIndicator,
						"discountIndicatorRoom" => $reservation->be_discountIndicatorRoom,
						"discountIndicatorServ" => $reservation->be_discountIndicatorServ,
						"discountRoom" => $reservation->be_discountRoom,
						"discountServ" => $reservation->be_discountServ,
						"grossAmountBeforeTax" => $reservation->be_grossAmountBeforeTax,
						"grossAmountBeforeTaxRoom" => $reservation->be_grossAmountBeforeTaxRoom,
						"grossAmountBeforeTaxServ" => $reservation->be_grossAmountBeforeTaxServ,
						"taxAmountTotal" => $reservation->tax
					]
				]
			],
			"selected" => true    
		);
		
		//get reservation url
		$reservationURL = Msystem::where('system_type', 'tc_url')->where('system_cd', 'reservation')->first();
		$reservationURL = str_replace('{hotelCode}', $reservation->hotel_id, $reservationURL->system_value);
		
		$response = Http::withHeaders([
			'Authorization' => 'Bearer '.$jwt->access_token,
			'Content-Type' => 'application/json'
		])->withBody(
			json_encode($data), 'application/json'
		)->post($reservationURL);
		
		
		if($response->successful()) 
		{
			if($this->createLogs) {
				$this->logs('--Reservation success--\n'); 
				$this->logs($response); 
			}
			
			$result = json_decode($response->responseBody());
			
			//update reservations data
			$data = array(
				'be_uniqueId' => $result->uniqueId,
				'be_reservationstatus' => $result->reservationStatus
			);
			
			Reservation::whereId($reservation->id)->update($data);
			
			if($this->createLogs) {
				$this->logs('--Reservation update data success--\n'); 
				$this->logs($data); 
			}
			
			return true;
		}
		
		$this->logs('--Post reservation to Booking Engine is failed--\n'); 
		$this->logs($response); 
		return false;
	}
}
