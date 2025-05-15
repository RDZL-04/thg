<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\OrderDining;
use App\Models\Reservation;
use App\Models\Members;
use App\Http\Controllers\LOG\ErorrLogController;
use Validator;

/*
|--------------------------------------------------------------------------
| Review Hotel API Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process data review hotel
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: january 07 2021 
*/

class ReviewController extends Controller
{
    public function __construct() {
        $this->LogController = new ErorrLogController;
    }

    /*
	 * Function: get data review 
	 * body: 
	 *	$request	: string name
	 **/
    public function get_review_hotel(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric',
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
            //get data ranting hotel
            $get_ranting = Review::get_ranting($request['id']);
            //get data top review hotel
            $get_top_review = Review::get_top_review($request['id']);
            $rating = $get_ranting['0'];
                if(empty($get_top_review)){
                    $get_top_review=null;
                }
                else if(empty($get_ranting)){
                    $get_ranting=null;
                }
                   /**
                    * send response data rating, top Review
                    */
             $response = [
                       'status' => true,
                       'code' => 200,
                       'message' => __('message.data_found' ),
                       'data' => ['rating' => $rating->rating,
                                  'top_review' => $get_top_review,],
                   ];
            return response()->json($response, 200);      
           }
           catch (Throwable $e) {
               report($e);
               $error = ['modul' => 'get_review_hotel',
                'actions' => 'get data review hotel',
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
	 * Function: get data review hotel
	 * body: 
	 *	$request	: string name
	 **/
    public function get_review_hotel_all(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'hotel_id' => 'required|numeric',
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
                    'hotel_id' => 'required|numeric',
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
            //get data ranting hotel
            if(!empty($request['dtFrom']))
            {
                $get_review = Review::get_review_by_date($request->all());
                // dd($data);
            }
            else
            {
                $get_review = Review::get_review($request['hotel_id']);
            }
            
            // dd($get_review);
            //get data top review hotel
            if(count($get_review)==0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            $response = [
                       'status' => true,
                       'code' => 200,
                       'message' => __('message.data_found' ),
                       'data' => $get_review,
                   ];
            return response()->json($response, 200);      
           }
           catch (Throwable $e) {
               report($e);
               $error = ['modul' => 'get_review_hotel_all',
                'actions' => 'get data review hotel',
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
	 * Function: get data review hotel
	 * body: 
	 *	$request	: string name
	 **/
    public function get_review_hotel_detail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric',
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
            //get data ranting hotel
                $get_review = Review::get_review_detail($request['id']);
            if(empty($get_review)){
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            $response = [
                       'status' => true,
                       'code' => 200,
                       'message' => __('message.data_found' ),
                       'data' => $get_review,
                   ];
            return response()->json($response, 200);      
           }
           catch (Throwable $e) {
               report($e);
               $error = ['modul' => 'get_review_hotel_detail',
                'actions' => 'get data review hotel',
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
	 * Function: get data transaction reservation
	 * body: 
	 *	$request	: string name
	 **/
    public function get_review_reservation(Request $request)
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
            //get data ranting hotel
                $get_review = Review::where('transaction_no',$request->transaction_no)->first();
            if(empty($get_review)){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            $response = [
                       'status' => true,
                       'code' => 200,
                       'message' => __('message.data_found' ),
                       'data' => $get_review,
                   ];
            return response()->json($response, 200);      
           }
           catch (Throwable $e) {
               report($e);
               $error = ['modul' => 'get_review_transaction_reservation',
                'actions' => 'get data review transaction reservation',
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
	 * Function: add data Review
	 * body: id_customer, id_hotel,review_type,ranting,comment
	 *	$request	: 
	*/
    
    public function add_review_hotel(Request $request)
    {
        try{
        //validasi data request
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
                'transaction_no' => 'required',
                'review_type' => 'required',
                'rating_number' => 'numeric',
                // 'comment' => 'required',
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
            // dd($request->all());
            $transaction_type = explode('/', $request->transaction_no);
            $transaction_type = substr($transaction_type[2], 0, 1);
            if(strtoupper($transaction_type) === 'S')
			{
				$transaction = Reservation::where('transaction_no', $request->transaction_no)->first();

			}
			elseif(strtoupper($transaction_type) === 'D')
			{
                $transaction = OrderDining::where('transaction_no', $request->transaction_no)->first();
            }
            if($transaction == null){
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => 'invalid transaction no', 
                    'data' =>null,
                    
                ];
                return response()->json($response, 200);
            }
            $check_user = Members::where('id', $request->customer_id)->first();
            if($check_user == null){
                $response = [
                    'status' => false,
                    'code' => 400,
                    'message' => 'invalid customer id', 
                    'data' =>null,
                    
                ];
                return response()->json($response, 200);
            }
            // dd($transaction);
            if(empty($request['comment'])){
                $request['comment'] = null;
            }
                $save= Review::add_review($request->all());
                if($save){
                        $response = [
                            'status' => true,
                            'message' => __('message.data_saved_success'),
                            'code' => 200,
                            'data' =>$save,
                            ];
                            return response()->json($response, 200);   
                        }
                else{
                $response = [
                            'status' => false,
                            'message' => __('message.failed_save_data'),
                            'code' => 200,
                            'data' => null, 
                            ];
                return response()->json($response, 200);
            }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'add_review_hotel',
                'actions' => 'save data review hotel',
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
	 * Function: add data Review
	 * body: id_customer, id_hotel,review_type,ranting,comment
	 *	$request	: 
	*/
    
    public function edit_review_hotel(Request $request)
    {
        try{
        //validasi data request
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
                'transaction_no' => 'required',
                'review_type' => 'required',
                'rating_number' => 'required|numeric',
                'id' => 'required|numeric',
                // 'comment' => 'required',
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
            
            if(empty($request['comment'])){
                $request['comment'] = null;
            }
                $save= Review::edit_review($request->all());
                if($save){
                        $response = [
                            'status' => true,
                            'message' => __('message.data_saved_success'),
                            'code' => 200,
                            'data' =>$save,
                            ];
                            return response()->json($response, 200);   
                        }
                else{
                $response = [
                            'status' => false,
                            'message' => __('message.failed_save_data'),
                            'code' => 200,
                            'data' => null, 
                            ];
                return response()->json($response, 200);
            }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'add_review_hotel',
                'actions' => 'save data review hotel',
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
	 * Function: get data review hotel per user 
	 * body: 
	 *	$request	: string name
	 **/
    public function get_review_user(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
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
            if(!empty($request['startDate']))
            {
                $validator = Validator::make($request->all(), [
                    'user_id' => 'required|numeric',
                    'endDate' => 'required',
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
            //get data ranting hotel
            if(!empty($request['startDate']))
            {
                $get_review = Review::get_review_user_by_date($request->all());
            }
            else
            {
                $get_review = Review::get_review_user($request);
            }
            
            // Check if record exists
            if(count($get_review)==0){
                $response = [
                    'status' => true,
                    'code' => 200,
                    'message' => __('message.data_not_found' ),
                    'data' => null,
                ];
                return response()->json($response, 200);
            }
            $response = [
                       'status' => true,
                       'code' => 200,
                       'message' => __('message.data_found' ),
                       'data' => $get_review,
                   ];
            return response()->json($response, 200);      
           }
           catch (Throwable $e) {
               report($e);
               $error = ['modul' => 'get_review_user',
                'actions' => 'get data review user',
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
