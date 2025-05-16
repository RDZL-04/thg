<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\OutletUsers;
use App\Models\Notification;
use App\Models\OrderDining;
use Auth;
use LanguageSwitcher;
use GuzzleHttp\Client;
use App\Http\Controllers\LOG\ErorrLogController;


class NotifCron extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifWaiters:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron job Notification waites';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->LogController = new ErorrLogController;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

    /*

        |--------------------------------------------------------------------------
        | CronJob Notif payment kasir
        |--------------------------------------------------------------------------
        |
        | Validate,.
        | This controller will process data order dining
        | 
        | @author: ilham.maulana@arkamaya.co.id 
        | @update: July ‎21, ‎2021
        */


    public function handle()
    {
        try {
            $check_transaction = OrderDining::where('payment_progress_sts', 1)->get();
            foreach ($check_transaction as $dining_transaction) {
                if ($dining_transaction['pg_payment_status'] === 'unpaid' || $dining_transaction['pg_payment_status'] == null || $dining_transaction['pg_payment_status'] === "") {
                    if ($dining_transaction['approver_id'] != null) {
                        // echo $dining_transaction['transaction_no'];
                        $check_waiters_online = OutletUsers::where('user_id', $dining_transaction['approver_id'])
                            ->where('fboutlet_id', $dining_transaction['fboutlet_id'])
                            ->first();
                        if ($check_waiters_online['active'] == 1) {
                            $user = User::where('id', $dining_transaction['approver_id'])->first();
                            // dd($user['token']);
                            $message =  __('message.order_pending_desc') . $dining_transaction['table_no'] . __('message.order_with_no') . $dining_transaction['transaction_no'];
                            $title = __('message.order_pending_head');
                            $messagex = ["body" => $message, "title" => $title];
                            $data = [
                                "to" => $user['token'],
                                "priority" => "normal",
                                "content-available" => "true",
                                "collapse_key" => "a",
                                "data" => $dining_transaction,
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
            }
            // $this->info('['.date("y-m-d H:i:s").'] Message sent.');

            //                 $error = ['modul' => 'send notif cronjob ke firebase',
            //                     'actions' => 'send notif ke cronjob firebase',
            //                     'error_log' => '['.date("y-m-d H:i:s").'] Message sent',
            //                     'device' => "0" ];
            //                     $report = $this->LogController->error_log($error);

            // return;
        } catch (Exception $e) {
            report($e);
            $error = [
                'modul' => 'send notif cronjob ke firebase',
                'actions' => 'send notif ke cronjob firebase',
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
}
