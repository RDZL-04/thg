<?php

namespace App\Http\Controllers\WEB;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Msystem;
use App\Models\Reservation;
use App\Models\OrderDining;
use App\Models\Outlet;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use App\Mail\SendMailFailedReservation;

/*
|--------------------------------------------------------------------------
| Webhook API Controller
|--------------------------------------------------------------------------
|
| Validate, Authorize and Capture Credit/Debit Card Transaction.
| This hook controller will be hit by Payment Gateway Server
| 
| @author: arif@arkamaya.co.id 
| @update: November 24, 2020 4:30 pm
*/

class WebhookController extends Controller
{
	public $apiKey = '';
	public $secretKey = '';
	public $urlCapture = 'https://api.dev.megapay.app/inquiry/<inquiry_id>/payments/<transaction_id>/capture';
	public $createLogs = true;
	public $amount = "";

	private $paymentSuccByTrxStatus = [];
	private $paymentSuccByInqStatus = ['paid'];
	private $paymentFailByTrxStatus = ['declined', 'failed'];
	private $paymentFailByInqStatus = ['failed'];

	private $mpgApiKey;
	private $mpgSecret;

	private $tcApiKey;
	private $tcSecret;
	private $oauthHeader;

	private $reqHeaderHost;
	private $reqHeaderSign;
	private $reqHeaderSignStr;
	private $reqHeaderSignStamp;
	private $reqType;
	private $reqSignature;
	private $reqTimestamp;
	private array $reqTrx;
	private array $reqInq;
	// private $reqInqOrder;
	// private $reqInqCustomer;

	private $trxNumber = null;
	private $orderType;

	private $reservation;
	private $resvGuest;
	private $resvHotel;

	private $dinning;
	private $dinnGuest;
	private $dinnOutlet;

	private $respValSign = null;
	private $respDataVal = [
		'status' => 'nok', // 'ok' || 'nok'
		'validateSignature' => null,
		'inquiry' => null,
	];
	private $respDataRec = [
		'status' => 'nack', // 'ack' || 'nack'
		'validateSignature' => null,
	];

