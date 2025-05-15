<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Http;
use \Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Msystem;
use App\Models\Reservation;
use App\Models\OrderDining;
use App\Models\OrderDiningDetail;
use App\Models\Outlet;
use App\Models\Table;

/*
|--------------------------------------------------------------------------
| Inquiry API Controller
|--------------------------------------------------------------------------
|
| Create Inquiry Request to Payment gateway.
| This hook controller will be hit by Mobile Apps
| 
| @author: arif@arkamaya.co.id 
| @update: November 26, 2021 10:30 am
*/

class InquiryController extends Controller
{

  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function __invoke(Request $request) {
		try {

			//validation check...
			$trCode = $this->get_transaction_code($request->transaction_no);

			$reservation = null;
			$alloCode = null;
			$alloCodeVerifier = null;

			if(
				!empty($request->alloCode) && 
				!empty($request->alloCodeVerifier)
			) {
				$alloCode = $request->alloCode;
				$alloCodeVerifier = $request->alloCodeVerifier;
			} 

			if(strtoupper($trCode) === 'S') {

				$reservation = Reservation::where('transaction_no', $request->transaction_no)
					->first();

			} elseif(strtoupper($trCode) === 'D') {

				$reservation = OrderDining::where('transaction_no', $request->transaction_no)
					->where('deleted_at', null)
					->first();

			} 

			$refUrl = Msystem::where('system_type', 'url')
				->where('system_cd', 'base_web_app')
				->first();

			$pgInquiryURL = Msystem::where('system_type', 'url')
				->where('system_cd', 'mpg_inquiries')
				->first();

			if (!$reservation)
				throw new \Exception(__('message.data_not_found'), 404);

			if (!$refUrl)
				throw new \Exception("URL Referrer empty", 404);

			if (!$reservation)
				throw new \Exception("Endpoint inquiry IPG empty", 404);
			
			//ROOM STAY :: GET DATA
			if(strtoupper($trCode) === 'S') {

				$guest = Guest::whereId($reservation->customer_id)
					->first();

				$api = Hotel::whereId($reservation->hotel_id)
					->where('be_hotel_id', $reservation->be_hotel_id)
					->first();
				
				if(!$guest || !$api) 
					throw new \Exception(__('inquiry.invalid_guest'), 404);

				$items = [
					[
						"name" => $reservation->be_room_type_nm,
						"quantity" => $reservation->ttl_room,
						"amount" => $reservation->price
					]
				];
				
				$data = array(
					"amount" => $reservation->price,
					"currency" => $reservation->currency,
					"referenceUrl" => $refUrl->system_value.'?transaction_no='.$request->transaction_no,
					"order" => [
						"id" => $reservation->transaction_no,
						"items" => $items,
						// "disablePromo" => true // kurang tau knp dibuat static `true`
						"disablePromo" => false
					],
					"customer" => [
						"name" => $guest->full_name,
						"email" => $guest->email,
						"phoneNumber" => $guest->phone,
						"country" => $guest->country,
						"postalCode" => $guest->postal_cd
					],
					"paymentSource" => $reservation->payment_source,
					"token" => "csKTePhgyZNTKz2o7Mb6Xw"
				);
				
				$ratePlan = Msystem::where('system_type', 'pg_rate_plan_mega')
					->where('system_value', $reservation->be_rate_plan_code)
					->first();
				
				if($ratePlan) {
					$data['order']['afterDiscount'] = 'creditmega';
				}
				
				if(!empty($request->alloCode) && !empty($request->alloCodeVerifier)) {
					if(strtolower($reservation->os_type) == "web") {
						$data['order']['auth'] = [
							"alloCode" => $alloCode,
							"alloCodeVerifier" => $alloCodeVerifier,
							"alloEquipmentId" => $reservation->equipment_id,
							"alloAppType" => strtolower($reservation->os_type),
						];
					} else {
						$data['order']['auth'] = [
							"alloCode" => $alloCode,
							"alloCodeVerifier" => $alloCodeVerifier,
							"alloOsType" => $reservation->os_type,
							"alloDeviceId" => $reservation->device_id,
							"alloAppType" => "apps",
						];
					}
				}
			}
			
			//DINING :: GET DATA
			if(strtoupper($trCode) === 'D') {

				$guest = Guest::whereId($reservation->customer_id)
						->first();

				$table = Table::where('table_no', $reservation->table_no)
					->where('fboutlet_id', $reservation->fboutlet_id)
					->where('deleted_flag', 0)
					->first();
				
				$api = Outlet::whereId($table->fboutlet_id)->first();

				if(!$guest || !$api) 
					throw new \Exception(__('inquiry.invalid_guest'), 404);

				$details = OrderDiningDetail::select('fb_transaction_details.quantity','fb_transaction_details.amount', 'fboutlet_menus.name')
					->join('fboutlet_menus', 'fboutlet_menus.id','=','fb_transaction_details.fb_menu_id')
					->where('fb_transaction_details.transaction_id', $reservation->id)
					->get();

				$items = [];
				foreach($details as $odd)
				{
					$items[] = [
						'name'		=> $odd->name,
						'quantity'	=> is_null($odd->quantity) ? 1 : $odd->quantity,
						'amount'	=> is_null($odd->amount) ? 0 : round($odd->amount)
					];
				}
														
				$data = [
					"amount" => round($reservation->total_price),
					"currency" => $reservation->currency,
					"referenceUrl" => $refUrl->system_value.'?transaction_no='.$request->transaction_no,
					"order" => [
						"id" => $reservation->transaction_no,
						"items" => $items,
						// "disablePromo" => $reservation->is_member === '1'
						"disablePromo" => false
					],
					"customer" => [
						"name" => $guest->full_name,
						"email" => $guest->email,
						"phoneNumber" => $guest->phone,
						"country" => $guest->country,
						"postalCode" => $guest->postal_cd
					],
					"token" => "csKTePhgyZNTKz2o7Mb6Xw"
				];
			}

			$inquiryHeader = [
				'Authorization' => $api->mpg_api_key,
				'Content-Type' => 'application/json'
			];

			Log::channel('inquiry')->info('================================');
			Log::channel('inquiry')->info('Request inquiry to IPG Start!');
			Log::channel('inquiry')->info('Request header: ' . json_encode($inquiryHeader));
			Log::channel('inquiry')->info('Request body: ' . json_encode($data));
			
			//make request to IPG 
			$response = Http::withHeaders($inquiryHeader)
				->withOptions(["verify"=>false])
				->withBody( json_encode($data), 'application/json' )
				->post($pgInquiryURL->system_value);

			if ($response->failed()) 
				return response()->json(array(
					'status' => false,
					'message' => __('inquiry.inquiry_failed'),
					'code' => 200,
					'data' => json_decode($response->body())
				));

			if($response->successful()) {
				
				//update reservation status on reservation table
				$data = json_decode($response->body());
									
				//ROOM STAY :: GET DATA
				if(strtoupper($trCode) === 'S') {
					$update = array(
						'mpg_id' => $data->id,
						'mpg_url' => json_encode($data->urls),
						'payment_sts' => $data->status
					);
					Reservation::where('transaction_no', $reservation->transaction_no)
						->update($update);
				} elseif(strtoupper($trCode) === 'D') {
					$update = array(
						'mpg_id' => $data->id,
						'mpg_url' => json_encode($data->urls),
						'pg_payment_status' => $data->status
					);
					OrderDining::where('transaction_no', $reservation->transaction_no)
						->update($update);
				}
				
				return response()->json(array(
					'status' => true,
					'message' => __('inquiry.inquiry_success'),
					'code' => 200,
					'data' => $data 
				));
				
			} 
			
		} catch( \Exception $e ) {

			Log::channel('inquiry')->info('Request inquiry failed!');
			Log::channel('inquiry')->info('Request inquiry message: ' . $e->getMessage());
			return response()->json([
				'status' => false,
				'message' => $e->getMessage(),
				'code' => $e->getCode(),
				'data' => [], 
			]);

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
}
