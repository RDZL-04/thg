<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use GuzzleHttp\Client;
use App\Http\Controllers\LOG\ErorrLogController;


class DiningController extends Controller
{
    public function __construct() {
        // Set header for API Request
        $this->headers = [
            'x-api-key' => env('API_KEY'),
          ];
        
         $this->LogController = new ErorrLogController;
      }
    //
    public function generate_pdf_payment(Request $request)
    {
        try
        {
          // dd($request->all());
            // dd($request['transaction_no']);
            $client = new Client(); //GuzzleHttp\Client
              // $response = $client->request('POST', 'http://thg-web/api/hotel/delete_facility',[
              $response = $client->request('GET', url('api').'/dining/get_order_fb?transaction_no='.$request['transaction_no'],[
              'verify' => false,
            //   'form_params'   => $data,
              'headers'       => $this->headers,
              '/delay/5',
               ['connect_timeout' => 3.14]
          ]);
          $body = $response->getBody();
          $response = json_decode($body, true);
          if($response['status'] == true && $response['data'] != null)
          {         
            // dd($response);
            // return view('user.payment-fnb', ['data' => $response['data']]);
            $pdf = PDF::loadView('user.payment-fnb', ['data' => $response['data']])->setPaper('a4', 'potrait'); 
            return $pdf->stream();
          }
          
        }
        catch (Throwable $e) 
        {
            report($e);
            $error = ['modul' => 'generate_pdf_payment',
                'actions' => 'generate file pdf confirmation payment',
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

    public function generate_pdf_report_dining(Request $request)
    {
        try
        {
          // dd($request->all());
            // dd($request['transaction_no']);
            $client = new Client(); //GuzzleHttp\Client
              // $response = $client->request('POST', 'http://thg-web/api/hotel/delete_facility',[
              $response = $client->request('GET', url('api').'/dining/manage/get_order_dining?'.decrypt($request['param']),[
              'verify' => false,
            //   'form_params'   => $data,
              'headers'       => $this->headers,
              '/delay/5',
               ['connect_timeout' => 3.14]
          ]);
          $body = $response->getBody();
          $response = json_decode($body, true);
          if($response['status'] == true && $response['data'] != null)
          {         
            // dd($response);
            // return view('report.report-fnb', ['data' => $response['data']]);
            $pdf = PDF::loadView('report.report-fnb', ['data' => $response['data']])->setPaper('a4', 'potrait'); 
            return $pdf->stream();
          }
          
        }
        catch (Throwable $e) 
        {
            report($e);
            $error = ['modul' => 'generate_pdf_payment',
                'actions' => 'generate file pdf confirmation payment',
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
