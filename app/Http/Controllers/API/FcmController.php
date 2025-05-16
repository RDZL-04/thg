<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OutletUsers;
use App\Models\Notification;
use App\Models\OrderDining;
use App\Http\Controllers\LOG\ErorrLogController;
use Validator;
use Crypt;
use Auth;
use LanguageSwitcher;
use GuzzleHttp\Client;


/*
|--------------------------------------------------------------------------
| Fcm API Controller
|--------------------------------------------------------------------------
|
| Validate, Authorize user.
| This user controller will control user data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: April 01, 2021
*/


class FcmController extends Controller
{


    // public function __construct(\Illuminate\Http\Request $request)
    // {
    //     $this->request = $request;
    // }
    public function __construct()
    {
        $this->LogController = new ErorrLogController;
    }


    /**
     * Function: Send Notification order masuk to waiters
     * body: id, image
     *	$request	: 
     */
    public function send_notification_order($request)
    {
        try {
            // dd($request['order']['transaction_no']);
            // dd($request['order']['fboutlet_id']);
            if ($request != null) {
                $user_outlet = OutletUsers::get_outlet_user_login($request['order']['fboutlet_id']);
                // dd($user_outlet);
                foreach ($user_outlet as $user) {
                    if ($user['token'] != null) {
                        // echo $user['token'];
                        $messagex = ["body" => __('message.order_arrived') . " " . $request['order']['transaction_no'], "title" => "Dining Order"];
                        $message = ["body" => json_encode($messagex), "title" => "Dining Order", "data" => json_encode($messagex)];
                        $data = [
                            "to" => $user['token'],
                            "priority" => "normal",
                            "content-available" => "true",
                            "collapse_key" => "a",
                            "data" => $request['order'],
                            "notification" => $messagex
                        ];

                        $data = json_encode($data);
                        $header = [
                            'Content-Type' => 'application/json',
                            // 'Authorization' => 'key=',
                            'Authorization' => 'key=',
                        ];
                        $client = new Client(); //GuzzleHttp\Client
                        $response = $client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
                            'verify' => false,
                            'body'   => json_encode(json_decode($data), true),
                            'headers'       => $header,
                            '/delay/5',
                            ['connect_timeout' => 3.14]
                        ]);
                    }
                }
                return;
            }
        } catch (Exception $e) {
            report($e);
            $error = [
                'modul' => 'add_image',
                'actions' => 'add data images user',
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
            return;
        }
    }

    /**
     * Function: Send Notification order masuk to waiters
     * body: id, image
     *	$request	: 
     */
    public function send_notification_approve_order($request)
    {
        try {
            // dd($request['data']['payment_progress_sts']);
            if ($request != null) {
                $user_outlet = OutletUsers::get_outlet_user_login($request['fboutlet_id']);
                // dd($user_outlet);
                foreach ($user_outlet as $user) {
                    if ($user['token'] != null) {
                        if ($user['user_id'] != $request['approver_id']) {
                            if ($request['data']['payment_progress_sts'] == 0) {
                                $message =  __('message.order_approved_by');
                                $title = __('message.order_approved');
                            } else if ($request['data']['payment_progress_sts'] == 1) {
                                $message =  __('message.order_approved_by');
                                $title = __('message.order_approved');
                            } else if ($request['data']['payment_progress_sts'] == 2) {
                                $message =  __('message.order_reject_by');
                                $title = __('message.order_reject');
                            } else if ($request['data']['payment_progress_sts'] == 5) {
                                $message =  __('message.order_cancel_by');
                                $title = __('message.order_cancel');
                            } else if ($request['data']['payment_progress_sts'] == 6) {
                                $message =  __('message.order-done');
                                $title = __('message.order-done');
                                // return;
                            } else if ($request['data']['payment_progress_sts'] == 3) {
                                $message =  __('message.order_approved_by');
                                $title = "Order Paid";
                            } else {
                                $message =  __('message.order_failed_by');
                                $title = __('message.order_failed');
                            }
                            $messagex = ["body" => "Order " . $request['transaction_no'] . " " . $message . " " . $request['approver_name'], "title" => $title];
                            // $message = ["body" => json_encode($messagex),"title" => "dining approve", "data" => json_encode($messagex)];            
                            $data = [
                                "to" => $user['token'],
                                "priority" => "normal",
                                "content-available" => "true",
                                "collapse_key" => "a",
                                "data" => $request['data'],
                                "notification" => $messagex
                            ];
                            $data = json_encode($data);
                            $header = [
                                'Content-Type' => 'application/json',
                                // 'Authorization' => 'key=',
                                'Authorization' => 'key=',
                            ];
                            $client = new Client(); //GuzzleHttp\Client
                            $response = $client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
                                'verify' => false,
                                'body'   => json_encode(json_decode($data), true),
                                'headers'       => $header,
                                '/delay/5',
                                ['connect_timeout' => 3.14]
                            ]);
                        }
                    }
                }
                return;
            }
        } catch (Exception $e) {
            report($e);
            $error = [
                'modul' => 'add_image',
                'actions' => 'add data images user',
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
            return;
        }
    }

    /**
     * Function: Send Notification order masuk to user
     * body: id, image
     *	$request	: 
     */
    public function send_notification_order_user($request)
    {
        try {
            if ($request != null) {
                $order = $request;
                // dd($order['device_id']);
                if ($order['payment_progress_sts'] == 0) {
                    if ($order['approver_id'] == null) {
                        $message = __('message.order') . $order['transaction_no'] . __('message.order_dining_created');
                        $title = __('message.member_order_created');
                    } else {
                        $message = __('message.order') . $order['transaction_no'] . __('message.order_dining_approved');
                        $title = __('message.member_order_approve');
                    }
                } else if ($order['payment_progress_sts'] == 1) {
                    $message = __('message.order') . $order['transaction_no'] . __('message.order_dining_approved');
                    $title = __('message.member_order_approve');
                } else if ($order['payment_progress_sts'] == 2) {
                    $message =  __('message.order') . $order['transaction_no'] . __('message.order_dining_canceled');
                    $title = __('message.member_order_cancel');
                } else if ($order['payment_progress_sts'] == 5) {
                    $message =  __('message.order') . $order['transaction_no'] . __('message.order_dining_canceled');
                    $title =  __('message.member_order_cancel');
                } else if ($order['payment_progress_sts'] == 3) {
                    $message = __('message.order') . $order['transaction_no'] . __('message.order_dining_success');
                    $title = __('message.member_order_success');
                } else if ($order['payment_progress_sts'] == 4) {
                    $message =  __('message.order') . $order['transaction_no'] . __('message.order_dining_failed');
                    $title = __('message.member_order_failed');
                } else if ($order['payment_progress_sts'] == 6) {
                    // $message =  __('message.order').$order['transaction_no'].__('message.order_dining_done');
                    // $title = __('message.member_order_done');
                    return;
                } else {
                    $message =  __('message.order') . $order['transaction_no'] . __('message.order_dining_failed');
                    $title = __('message.member_order_failed');
                }
                $transaction_no = str_replace("/", ",", $order['transaction_no']);
                // dd($transaction_no);
                if (!empty($order['device_id']) || $order['device_id'] != null) {
                    $user = Notification::where('device_id', $order['device_id'])->first();
                    // dd($user['token']);
                    if ($user != null) {
                        // $messagex = ["body" => __('message.order_arrived')." ".$request['order']['transaction_no'],"title" => "Orderan dining anda masuk boss"];


                        $message = ["body" => $message, "title" => $title, "link" => "thg://app/DiningDetail/" . $transaction_no . "/notifications"];
                        $data = [
                            "to" => $user['token'],
                            "priority" => "normal",
                            "content-available" => "true",
                            "collapse_key" => "a",
                            "data" => $order,
                            "notification" => $message
                        ];
                        $data = json_encode($data);
                        $header = [
                            'Content-Type' => 'application/json',
                            // 'Authorization' => 'key=',
                            'Authorization' => 'key=',
                        ];
                        $client = new Client(); //GuzzleHttp\Client
                        $response = $client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
                            'verify' => false,
                            'body'   => json_encode(json_decode($data), true),
                            'headers'       => $header,
                            '/delay/5',
                            ['connect_timeout' => 3.14]
                        ]);
                    }
                }
            }
            return;
        } catch (Exception $e) {
            report($e);
            $error = [
                'modul' => 'send notif ke firebase',
                'actions' => 'send notif ke firebase',
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
            return;
        }
    }

    /**
     * Function: Send Notification order masuk to user
     * body: id, image
     *	$request	: 
     */
    public function send_notification_stay_user($request)
    {
        try {
            // dd($request);
            if ($request != null) {
                $stay = $request;
                // dd($stay['payment_sts']);
                if ($stay['payment_sts'] == "" || $stay['payment_sts'] == null) {
                    $message =  __('message.reservation_number') . $stay['transaction_no'] . __('message.reservation_created');
                    $title = __('message.member_stay_created');
                } else if ($stay['payment_sts'] == 'unpaid') {
                    $awal  = date_create($stay['hold_at']);
                    $akhir = date_create(); // waktu sekarang
                    $diff  = date_diff($awal, $akhir);

                    $hari =  $diff->d;
                    $jam = $diff->h;
                    $menit = $diff->i;
                    $detik = $diff->s;
                    if ($hari == 0 && $jam == 0 && $menit < 10) {
                        $message =  __('message.reservation_number') . $stay['transaction_no'] . __('message.reservation_created');
                        $title = __('message.member_stay_created');
                    } else {
                        $message = __('message.reservation_number') . $stay['transaction_no'] . __('message.reservation_is_cancel');
                        $title = __('message.your_payment_expire');
                    }
                } else if ($stay['payment_sts'] == 'paid') {
                    if ($stay['checkin_dt'] <= date('Y-m-d')) {
                        $message = __('message.reservation_number') . $stay['transaction_no'] . __('message.reservation_finish');
                        $title = __('message.member_stay_finish');
                    } else if ($stay['checkin_dt'] >= date('Y-m-d')) {
                        $message = __('message.reservation_number') . $stay['transaction_no'] . __('message.reservation_is_success');
                        $title = __('message.member_stay_success');
                    }
                } else {
                    $message = __('message.reservation_number') . $stay['transaction_no'] . __('message.reservation_is_cancel');
                    $title = __('message.member_stay_failed');
                }
                $transaction_no = str_replace("/", ",", $stay['transaction_no']);
                if (!empty($stay['device_id']) || $stay['device_id'] != null) {
                    $user = Notification::where('device_id', $stay['device_id'])->first();
                    if ($user != null) {
                        // $messagex = ["body" => __('message.order_arrived')." ".$request['order']['transaction_no'],"title" => "Orderan dining anda masuk boss"];
                        $message = ["body" => $message, "title" => $title, "link" => "thg://app/StayDetail/" . $transaction_no . "/notifications"];
                        $data = [
                            "to" => $user['token'],
                            "priority" => "normal",
                            "content-available" => "true",
                            "collapse_key" => "a",
                            "data" => $stay,
                            "notification" => $message
                        ];
                        $data = json_encode($data);
                        $header = [
                            'Content-Type' => 'application/json',
                            // 'Authorization' => 'key=',
                            'Authorization' => 'key=',
                        ];
                        $client = new Client(); //GuzzleHttp\Client
                        $response = $client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
                            'verify' => false,
                            'body'   => json_encode(json_decode($data), true),
                            'headers'       => $header,
                            '/delay/5',
                            ['connect_timeout' => 3.14]
                        ]);
                    }
                }
            }
            return;
        } catch (Exception $e) {
            report($e);
            $error = [
                'modul' => 'send notif ke firebase',
                'actions' => 'send notif ke firebase',
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
            return;
        }
    }


    /**
     * Function: Send Notification order masuk to user
     * body: id, image
     *	$request	: 
     */
    public function send_notification_order_user_canceled($request)
    {
        try {
            // dd($request);
            if ($request != null) {
                $order = $request;
                if ($order['payment_progress_sts'] == 5) {
                    $message =  __('message.order') . $order['transaction_no'] . __('message.order_dining_canceled');
                    $title =  __('message.member_order_cancel');
                }
                $transaction_no = str_replace("/", ",", $order['transaction_no']);
                // dd($transaction_no);
                if (!empty($order['device_id']) || $order['device_id'] != null) {
                    $user = Notification::where('device_id', $order['device_id'])->first();
                    // dd($user['token']);
                    if ($user != null) {
                        // $messagex = ["body" => __('message.order_arrived')." ".$request['order']['transaction_no'],"title" => "Orderan dining anda masuk boss"];


                        $message = ["body" => $message, "title" => $title, "link" => "thg://app/DiningDetail/" . $transaction_no . "/notifications"];
                        $data = [
                            "to" => $user['token'],
                            "priority" => "normal",
                            "content-available" => "true",
                            "collapse_key" => "a",
                            "data" => $order,
                            "notification" => $message
                        ];
                        $data = json_encode($data);
                        $header = [
                            'Content-Type' => 'application/json',
                            // 'Authorization' => 'key=',
                            'Authorization' => 'key=',
                        ];
                        $client = new Client(); //GuzzleHttp\Client
                        $response = $client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
                            'verify' => false,
                            'body'   => json_encode(json_decode($data), true),
                            'headers'       => $header,
                            '/delay/5',
                            ['connect_timeout' => 3.14]
                        ]);
                        // dd($response);
                    }
                }
            }
            return $response;
        } catch (Exception $e) {
            report($e);
            $error = [
                'modul' => 'send notif ke firebase',
                'actions' => 'send notif ke firebase',
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
            return;
        }
    }


    /**
     * Function: Send Notification order to waiters
     * body: id, image
     *	$request	: 
     */
    public function send_notification_order_waiters($request)
    {
        try {
            // dd($request['data']['payment_progress_sts']);
            if ($request != null) {
                $user = User::where('id', $request['approver_id'])->first();
                // dd($user_outlet);
                // foreach ($user_outlet as $user){
                if ($user['token'] != null) {
                    if ($user['user_id'] != $request['approver_id']) {
                        if ($request['data']['payment_progress_sts'] == 0) {
                            $message =  __('message.order_approved_by');
                            $title = __('message.order_approved');
                        } else if ($request['data']['payment_progress_sts'] == 1) {
                            $message =  __('message.order_approved_by');
                            $title = __('message.order_approved');
                        } else if ($request['data']['payment_progress_sts'] == 2) {
                            $message =  __('message.order_reject_by');
                            $title = __('message.order_reject');
                        } else if ($request['data']['payment_progress_sts'] == 5) {
                            $message =  __('message.order_cancel_by');
                            $title = __('message.order_cancel');
                        } else if ($request['data']['payment_progress_sts'] == 6) {
                            $message =  __('message.order-done');
                            $title = __('message.order-done');
                            // return;
                        } else if ($request['data']['payment_progress_sts'] == 3) {
                            $message =  __('message.order_approved_by');
                            $title = "Order Paid";
                        } else {
                            $message =  __('message.order_failed_by');
                            $title = __('message.order_failed');
                        }
                        $messagex = ["body" => "Order " . $request['transaction_no'] . " " . $message . " " . $request['approver_name'], "title" => $title];
                        // $message = ["body" => json_encode($messagex),"title" => "dining approve", "data" => json_encode($messagex)];            
                        $data = [
                            "to" => $user['token'],
                            "priority" => "normal",
                            "content-available" => "true",
                            "collapse_key" => "a",
                            "data" => $request['data'],
                            "notification" => $messagex
                        ];
                        $data = json_encode($data);
                        $header = [
                            'Content-Type' => 'application/json',
                            // 'Authorization' => 'key=',
                            'Authorization' => 'key=',
                        ];
                        $client = new Client(); //GuzzleHttp\Client
                        $response = $client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
                            'verify' => false,
                            'body'   => json_encode(json_decode($data), true),
                            'headers'       => $header,
                            '/delay/5',
                            ['connect_timeout' => 3.14]
                        ]);
                    }
                    // }
                }
                return;
            }
        } catch (Exception $e) {
            report($e);
            $error = [
                'modul' => 'add_image',
                'actions' => 'add data images user',
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
            return;
        }
    }

    // /**
    //  * Function: Send Notification order masuk to user
    //  * body: id, image
    //  *	$request	: 
    // */

    // public function send_message_unpaid_order_dining(){
    //     try{
    //         $check_transaction = OrderDining::where('payment_progress_sts',1)->get();
    //         // dd($check_transaction);
    //         foreach($check_transaction as $dining_transaction){

    //             if($dining_transaction['pg_payment_status'] === 'unpaid' || $dining_transaction['pg_payment_status'] == null || $dining_transaction['pg_payment_status'] === ""){
    //                 // echo $dining_transaction['transaction_no'];
    //                 // echo '<br>';

    //                 if($dining_transaction['approver_id'] != null){
    //                     echo $dining_transaction['transaction_no'];
    //                     $check_waiters_online = OutletUsers::where('user_id',$dining_transaction['approver_id'])
    //                                                         ->where('fboutlet_id',$dining_transaction['fboutlet_id'])
    //                                                         ->first();
    //                     if($check_waiters_online['active'] == 1){
    //                         $user = User::where('id',$dining_transaction['approver_id'])->first();
    //                         // dd($user['token']);
    //                         $message =  __('message.order_pending_desc').$dining_transaction['table_no'].__('message.order_with_no').$dining_transaction['transaction_no'];
    //                         $title = __('message.order_pending_head');
    //                         $messagex = ["body" => $message,"title" => $title];     
    //                         $data = ["to" => $user['token'],
    //                                 "priority" => "normal",
    //                                 "content-available" => "true",
    //                                 "collapse_key" => "a",
    //                                 "data" => $dining_transaction,
    //                                 "notification" =>$messagex];
    //                         $data = json_encode($data);
    //                         $header = [
    //                                     'Content-Type' => 'application/json',
    //                                     'Authorization' => 'key=',
    //                                 ];
    //                         $client = new Client(); //GuzzleHttp\Client
    //                         $response = $client->request('POST','https://fcm.googleapis.com/fcm/send',[
    //                                                     'verify' => false,
    //                                                     'body'   => json_encode(json_decode($data),true),
    //                                                     'headers'       => $header,
    //                                                     '/delay/5',
    //                                                     ['connect_timeout' => 3.14]
    //                                                     ]);


    //                     }
    //                 }
    //             }              
    //         }
    //         echo date("y-m-d H:i:s").' Message sent.';

    //                         // $error = ['modul' => 'send notif cronjob ke firebase',
    //                         //     'actions' => 'send notif ke cronjob firebase',
    //                         //     'error_log' => '['.date("y-m-d H:i:s").'] Message sent',
    //                         //     'device' => "0" ];
    //                         //     $report = $this->LogController->error_log($error);

    //                         // return;
    //     }catch(Exception $e) {
    //         report($e);
    //         $error = ['modul' => 'send notif cronjob ke firebase',
    //             'actions' => 'send notif ke cronjob firebase',
    //             'error_log' => $e,
    //             'device' => "0" ];
    //             $report = $this->LogController->error_log($error);
    //         $response = [
    //             'status' => false,
    //             'message' => __('message.internal_erorr'),
    //             'code' => 500,
    //             'data' => null, 
    //         ];
    //         return;
    //     }
    // }


}
