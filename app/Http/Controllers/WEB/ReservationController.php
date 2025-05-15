<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use GuzzleHttp\Client;
use App\Http\Controllers\LOG\ErorrLogController;
use App\Http\Controllers\Helpers\Custom\GAfour;
use App\Models\Hotel;

/*
|--------------------------------------------------------------------------
| reservation WEB Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process reservation data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: February 16
*/

class ReservationController extends Controller
{
  // public $createLogs = true;

  public function __construct()
  {
    // Set header for API Request
    $this->headers = [
      'x-api-key' => env('API_KEY'),
    ];
    $this->LogController = new ErorrLogController;
    // set API URL dari APP_ENV
    if (env('APP_ENV') == 'local') {
      $this->url_redirect = env('APP_REDIRECT_URL');
    } elseif (env('APP_ENV') == 'dev') {
      $this->url_redirect = env('APP_REDIRECT_URL_DEV');
    } elseif (env('APP_ENV') == 'prod') {
      $this->url_redirect = env('APP_REDIRECT_URL_PROD');
    }
  }
  //
  public function generate_pdf_payment(Request $request)
  {
    try {
      // dd($request['transaction_no']);
      $client = new Client(); //GuzzleHttp\Client
      // $response = $client->request('POST', 'http://thg-web/api/hotel/delete_facility',[
      $response = $client->request('GET', url('api') . '/reservation/get_reservation_callback?transaction_no=' . $request['transaction_no'], [
        'verify' => false,
        //   'form_params'   => $data,
        'headers'       => $this->headers,
        '/delay/5',
        ['connect_timeout' => 3.14]
      ]);
      $body = $response->getBody();
      $response = json_decode($body, true);
      // dd($response);
      if ($response['status'] == true && $response['data'] != null) {
        // dd($response);
        // return view('user.payment-reservation', ['data' => $response['data']]);
        $pdf = PDF::loadView('user.payment-reservation', ['data' => $response['data']])->setPaper('a4', 'potrait');
        return $pdf->stream();
      }
    } catch (Throwable $e) {
      report($e);
      $error = [
        'modul' => 'generate_pdf_payment',
        'actions' => 'generate pdf file confirmation reservation',
        'error_log' => $e,
        'device' => "0"
      ];
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

  public function payment_finish(Request $request)
  {
    try {
      // dd('ada');
      // dd($request->all());

      if (!empty($request['transaction_no'])) {
        $trCode = $this->get_transaction_code($request->transaction_no);
        if (strtoupper($trCode) === 'S') {
          $url = '/reservation/get_reservation_callback?transaction_no=';
        } elseif (strtoupper($trCode) === 'D') {
          $url = '/dining/get_order_callback?transaction_no=';
        } else {
          $reservation = false;
        }

        //GuzzleHttp\Client
        $client = new Client();
        $response = $client->request('GET', url('api') . $url . $request['transaction_no'], [
          'verify' => false,
          'headers'       => $this->headers,
          '/delay/5',
          ['connect_timeout' => 3.14]
        ]);
        $body = $response->getBody();
        $response_data = json_decode($body, true);

        if (strtoupper($trCode) === 'D') {
          if ($response_data['status'] == true && $response_data['data'] != null) {
            $outlet_tax = $response_data['data']['order']['outlet_tax'];
            $outlet_service = $response_data['data']['order']['outlet_service'];
            if ($response_data['data']['order']['pg_payment_status'] == 'failed') {
              $data = json_decode(json_encode($response_data['data']));
              $data = [
                'order' => $response_data['data']['order'],
                'order_detail' => $response_data['data']['order_detail']
              ];
              $request_res = new Request;
              $request_res->merge([$data]);
              $client = new Client(); //GuzzleHttp\Client
              // $response_new = $client->request('POST', "http://localhost:8002/api".'/dining/order_dining_failed',[
              $response_new = $client->request('POST', url('api') . '/dining/order_dining_failed', [
                'verify' => false,
                'form_params'   => $data,
                'headers'       => $this->headers,
                '/delay/5',
                ['connect_timeout' => 3.14]
              ]);
              $body = $response_new->getBody();
              $response_new = json_decode($body, true);

              if ($response_new['status'] == true) {
                $response_new['data']['order']['outlet_tax'] = $outlet_tax;
                $response_new['data']['order']['outlet_service'] = $outlet_service;
                if ($response_new['data']['order']['os_type'] == "WEB") {
                  return redirect()->away($this->url_redirect . 'mpgcallback?response=' . $response_new['data']['order']['transaction_no']);
                } elseif ($response_new['data']['order']['os_type'] == "MOBILE" || $response_new['data']['order']['os_type'] == "android" || $response_new['data']['order']['os_type'] == "ios") {
                  return view('payment.finish', [
                    'response' => json_encode($response_new)
                  ]);
                } else {
                  return redirect()->away($this->url_redirect . 'error_page?message=' . $response_new['message']);
                }
              } else {
                return redirect()->away($this->url_redirect . 'error_page?message=' . $response_new['message']);
              }
            } else {
              if ($response_data['data']['order']['pg_payment_status'] == 'unpaid') {

                $url_mpg = $this->get_url_mpg();
                $mpg_apiKey = $this->get_api_key_mpg($response_data['data']['order']['fboutlet_id']);
                if ($url_mpg != null && $mpg_apiKey != null) {
                  $payment_status_mpg = $this->payment_status_mpg($url_mpg, $mpg_apiKey, $response_data['data']['order']['mpg_id']);
                  $transaction_status_mpg = $this->transaction_status_mpg($url_mpg, $mpg_apiKey, $response_data['data']['order']['mpg_id']);
                  if ($transaction_status_mpg != null) {
                    $transaction_status = $transaction_status_mpg['status'];
                    $transaction_mpg_code_status = $transaction_status_mpg['mpg_status_code'];
                    if ($transaction_status === 'authenticate' && $transaction_mpg_code_status === '-1') {
                      $transaction_status_mpg = 'failed';
                    } else {
                      $transaction_status_mpg = $transaction_status;
                    }
                  }
                  if ($response_data['data']['order']['pg_payment_status'] != $payment_status_mpg || $transaction_status_mpg != $response_data['data']['order']['pg_transaction_status']) {
                    //update data to paid
                    if ($payment_status_mpg === 'paid') {
                      $progress_status = 3;
                    } else if ($payment_status_mpg === 'failed') {
                      $progress_status = 4;
                    } else {
                      if ($transaction_status_mpg === 'failed' && $transaction_status_mpg === 'declined') {
                        $progress_status = 4;
                      } else {
                        $progress_status = 1;
                      }
                    }

                    $data = [
                      'transaction_no' => $response_data['data']['order']['transaction_no'],
                      'payment_progress_sts' => $progress_status,
                      'pg_payment_status' => $payment_status_mpg,
                      'pg_transaction_status' => $transaction_status_mpg
                    ];

                    $client = new Client(); //GuzzleHttp\Client
                    // $response = $client->request('POST', "http://localhost:8002/api".'/dining/update_status_dining',[
                    $response = $client->request('POST', url('api') . '/dining/update_status_dining', [
                      'verify' => false,
                      'form_params'   => $data,
                      'headers'       => $this->headers,
                      '/delay/5',
                      ['connect_timeout' => 3.14]
                    ]);

                    $body = $response->getBody();
                    $response = json_decode($body, true);
                    if ($response['status']) {
                      $client = new Client();
                      // $response = $client->request('GET', "http://localhost:8002/api".'/dining/get_order_callback?transaction_no='.$request['transaction_no'],[
                      $response = $client->request('GET', url('api') . '/dining/get_order_callback?transaction_no=' . $request['transaction_no'], [
                        // $response = $client->request('GET', url('api').'/reservation/get_reservation?transaction_no='.$request['transaction_no'],[
                        'verify' => false,
                        'headers'       => $this->headers,
                        '/delay/5',
                        ['connect_timeout' => 3.14]
                      ]);
                      $body = $response->getBody();
                      $response_data = json_decode($body, true);
                      if ($response_data['status']) {
                        if ($response_data['data']['order']['os_type'] == "WEB") {
                          return redirect()->away($this->url_redirect . 'mpgcallback?response=' . $request['transaction_no']);
                        } elseif ($response_data['data']['order']['os_type'] == "MOBILE" || $response_data['data']['order']['os_type'] == "android" || $response_data['data']['order']['os_type'] == "ios") {
                          return view('payment.finish', [
                            'response' => json_encode($response_data)
                          ]);
                        }
                      }
                    } else {
                      return redirect()->away($this->url_redirect . 'error_page?message=' . $response_data['message']);
                    }
                  }
                }
              }

              $response = [
                'status' => true,
                'message' => $response_data['message'],
                'code' => 200,
                'data' => $response_data['data']
              ];

              if ($response_data['data']['order']['os_type'] == "WEB") {
                return redirect()->away($this->url_redirect . 'mpgcallback?response=' . $request['transaction_no']);
              } elseif ($response_data['data']['order']['os_type'] == "MOBILE" || $response_data['data']['order']['os_type'] == "android" || $response_data['data']['order']['os_type'] == "ios") {
                return view('payment.finish', [
                  'response' => json_encode($response)
                ]);
              } else {
                return redirect()->away($this->url_redirect . 'error_page?message=' . $response_data['message']);
              }
            }
          }
          return redirect()->away($this->url_redirect . 'error_page?message=' . $response_data['message']);
        } elseif (strtoupper($trCode) === 'S') {
          if ($response_data['data']['reservation']['payment_sts'] === 'unpaid') {
            //check di mpg paid atau tidak
            $url_mpg = $this->get_url_mpg();
            $url_mpg = $this->get_url_mpg();
            // dd($url_mpg);
            $url_mpg = $this->get_url_mpg();
            // dd($url_mpg);
            $mpg_apiKey = $this->get_api_key_mpg_hotel($response_data['data']['reservation']['hotel_id']);
            if ($url_mpg != null && $mpg_apiKey != null) {
              $payment_status_mpg = $this->payment_status_mpg($url_mpg, $mpg_apiKey, $response_data['data']['reservation']['mpg_id']);
              $transaction_status_mpg = $this->transaction_status_mpg($url_mpg, $mpg_apiKey, $response_data['data']['reservation']['mpg_id']);
              if ($transaction_status_mpg != null) {
                $transaction_status = $transaction_status_mpg['status'];
                $transaction_mpg_code_status = $transaction_status_mpg['mpg_status_code'];
                if ($transaction_status === 'authenticate' && $transaction_mpg_code_status === '-1') {
                  $transaction_status_mpg = 'failed';
                } else {
                  $transaction_status_mpg = $transaction_status;
                }
              }

              if ($response_data['data']['reservation']['payment_sts'] != $payment_status_mpg || $transaction_status_mpg != $response_data['data']['reservation']['pg_transaction_status']) {
                //hit booking engine
                $data = [
                  'transaction_no' => $response_data['data']['reservation']['transaction_no'],
                  'pg_transaction_status' => $transaction_status_mpg,
                  'payment_sts' => $payment_status_mpg
                ];

                $update_statusReservations = $this->update_status_stay($data);
                if ($payment_status_mpg === 'paid') {
                  $booking_tc = $this->update_booking_engine($response_data['data']['reservation']['transaction_no']);
                  $check_booking_tc = $this->check_booking_tc($response_data['data']['reservation']['transaction_no']);
                }
                $response = $this->get_data_reservation_stay($response_data['data']['reservation']['transaction_no']);
                if ($response != null) {
                  if ($response['data']['reservation']['os_type'] == "WEB") {
                    return view('payment.finish-web');
                  } elseif ($response_data['data']['reservation']['os_type'] == "MOBILE" || $response_data['data']['reservation']['os_type'] == "android" || $response_data['data']['reservation']['os_type'] == "ios") {
                    return view('payment.finish', [
                      'response' => json_encode($response)
                    ]);
                  }
                }
              }
            }
          }
          $url_mpg = $this->get_url_mpg();
          $mpg_apiKey = $this->get_api_key_mpg_hotel($response_data['data']['reservation']['hotel_id']);
          if ($url_mpg != null && $mpg_apiKey != null) {
            $payment_status_mpg = $this->payment_status_mpg($url_mpg, $mpg_apiKey, $response_data['data']['reservation']['mpg_id']);
            $transaction_status_mpg = $this->transaction_status_mpg($url_mpg, $mpg_apiKey, $response_data['data']['reservation']['mpg_id']);

            if ($transaction_status_mpg != null) {
              $transaction_status = $transaction_status_mpg['status'];
              $transaction_mpg_code_status = $transaction_status_mpg['mpg_status_code'];

              if ($transaction_status === 'authenticate' && $transaction_mpg_code_status === '-1') {
                $transaction_status_mpg = 'failed';
              } else {
                $transaction_status_mpg = $transaction_status;
              }
            }

            if ($response_data['data']['reservation']['payment_sts'] === 'paid' && $payment_status_mpg === 'paid') {
              if ($response_data['data']['reservation']['be_reservationstatus'] === 'P') {

                if ($response_data['data']['reservation']['os_type'] == "WEB") {

                  $hotel = Hotel::where('id', $response_data['data']['reservation']['hotel_id'])->first();

                  $ga4Helper = new GAfour();
                  $ga4Helper->clientId = $response_data['data']['guest']['id'];
                  $ga4Helper->event = 'add_payment_info';
                  $ga4Helper->params = [
                    'payment_type' => $response_data['data']['reservation']['payment_source'],
                    'tax' => $response_data['data']['reservation']['tax'],
                    'value' => $response_data['data']['reservation']['price'],
                    'currency' => $response_data['data']['reservation']['currency'],
                    'coupon' => $response_data['data']['reservation']['be_discountCode'],
                    'items' => [
                      'item_brand' => $hotel['name'],
                      'item_category' => $response_data['data']['reservation']['be_room_type_nm'],
                      'item_id' => $response_data['data']['reservation']['be_hotel_id'],
                      'item_name' => $response_data['data']['reservation']['be_room_type_nm'],
                      'index' => 0,
                      'price' => $response_data['data']['reservation']['price'],
                      'quantity' => $response_data['data']['reservation']['duration'],
                    ],
                  ];
                  $ga4Helper->send();
                }

                // $this->send_email_notification($response_data['data']['reservation']['transaction_no']);
              }
            }
          }
          $response = [
            'status' => true,
            'message' => $response_data['message'],
            'code' => 200,
            'data' => $response_data['data']
          ];
          if ($response_data['data']['reservation']['os_type'] == "WEB") {
            // return redirect()->away($this->url_redirect.'mpgcallback?response='.json_encode($response));
            return view('payment.finish-web');
          } elseif ($response_data['data']['reservation']['os_type'] == "MOBILE" || $response_data['data']['reservation']['os_type'] == "android" || $response_data['data']['reservation']['os_type'] == "ios") {
            return view('payment.finish', ['response' => json_encode($response)]);
          } else {
            return redirect()->away($this->url_redirect . 'error_page?message=' . $response_data['message']);
          }
        } else {
          return redirect()->away($this->url_redirect . 'error_page?message=' . $response_data['message']);
        }
      } else {
        return redirect()->away($this->url_redirect . 'error_page?message=Something Wrong');
      }
    } catch (Throwable $e) {
      report($e);
      $response = [
        'status' => false,
        'message' => __('message.internal_erorr'),
        'code' => 500,
        'data' => null,
      ];
      return response()->json($response, 500);
    }
  }

  private function get_data_reservation_stay($transaction_no)
  {
    //GuzzleHttp\Client
    $client = new Client();
    $response = $client->request('GET', url('api') . '/reservation/get_reservation_callback?transaction_no=' . $transaction_no, [
      'verify' => false,
      'headers'       => $this->headers,
      '/delay/5',
      ['connect_timeout' => 3.14]
    ]);
    $body = $response->getBody();
    $response_data = json_decode($body, true);
    return $response_data;
  }

  /*
	 * Function: Get Transaction Code
	 * @param  $transaction_no
	 */
  private function get_transaction_code($transaction_no)
  {
    $tr_no = explode('/', $transaction_no);
    if (count($tr_no) === 3) {
      return substr($tr_no[2], 0, 1);
    } else {
      return false;
    }
  }

  /*
	 * Function: Get URL MPG
	 * @param  
	 */
  private function get_url_mpg()
  {
    $client = new Client();
    $response = $client->request('GET', url('api') . '/msytem/get_system_type_cd?system_cd=mpg_inquiries', [
      'verify' => false,
      'headers'       => $this->headers,
      // '/delay/5',
      // ['connect_timeout' => 3.14]
    ]);
    $body = $response->getBody();
    $response = json_decode($body, true);
    if ($response['status']) {
      $url = $response['data'][0]['system_value'];
    } else {
      $url = null;
    }
    return $url;
  }

  /*
	 * Function: Get api key outlet
	 * @param  $outlet id
	 */
  private function get_api_key_mpg($request)
  {
    $client = new Client();
    $response = $client->request('GET', url('api') . '/outlet/get_outlet_detail?outlet_id=' . $request, [
      'verify' => false,
      'headers'       => $this->headers,
      // '/delay/5',
      //   ['connect_timeout' => 3.14]
    ]);
    $body = $response->getBody();
    $response = json_decode($body, true);
    if ($response['status']) {
      $mpg_apiKey = $response['data']['mpg_api_key'];
    } else {
      $mpg_apiKey = null;
    }
    return $mpg_apiKey;
  }
  /*
	 * Function: Get Status payment mpg
	 * @param  url mpg, apikey outlet, inquiry no
	 */
  private function payment_status_mpg($url_mpg, $mpg_apiKey, $inquiry_no)
  {
    $client = new Client();
    $pgInquiryURL = $url_mpg . '/' . $inquiry_no . '/transactions';

    $headers = array(
      "Authorization" => $mpg_apiKey,
      "cache-control" => "no-cache",
      "Content-Type" => "application/json"
    );
    $response = $client->request('GET', $pgInquiryURL, [
      'verify' => false,
      'headers'       => $headers,
      // '/delay/5',
      //   ['connect_timeout' => 3.14]
    ]);
    $code = $response->getStatusCode();
    $body = $response->getBody();

    if ($code == 200) {
      $response = json_decode($body, true);
      $payment_status_mpg = $response[0]['status'];
    } else {
      $payment_status_mpg = null;
    }
    return $payment_status_mpg;
  }


  /*
	 * Function: Get Status transaction stay
	 * @param  url mpg, apikey outlet, inquiry no
	 */
  private function update_status_stay($request)
  {
    $client = new Client(); //GuzzleHttp\Client
    $response = $client->request('POST', url('api') . '/reservation/update_status_payment_reservation', [
      'verify' => false,
      'form_params'   => $request,
      'headers'       => $this->headers,
      // '/delay/5',
      //   ['connect_timeout' => 3.14]
    ]);
    $body = $response->getBody();
    $response = json_decode($body, true);
    if ($response['status']) {

      $return = true;
    } else {
      $return = false;
    }
    return $return;
  }

  /*
	 * Function: Get Transaction Status MPG
	 * @param  URL MPG, API key outlet, inquiry no
	 */
  private function transaction_status_mpg($url_mpg, $mpg_apiKey, $inquiry_no)
  {
    $client = new Client();
    $pgInquiryURL = $url_mpg . '/' . $inquiry_no . '/transactions';

    $headers = array(
      "Authorization" => $mpg_apiKey,
      "cache-control" => "no-cache",
      "Content-Type" => "application/json"
    );
    $response = $client->request('GET', $pgInquiryURL, [
      'verify' => false,
      'headers'       => $headers,
      // '/delay/5',
      //   ['connect_timeout' => 3.14]
    ]);
    $code = $response->getStatusCode();
    $body = $response->getBody();

    if ($code == 200) {
      $response = json_decode($body, true);
      if (!empty($response)) {
        foreach ($response as $data) {
          $transaction_status_mpg = [
            'status' => $data['status'],
            'mpg_status_code' => $data['statusCode']
          ];
        }
      } else {
        $transaction_status_mpg = null;
      }
    } else {
      $transaction_status_mpg = null;
    }
    return $transaction_status_mpg;
  }


  /*
	 * Function: Get api key hotel
	 * @param  $outlet id
	 */
  private function get_api_key_mpg_hotel($request)
  {
    $client = new Client();
    $response = $client->request('GET', url('api') . '/hotel/get_hotel_id?id=' . $request, [
      'verify' => false,
      'headers'       => $this->headers,
      // '/delay/5',
      //   ['connect_timeout' => 3.14]
    ]);
    $body = $response->getBody();
    $response = json_decode($body, true);

    if ($response['status']) {

      foreach ($response['data']['data'] as $data) {
        $mpg_apiKey = $data['mpg_api_key'];
      }
    } else {
      $mpg_apiKey = null;
    }
    return $mpg_apiKey;
  }

  /*
	 * Function: Get api key hotel
	 * @param  $outlet id
	 */
  private function get_data_reservation($request)
  {
    $client = new Client();
    $response = $client->request('GET', url('api') . '/reservation/get_reservation_callback?transaction_no=' . $request, [
      'verify' => false,
      'headers'       => $this->headers,
      // '/delay/5',
      //   ['connect_timeout' => 3.14]
    ]);
    $body = $response->getBody();
    $response = json_decode($body, true);

    if ($response['status']) {
      $data = $response['data'];
    } else {
      $data = null;
    }
    return $data;
  }
  /*
	 * Function: Get api key hotel
	 * @param  $outlet id
	 */
  private function get_data_hotel($request)
  {
    $client = new Client();
    $response = $client->request('GET', url('api') . '/hotel/get_hotel_id?id=' . $request, [
      'verify' => false,
      'headers'       => $this->headers,
      '/delay/5',
      ['connect_timeout' => 3.14]
    ]);
    $body = $response->getBody();
    $response = json_decode($body, true);

    if ($response['status']) {

      foreach ($response['data']['data'] as $d) {
        if ($d['id'] == $request) {
          $data = $d;
        }
      }
    } else {
      $data = null;
    }
    return $data;
  }

  /*
	 * Function: Get URL MPG
	 * @param  
	 */
  private function get_tc_url_login()
  {
    $client = new Client();
    $response = $client->request('GET', url('api') . '/msytem/get_system_type_cd?system_type=tc_url&system_cd=oauth', [
      'verify' => false,
      'headers'       => $this->headers,
      // '/delay/5',
      //   ['connect_timeout' => 3.14]
    ]);
    $body = $response->getBody();
    $response = json_decode($body, true);
    if ($response['status']) {
      foreach ($response['data'] as $d) {
        if ($d['system_type'] === 'tc_url' && $d['system_cd'] === 'oauth') {
          $data = $d['system_value'];
        }
      }
    } else {
      $data = null;
    }
    return $data;
  }

  /*
	 * Function: Get data msystem by system type and system cd
	 * @param  
	 */
  private function get_msystem_by_system_cd_type($system_type, $system_cd)
  {
    $client = new Client();
    $response = $client->request('GET', url('api') . '/msytem/get_system_type_cd?system_type=' . $system_type . '&system_cd=' . $system_cd, [
      'verify' => false,
      'headers'       => $this->headers,
      '/delay/5',
      ['connect_timeout' => 3.14]
    ]);
    $body = $response->getBody();
    $response = json_decode($body, true);
    if ($response['status']) {

      if (!empty($response['data'])) {

        foreach ($response['data'] as $d) {
          if ($d['system_type'] === $system_type && $d['system_cd'] === $system_cd) {
            $data = $d;
          }
        }
      } else {
        $data = null;
      }
    } else {
      $data = null;
    }
    return $data;
  }

  /*
	 * Function: Get data msystem by system type 
	 * @param  
	 */
  private function get_msystem_by_system_cd($system_type)
  {

    $client = new Client();
    $response = $client->request('GET', url('api') . '/msytem/get_system_type_cd?system_type=' . $system_type, [
      'verify' => false,
      'headers'       => $this->headers,
      '/delay/5',
      ['connect_timeout' => 3.14]
    ]);
    $body = $response->getBody();
    $response = json_decode($body, true);
    if ($response['status']) {

      if (!empty($response['data'])) {
        $data = $response['data'];
      } else {
        $data = null;
      }
    } else {
      $data = null;
    }
    return $data;
  }
  /*
	 * Function: check Booking Engine
	 * @param  string $transaction_no
	 */
  private function check_booking_tc($transaction_no)
  {
    try {
      $reservation = $this->get_data_reservation($transaction_no);
      $guest = $reservation['guest'];
      $reservation = $reservation['reservation'];
      if (isset($reservation['hotel_id'])) {

        $hotel = $this->get_data_hotel($reservation['hotel_id']);
      } else {
        $hotel = null;
      }
      if (!isset($reservation['hotel_id']) && !$hotel) {

        // $this->logs('--update booking engine failed--\n');
        // $this->logs('--Error: Reservation hotel id not valid--\n');
        return false;
      }
      //make request to TravelClick Login API 
      $tcLoginURL = $this->get_tc_url_login();

      $oauthHeader = base64_encode($hotel['be_api_key'] . ':' . $hotel['be_secret_key']);

      $headers = [
        'Authorization' => 'Basic ' . $oauthHeader,
        'Content-Type' => 'application/json'
      ];
      $client = new Client();
      $response = $client->request('POST', $tcLoginURL, [
        'verify' => false,
        'headers'       => $headers,
        '/delay/5',
        // ['connect_timeout' => 3.14]
      ]);

      if (isset($response) && $response->getStatusCode() == 200) {

        $body = $response->getBody();
        $jwt = json_decode($body, true);
      } else {
        $jwt = null;
      }
      if (!$jwt) {
        return false;
      }
      $reservationURL = $this->get_msystem_by_system_cd_type('tc_url', 'check_reservation');
      $reservationURL['system_value'] = str_replace('{hotelCode}', $reservation['be_hotel_id'], $reservationURL['system_value']);
      $reservationURL['system_value'] = str_replace('{be_uniqueId}', $reservation['be_uniqueId'], $reservationURL['system_value']);
      $reservationURL['system_value'] = str_replace('{guest_full_name}', $guest['guest_full_name'], $reservationURL['system_value']);
      $headers = [
        'Authorization' => 'Bearer ' . $jwt['access_token'],
        'Content-Type' => 'application/json'
      ];

      $response = $client->request('GET', $reservationURL['system_value'], [
        'verify' => false,
        'headers' => $headers,

      ]);
      // dd($response->getStatusCode() == 200);
      if ($response->getStatusCode() == 200) {
        // $body = $response->getBody();
        // $response = json_decode($body, true);
        // $statusBooking = true;
        return;
        // dd($result);
      } else {
        $this->send_email_notification($reservation['transaction_no']);
        return;
      }
    } catch (\Exception $e) {
      $data = array(
        "status" => "nok",
        "message" => "Send email failed with message: " . $e->getMessage()
      );

      // $this->logs($data);

      return false;
    }
  }

  /*
	 * Function: Update Booking Engine
	 * @param  string $transaction_no
	 */
  private function update_booking_engine($transaction_no)
  {

    $reservation = $this->get_data_reservation($transaction_no);
    $guest = $reservation['guest'];
    $reservation = $reservation['reservation'];
    if (isset($reservation['hotel_id'])) {

      $hotel = $this->get_data_hotel($reservation['hotel_id']);
    } else {
      $hotel = null;
    }
    if (!isset($reservation['hotel_id']) && !$hotel) {

      // $this->logs('--update booking engine failed--\n');
      // $this->logs('--Error: Reservation hotel id not valid--\n');
      return false;
    }
    //make request to TravelClick Login API 
    $tcLoginURL = $this->get_tc_url_login();

    $oauthHeader = base64_encode($hotel['be_api_key'] . ':' . $hotel['be_secret_key']);

    $headers = [
      'Authorization' => 'Basic ' . $oauthHeader,
      'Content-Type' => 'application/json'
    ];
    $client = new Client();
    $response = $client->request('POST', $tcLoginURL, [
      'verify' => false,
      'headers'       => $headers,
      '/delay/5',
      // ['connect_timeout' => 3.14]
    ]);

    if (isset($response) && $response->getStatusCode() == 200) {

      $body = $response->getBody();
      $jwt = json_decode($body, true);
    } else {
      $jwt = null;
    }

    //LOG DI MATIIN DULU
    // $this->logs('--JWT auth response--\n');
    // $this->logs($body);

    if (!$jwt) {
      //LOG DI MATIIN DULU
      // $this->logs('--Login oauth to booking engine failed--\n');
      return false;
    }

    //get Language code
    $languageCode = $this->get_msystem_by_system_cd_type('language', 'code');

    //build comments data
    $comments = "guestReq: reservations.spesial_req | paymentInfo: reservations.transaction_no | guest.fullname | reservations.payment_sts";
    $comments = str_replace('reservations.spesial_req', $reservation['special_request'], $comments);
    $comments = str_replace('reservations.transaction_no', $reservation['transaction_no'], $comments);
    $comments = str_replace('guest.fullname', $guest['full_name'], $comments);
    $comments = str_replace('reservations.payment_sts', $reservation['payment_sts'], $comments);

    //get payment card informations
    $paymentCard = $this->get_msystem_by_system_cd('paymentCard');

    foreach ($paymentCard as $pc) {
      if ($pc['system_cd'] == 'cardCode') $cardCode = $pc['system_value'];
      if ($pc['system_cd'] == 'cardHolderName') $cardHolderName = $pc['system_value'];
      if ($pc['system_cd'] == 'cardNumber') $cardNumber = $pc['system_value'];
      if ($pc['system_cd'] == 'cardType') $cardType = $pc['system_value'];
      if ($pc['system_cd'] == 'expireDate') $expireDate = $pc['system_value'];
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
        "comments" => [
          [
            "comment" => $comments
          ]
        ],
        "guestCounts" => [
          [
            "ageQualifyingCode" => "10",
            "count" => $reservation['ttl_adult']
          ],
          [
            "ageQualifyingCode" => "8",
            "count" => $reservation['ttl_children']
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
          "duration" => $reservation['duration']
        ]
      ],
      "resGuests" => [
        [
          "profile" => [
            "customer" => [
              "givenName" => $guest['guest_full_name'],
              "surName" => $guest['guest_full_name'],
              "telephone" => [
                [
                  "phoneUseType" => "1",
                  "phoneNumber" => $guest['guest_phone']
                ]
              ],
              "email" => $guest['guest_email'],
              "address" => [
                [
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
      "roomStays" => [
        [
          "ratePlans" => [
            [
              "ratePlanCode" => $reservation['be_rate_plan_code'],
              "ratePlanType" => $reservation['be_rate_plan_type']
            ]
          ],
          "roomRates" => [
            [
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
    $reservationURL = $this->get_msystem_by_system_cd_type('tc_url', 'reservation');
    $reservationURL['system_value'] = str_replace('{hotelCode}', $reservation['be_hotel_id'], $reservationURL['system_value']);

    $headers = [
      'Authorization' => 'Bearer ' . $jwt['access_token'],
      'Content-Type' => 'application/json'
    ];

    $response = $client->request('POST', $reservationURL['system_value'], [
      'verify' => false,
      'headers' => $headers,
      'json' => $data,

    ]);
    if (isset($response) && $response->getStatusCode() == 200) {
      $body = $response->getBody();
      $response = json_decode($body, true);
      $statusBooking = true;
      // dd($result);
    } else {
      //DI MATIIN DULU
      // $this->logs('--Reservation success--\n'); 
      // $this->logs($response); 
      $statusBooking = false;
      $response = null;
    }
    if ($statusBooking) {

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
      $update_statusReservations = $this->update_status_stay($data);

      if ($update_statusReservations == false) {
        // $this->send_email_notification($transaction_no);

        $error = [
          'modul' => 'update_status_reservations failed',
          'actions' => 'update status reservations failed',
          'error_log' => $e,
          'device' => "0"
        ];
        $report = $this->LogController->error_log($error);
        //DI MATIIN DULU
        // $this->logs('--Reservation to Booking Engine is failed--\n'); 
        // $this->logs($result); 

        return $response;
      }
      if (strtoupper($response['reservationStatus']) !== 'W') {
        $error = [
          'modul' => 'update_status_reservations failed',
          'actions' => 'update status reservations failed',
          'error_log' => $e,
          'device' => "0"
        ];
        $report = $this->LogController->error_log($error);
        // $this->send_email_notification($transaction_no);
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
    } else {
      //DI MATIIN DULU
      // $this->logs('--Post reservation to Booking Engine is failed--\n'); 
      // $this->logs('--Post url: '.$reservationURL.' \n'); 
      // $this->logs($response); 

      // $this->send_email_notification($transaction_no);
      $error = [
        'modul' => 'update_status_reservations failed',
        'actions' => 'update status reservations failed',
        'error_log' => $e,
        'device' => "0"
      ];
      $report = $this->LogController->error_log($error);
      return $response;
    }
  }


  /*
	 * Function: Send email notification about the transaction
	 * @param  string $transaction_no
	 */
  private function send_email_notification($transaction_no)
  {
    if ($transaction_no) {
      // $baseurl = Msystem::where('system_cd', 'url')->where('system_type', 'base')->first();
      // $app = Application::select('api_key')->where('ip_address_whitelist', '*')->first();

      // if($app && $baseurl)
      // {
      try {
        $data = ['transaction_no' => $transaction_no];

        $client = new Client(); //GuzzleHttp\Client
        $response = $client->request('POST', url('api') . '/reservation/send_mail_reservation', [
          'verify' => false,
          'form_params'   => $data,
          'headers'       => $this->headers,
          // 'json' => $data,
          // '/delay/5',
          // ['connect_timeout' => 3.14]
        ]);
        $body = $response->getBody();
        $response = json_decode($body, true);
        if ($response['status']) {
          // dd($response);
          return;
        } else {
          return;
        }
      } catch (\Exception $e) {
        $data = array(
          "status" => "nok",
          "message" => "Send email failed with message: " . $e->getMessage()
        );

        // $this->logs($data);

        return false;
      }
    }
  }
}
