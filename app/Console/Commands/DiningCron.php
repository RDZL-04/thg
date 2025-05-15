<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrderDining;
use Auth;
use LanguageSwitcher;
use GuzzleHttp\Client;
use App\Http\Controllers\LOG\ErorrLogController;
use App\Http\Controllers\API\FcmController;

class DiningCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CancelDining:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command cron job cancel dining';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->LogController = new ErorrLogController;
        $this->FcmController = new FcmController;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

      /*
        |--------------------------------------------------------------------------
        | CronJob Dining
        |--------------------------------------------------------------------------
        |
        | Validate,.
        | This controller will process data order dining
        | 
        | @author: ilham.maulana@arkamaya.co.id 
        | @update: Agustus 05, ‎2021
        */
    public function handle()
    {
        try{
            
            $check_transaction = OrderDining::get();
            foreach($check_transaction as $dining_transaction){
                if($dining_transaction['payment_progress_sts'] === '0' or $dining_transaction['payment_progress_sts'] === '1'){
                    $awal  = date_create($dining_transaction['created_at']);
                    $akhir = date_create(); // waktu sekarang
                    $diff  = date_diff( $awal, $akhir );
                    if(($diff->d > 0)){
                        // echo $dining_transaction['id'];
                        
                        $update= OrderDining::where('id', $dining_transaction['id'])
                                ->update([
                                    'payment_progress_sts' => 5,
                                    'status_notification' => 0,
                                    'note' => 'Canceled By System'
                                    ] );
                    $data = OrderDining::where('transaction_no','=', $dining_transaction['transaction_no'])
                                        ->first();
                    // print_r($data['transaction_no']);
                    $sendNotificationUser = $this->FcmController->send_notification_order_user_canceled($data);
                    // dd($sendNotificationUser);
                    }elseif(($diff->d == 0)){
                        if($diff->h >= 3){
                            // echo $dining_transaction['id'];
                            $update= OrderDining::where('id', $dining_transaction['id'])
                                ->update([
                                    'payment_progress_sts' => 5,
                                    'status_notification' => 0,
                                    'note' => 'Canceled By System'
                                    ] );
                                    $data = OrderDining::where('transaction_no','=', $dining_transaction['transaction_no'])
                                    ->first();
                                    // print_r($data['transaction_no']);
                                    $sendNotificationUser = $this->FcmController->send_notification_order_user_canceled($data);
                                    // dd($sendNotificationUser);
                        }
                    }
                }
                
            }
        }catch(Exception $e) {
            report($e);
            $error = ['modul' => 'cron job cancel dining',
                'actions' => 'cron job cancel dining',
                'error_log' => $e,
                'device' => "0" ];
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