	/**
	 * Handle the incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function __invoke(Request $request)
	{
		Log::channel('event')->info('================================');
		Log::channel('event')->info('Request coming!');
		Log::channel('event')->info('Request type: ' . $request->type);
		Log::channel('event')->info('Request trx no: ' . $request->inquiry['order']['id']);
		Log::channel('event')->info('Request header: ' . json_encode($request->headers->all()));
		Log::channel('event')->info('Request body: ' . json_encode($request->all()));

		try {

			$vals = $request->validate([
				'type' => 'required',
				'transaction' => 'required',
				'inquiry' => 'required',
			]);

			if (!$request->hasHeader('Host'))
				throw new \Exception("Header host is required");

			if (!$request->hasHeader('Signature'))
				throw new \Exception("Header signature is required");

			Log::channel('event')->debug('Request success validation.');

			$this->reqHeaderHost = $request->header('Host');
			$this->reqHeaderSign = $request->header('Signature');
			$this->reqType = $vals['type'];
			$this->trxNumber = $vals['inquiry']['order']['id'];
			$this->reqTrx = $vals['transaction'];
			$this->reqInq = $vals['inquiry'];

			Log::channel('event')->debug('Request ready to setup order data.');

			$this->getOrderType();
			$this->setReservation();
			$this->setDinning();

			Log::channel('event')->debug('Request ready to setup credential vendor.');

			$this->genTCAuth();
			$this->genIpgAuth();
			$this->genIPGValSign();

			Log::channel('event')->debug('Request finish to setup depedency data.');

			if ($this->orderType === 'D')
				throw new \Exception("This webhook unsupported for order type dinning, please contact the administrator.");

			Log::channel('event')->debug('Validating is order failed.');

			$this->isFailedTransaction();

			Log::channel('event')->debug('Prepare response request.');

			if ($this->reqType === 'payment.validate')
				return $this->procPaymentValidate();

			if ($this->reqType === 'payment.received')
				return $this->procPaymentReceive();
		} catch (ValidationException $e) {

			Log::channel('event')->info('Found error: ' . $e->validator);
			Log::channel('event')->info('Request json: ' . json_encode($request->all()));
			$resBody = [];

			if ($this->reqType === 'payment.validate')
				$resBody = $this->respDataVal;

			if ($this->reqType === 'payment.received')
				$resBody = $this->respDataRec;

			Log::channel('event')->info('Response body: ' . json_encode($resBody));
			return response()->json($resBody)
				->header('Authorization', $this->mpgApiKey)
				->header('Signature', $this->reqHeaderSignStr)
				->header('Host', $this->reqHeaderHost);
		} catch (\Exception $e) {

			Log::channel('event')->info('Found error: ' . $e->getMessage());
			Log::channel('event')->info('Detail error: ' . json_encode($e));
			Log::channel('event')->info('Request json: ' . json_encode($request->all()));
			$resBody = [];

			if ($this->reqType === 'payment.validate')
				$resBody = $this->respDataVal;

			if ($this->reqType === 'payment.received')
				$resBody = $this->respDataRec;

			Log::channel('event')->info('Response body: ' . json_encode($resBody));
			return response()->json($resBody)
				->header('Authorization', $this->mpgApiKey)
				->header('Signature', $this->reqHeaderSignStr)
				->header('Host', $this->reqHeaderHost);
		}
	}

	/*
	 * Function: Authorized Capture Request
	 * Param: 
	 *	$url	: string
	 *	$data	: array
	 */
	private function capture_request()
	{

		$msys = Msystem::where('system_type', 'url')->where('system_cd', 'mpg_capture')->first();
		$urlCapture = str_replace('<inquiry_id>', $this->reqInq['id'], $msys->system_value);
		$urlCapture = str_replace('<transaction_id>', $this->reqTrx['id'], $urlCapture);

		$data = [
			'authorizationCode' => $this->reqTrx['authorizationCode'],
			'amount' => $this->reqInq['amount'],
			'newAmount' => $this->reqInq['amount']
		];

		$curl = curl_init();
		$postFields = json_encode($data);

		curl_setopt_array($curl, array(
			CURLOPT_URL => $urlCapture,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $postFields,
			CURLOPT_HTTPHEADER => array(
				"authorization: " . $this->apiKey,
				"cache-control: no-cache",
				"content-type: application/json"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return "cURL Error #:" . $err;
		} else {
			return $response;
		}
	}

	/*
	 * Function: Update Reservation Status
	 * @param  string $transaction_no
	 * @param  string $status
	 */
	//private function update_reservation($transaction_no = 'null', $inquiry = 'unpaid', $transaction = 'pending')
	private function update_reservation()
	{
		$payment_status = [
			'payment_sts' => $this->reqInq['status'],
			'pg_transaction_status' => $this->reqTrx['status'],
			'payment_source' => $this->get_payment_source($this->reqInq['id']),
		];

		$updateRes = Reservation::where('transaction_no', $this->trxNumber)
			->update($payment_status);

		if (!$updateRes)
			throw new \Exception("Failed to update reservation to data: " . json_encode($payment_status));
	}

	/*
	 * Function: Update FnB Status
	 * @param  string $transaction_no
	 * @param  string $status
	 */
	//private function update_fnb($transaction_no = 'null', $inquiry = 'unpaid', $transaction = 'pending')
	private function update_fnb()
	{
		// edit by ilham 	
		$progress_sts = '1';

		if ($this->reqInq['status'] === 'paid')
			$progress_sts = '3';

		if (
			$this->reqInq['status'] === 'failed' ||
			in_array(strtolower($this->reqTrx['status']), $this->paymentFailByTrxStatus) ||
			$this->reqTrx['status'] === 'authenticate'
		) $progress_sts = '4';

		$payment_status = [
			'payment_progress_sts' => $progress_sts,
			'pg_payment_status' => $this->reqInq['status'],
			'payment_source' => $this->get_payment_source($this->reqInq['id']),
			'pg_transaction_status' => $this->reqTrx['status']
		];

		$updateDinning = OrderDining::where('transaction_no', $this->trxNumber)
			->update($payment_status);

		if (!$updateDinning)
			throw new \Exception("Failed to update dinning to data: " . json_encode($payment_status));
	}

	/*
	 * Function: Get Payment Source Data from transaction
	 * @param  string $inquiry_no
	 * @param  string $status
	 */
	private function get_payment_source($inquiry_no)
	{
		$configURL = Msystem::where('system_type', 'url')->where('system_cd', 'mpg_inquiries')->first();
		$pgInquiryURL = $configURL->system_value . '/' . $inquiry_no . '/transactions';

		//make request to IPG 
		$response = Http::withHeaders([
			'Authorization' => $this->mpgApiKey,
			'Content-Type' => 'application/json'
		])
			->withOptions(["verify" => false])
			->get($pgInquiryURL);

		$payment_source = 'invalid';

		if ($response->successful()) {

			$data = json_decode($response->body());
			foreach ($data as $d) {
				$payment_source = $d->paymentSource;
			}
		}

		return $payment_source;
	}


	/*
	 * Function: Update Booking Engine
	 * @param  string $transaction_no
	 */
	private function update_booking_engine($transaction_no)
	{
		$reservation = Reservation::where('transaction_no', $transaction_no)->first();
		if (isset($reservation->hotel_id)) {
			$hotel = Hotel::whereId($reservation->hotel_id)->where('be_hotel_id', $reservation->be_hotel_id)->first();
		} else {
			$hotel = null;
		}

		if (!isset($reservation->hotel_id) && !$hotel) {
			return false;
		}

		//make request to TravelClick Login API 
		$tcLoginURL = Msystem::where('system_type', 'tc_url')->where('system_cd', 'oauth')->first();
		$oauthHeader = base64_encode($hotel->be_api_key . ':' . $hotel->be_secret_key);

		$auth = Http::withHeaders([
			'Authorization' => 'Basic ' . $oauthHeader,
			'Content-Type' => 'application/json'
		])
			->withOptions(["verify" => false])
			->post($tcLoginURL->system_value);

		if ($auth->successful()) {
			$jwt = json_decode($auth->body());
		} else {
			$jwt = null;
		}

		if (!$jwt) {
			return false;
		}

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
		foreach ($paymentCard as $pc) {
			if ($pc->system_cd == 'cardCode') $cardCode = $pc->system_value;
			if ($pc->system_cd == 'cardHolderName') $cardHolderName = $pc->system_value;
			if ($pc->system_cd == 'cardNumber') $cardNumber = $pc->system_value;
			if ($pc->system_cd == 'cardType') $cardType = $pc->system_value;
			if ($pc->system_cd == 'expireDate') $expireDate = $pc->system_value;
		}

		$checkin_dt = date_create_from_format("Y-m-d", $reservation->checkin_dt);
		$checkout_dt = date_create_from_format("Y-m-d", $reservation->checkout_dt);

		///==============================
		$data = array(
			"uniqueId" => $reservation->be_uniqueId,
			"posSource" => [
				"requestorIds" => []
			],
			"resGlobalInfo" => [
				"comments" => [
					[
						"comment" => $comments
					]
				],
				"guestCounts" => [
					[
						"ageQualifyingCode" => "10",
						"count" => $reservation->ttl_adult
					],
					[
						"ageQualifyingCode" => "8",
						"count" => $reservation->ttl_children
					]
				],
				"guaranteesAccepted" => [
					[
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
					"duration" => $reservation->duration
				]
			],
			"resGuests" => [
				[
					"profile" => [
						"customer" => [
							"givenName" => $guest->guest_full_name,
							"surName" => $guest->guest_full_name,
							"telephone" => [
								[
									"phoneUseType" => "1",
									"phoneNumber" => $guest->guest_phone
								]
							],
							"email" => $guest->guest_email,
							"address" => [
								[
									"useType" => "1",
									"countryCode" => $guest->guest_country,
									"stateName" => $guest->guest_state_province,
									"cityName" => $guest->guest_city,
									"postalCode" => $guest->guest_postal_cd,
									"addressLine1" => $guest->guest_address
								]
							]
						]
					]
				]
			],
			"roomStays" => [
				[
					"ratePlans" => [
						[
							"ratePlanCode" => $reservation->be_rate_plan_code,
							"ratePlanType" => $reservation->be_rate_plan_type
						]
					],
					"roomRates" => [
						[
							"roomTypeCode" => $reservation->be_room_id,
							"numberOfUnits" => $reservation->ttl_room
						]
					]
				]
			],
			"reservationStatus" => $reservation->be_reservationstatus,
			"selected" => true
		);

		Log::channel('event')->info('Data Param ke TC: ' . json_encode($data));

		//get reservation url
		$reservationURL = Msystem::where('system_type', 'tc_url')->where('system_cd', 'reservation')->first();
		$reservationURL = str_replace('{hotelCode}', $reservation->be_hotel_id, $reservationURL->system_value);

		$response = Http::withHeaders([
			'Authorization' => 'Bearer ' . $jwt->access_token,
			'Content-Type' => 'application/json'
		])
			->withBody(json_encode($data), 'application/json')
			->withOptions(["verify" => false])
			->post($reservationURL);

		if ($response->successful()) {

			$result = json_decode($response->body());
			Log::channel('event')->info('Response body dari TC update status reservasi: ' . json_encode($result));
			//update reservations data
			$data = array(
				'be_uniqueId' => $result->uniqueId,
				'be_reservationstatus' => $result->reservationStatus
			);

			Reservation::whereId($reservation->id)->update($data);

			if (strtoupper($result->reservationStatus) !== 'W') {
				return $response;
			}

			return $response;
		}

		return $response;
	}

	private function send_mail()
	{
		$headers = [
			'x-api-key' => env('API_KEY'),
		];

		$data = ['transaction_no' => $this->trxNumber];

		$client = new Client();
		$response = $client->request('POST', url('api') . '/reservation/send_mail_reservation', [
			'verify' => false,
			'form_params'   => $data,
			'headers'       => $headers
		]);
		$body = $response->getBody();
		$response = json_decode($body, true);

		Log::channel('event')->info('Email Check: ' . json_encode($response));
	}

	private function getOrderType()
	{
		if (!$this->trxNumber)
			throw new \Exception("Transaction number must be exist.");

		$trNo = explode('/', $this->trxNumber);
		if (count($trNo) !== 3)
			throw new \Exception("Invalid transaction number, cannot validate for stay or dinning.");

		$this->orderType = substr($trNo[2], 0, 1);
	}

	private function setReservation()
	{
		if ($this->orderType !== 'S') return;

		if (!$this->trxNumber)
			throw new \Exception("Transaction number must be exist");

		$resv = Reservation::where('transaction_no', $this->trxNumber)->first();
		if (!$resv)
			throw new \Exception("Data reservation not found.");

		$this->reservation = $resv;

		$this->setResvGuest();
		$this->setResvHotel();
	}

	private function setResvGuest()
	{
		if (!$this->reservation)
			throw new \Exception("Reservation data must be exist");

		$guest = Guest::where('id', $this->reservation->customer_id)->first();
		if (!$guest)
			throw new \Exception("Guest for reservation is empty");

		$this->resvGuest = $guest;
	}

	private function setResvHotel()
	{
		if (!$this->reservation)
			throw new \Exception("Reservation data must be exist");

		$hotel = Hotel::whereId($this->reservation->hotel_id)
			->where('be_hotel_id', $this->reservation->be_hotel_id)
			->first();

		if (!$hotel)
			throw new \Exception("Hotel for reservation is empty");

		$this->resvHotel = $hotel;
	}

	private function setDinning()
	{
		if ($this->orderType !== 'D') return;

		if (!$this->trxNumber)
			throw new \Exception("Transaction number must be exist");

		$dinning = OrderDining::where('transaction_no', $this->trxNumber)->first();
		if (!$dinning)
			throw new \Exception("Data dinning not found.");

		$this->dinning = $dinning;

		$this->setDinnGuest();
		$this->setDinnOutlet();
	}

	private function setDinnGuest()
	{
		if (!$this->dinning)
			throw new \Exception("Data dinning not found.");

		$guest = Guest::where('id', $this->dinning->customer_id)->first();
		if (!$guest)
			throw new \Exception("Guest for reservation is empty");

		$this->dinnGuest = $guest;
	}

	private function setDinnOutlet()
	{
		if (!$this->dinning)
			throw new \Exception("Data dinning not found.");

		$outlet = Outlet::whereId($this->dinning->fboutlet_id)->first();
		if (!$outlet)
			throw new \Exception("Data outlet not found.");

		$this->dinnOutlet = $outlet;
	}

	private function genTCAuth()
	{
		$orderPlace = null;
		if ($this->orderType === 'S')
			$orderPlace = $this->resvHotel;

		if ($this->orderType === 'D')
			$orderPlace = $this->dinnOutlet;

		if (
			!$orderPlace ||
			!isset($orderPlace->be_api_key) ||
			!isset($orderPlace->be_secret_key)
		) throw new \Exception("Order place not found, cannot define TC api key and secret key.");

		$this->tcApiKey = $orderPlace->be_api_key;
		$this->tcSecret = $orderPlace->be_secret_key;
		$this->oauthHeader = base64_encode($this->tcApiKey . ':' . $this->tcSecret);
	}

	private function genIpgAuth()
	{
		$orderPlace = null;
		if ($this->orderType === 'S')
			$orderPlace = $this->resvHotel;

		if ($this->orderType === 'D')
			$orderPlace = $this->dinnOutlet;

		if (
			!$orderPlace ||
			!isset($orderPlace->mpg_api_key) ||
			!isset($orderPlace->mpg_secret_key)
		) throw new \Exception("Order place not found, cannot define mpg api & secret key.");

		$this->mpgApiKey = $orderPlace->mpg_api_key;
		$this->mpgSecret = $orderPlace->mpg_secret_key;
	}

	private function genIPGValSign()
	{
		if (!$this->reqHeaderSign)
			throw new \Exception("Request header sign not found.");

		if (!$this->mpgSecret)
			throw new \Exception("MPG Secret not found, please set mpg secret first.");

		$st = explode(';', $this->reqHeaderSign);
		$this->reqHeaderSignStr = $st[0];
		$this->reqHeaderSignStamp = $st[1];
		$this->respValSign = md5($this->mpgSecret . $this->reqHeaderSignStr . $this->reqHeaderSignStamp);

		Log::channel('event')->debug('MPG Secret: ' . $this->mpgSecret);
		Log::channel('event')->debug('Req header sign: ' . $this->reqHeaderSignStr);
		Log::channel('event')->debug('Req header timestamp: ' . $this->reqHeaderSignStamp);

		$this->respDataVal['validateSignature'] = $this->respValSign;
		$this->respDataRec['validateSignature'] = $this->respValSign;
	}

	private function isFailedTransaction()
	{
		if (!in_array(
			strtolower($this->reqTrx['status']),
			$this->paymentFailByTrxStatus
		)) return;

		$param = [
			'transaction_no' => $this->trxNumber
		];

		$resv = Reservation::get_reservation_email($param);
		if (!$resv)
			return;

		$guest = Guest::where('id', $resv['customer_id'])->first();
		$receivers = [
			$resv->email_notification,
			$guest->email,
			$guest->guest_email,
		];
		Mail::to($receivers)->send(new SendMailFailedReservation($this->trxNumber));
	}

	private function consumePoint()
	{
		$orderData = null;
		if ($this->orderType === 'S')
			$orderData = $this->reservation;

		if ($this->orderType === 'D')
			$orderData = $this->dinnOutlet;

		if (!$orderData->allo_point)
			return;

		Log::channel('event')->debug('Reservation will consume point: ' . $orderData->allo_point);

		if (!$orderData->allo_access_token)
			throw new \Exception('Webhook try consume point, but allo access token is empty, skipping consume point.');

		$reqBody = [
			'transactionNo' => $this->genTransNo(),
			'requestData' => [
				'amount' => $orderData->allo_point,
				'accessToken' => $orderData->allo_access_token,
				'externalMerchantId' => '00000002', // tmp static hard code
				'externalMerchantName' => "Trans Hotel Group - TLH", // tmp static hard code
				'orderNo' => $orderData->be_uniqueId,
				'acquirer' => 'BANK_MEGA', // tmp static hard code, blm tau possible pake data IPG?
			]
		];

		Log::channel('event')->debug('Body consume point: ' . json_encode($reqBody));

		$helper = new \App\Http\Controllers\Helpers\MPC\Adapter\ConsumePoint($reqBody);
		$res = $helper->send();

		Log::channel('event')->debug('Finish consume point: ' . $res->getBody()->getContents());
	}

	private function redeemCoupon()
	{
		if (!$this->trxNumber)
			throw new \Exception("Transaction number must be exist.");

		$orderData = null;
		if ($this->orderType === 'S')
			$orderData = $this->reservation;

		if ($this->orderType === 'D')
			$orderData = $this->dinnOutlet;

		if (!$orderData->allo_coupons_number)
			return;

		Log::channel('event')->debug('Reservation will redeem coupon: ' . $orderData->allo_coupons_number);

		if (!$orderData->allo_access_token)
			throw new \Exception('Webhook try redeem coupon, but allo access token is empty, skipping redeem coupon.');

		if (!$orderData->price)
			throw new \Exception("Order amount cannot be empty");

		$reqBody = [
			"transactionNo" => $this->genTransNo(),
			"requestData" => [
				"accessToken" => $orderData->allo_access_token,
				"couponNo" => $orderData->allo_coupons_number,
				"orderNo" => $orderData->be_uniqueId,
				"merchantId" => "00000002", // tmp static hard code
				"merchantName" => "Trans Hotel Group - TLH", // tmp static hard code
				"transactionTime" => (string) (strtotime($orderData->hold_at) * 1000),
				"acquirer" => "BANK_MEGA", // tmp static hard code, blm tau possible pake data IPG?
				"orderAmount" => (string) $orderData->price,
			],
		];

		Log::channel('event')->debug('Body redeem coupon: ' . json_encode($reqBody));

		$helper = new \App\Http\Controllers\Helpers\MPC\Adapter\RedeemCoupon($reqBody);
		$res = $helper->send();

		Log::channel('event')->debug('Finish redeem coupon: ' . $res->getBody()->getContents());
	}

	protected function genTransNo()
	{
		$date = date('ymd');
		$middleNo = 'ARKTHG' . mt_rand(100000000000, 999999999999) . mt_rand(10000000, 99999999);;
		$data = $date . $middleNo;
		return $data;
	}

	private static function isReservationExpired($holdAt)
	{
		$msys = Msystem::where('system_type', 'reservation')
			->where('system_cd', 'exp')
			->first();

		if (!$msys)
			return true;

		$est = $msys->system_value * 60; // satuan menit dikali 60, untuk jadi detik
		$time = strtotime($holdAt) + $est;
		$currTime = time();

		return $currTime > $time;
	}

	private function procPaymentValidate()
	{
		if ($this->reqType != 'payment.validate')
			return;

		if (!$this->trxNumber)
			throw new \Exception("Transaction number must be exist.");

		$orderData = null;
		if ($this->orderType === 'S')
			$orderData = $this->reservation;

		if ($this->orderType === 'D')
			$orderData = $this->dinnOutlet;

		if (!$orderData || !isset($orderData->price))
			throw new \Exception("Order not found, cannot validate order price.");

		if (!$this->reqTrx || !isset($this->reqTrx['amount']))
			throw new \Exception("Request transaction amount is empty, cannot validate order price.");

		if ((int) $orderData->price !== (int) $this->reqTrx['amount'])
			throw new \Exception("Request transaction amount is not equal with order amount.");

		if ($this->orderType === 'S' && !$orderData->hold_at)
			throw new \Exception("Reservation hold reservation is empty.");

		if ($this->orderType === 'S' && self::isReservationExpired($orderData->hold_at))
			throw new \Exception("Reservation is expired, hold reservation on: {$orderData->hold_at}.");

		if ($this->orderType === 'S')
			$this->update_reservation();

		if ($this->orderType === 'D')
			$this->update_fnb();

		$this->respDataVal['status'] = 'ok';
		$this->respDataVal['inquiry'] = $this->reqInq;

		Log::channel('event')->info('Request payment validate success for order number: ' . $this->trxNumber);
		Log::channel('event')->info('Response body: ' . json_encode($this->respDataVal));
		return response()->json($this->respDataVal)
			->header('Authorization', $this->mpgApiKey)
			->header('Signature', $this->reqHeaderSignStr)
			->header('Host', $this->reqHeaderHost);
	}

	private function procPaymentReceive()
	{
		if ($this->reqType != 'payment.received')
			return;

		if (!$this->trxNumber)
			throw new \Exception("Transaction number must be exist.");

		if ($this->orderType === 'S') {
			$this->update_reservation();

			if ($this->reqInq['status'] === 'paid') {

				$this->consumePoint();
				$this->redeemCoupon();
				$this->update_booking_engine($this->trxNumber);
				$this->send_mail();
			}
		}

		if ($this->orderType === 'D') {
			$this->update_fnb();
		}

		$this->respDataRec['status'] = 'ok';

		Log::channel('event')->info('Request payment receive success for order number: ' . $this->trxNumber);
		Log::channel('event')->info('Response body: ' . json_encode($this->respDataRec));
		return response()->json($this->respDataRec)
			->header('Authorization', $this->mpgApiKey)
			->header('Signature', $this->reqHeaderSignStr)
			->header('Host', $this->reqHeaderHost);
	}
}
