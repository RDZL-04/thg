<?php
/*
|--------------------------------------------------------------------------
| RequestProposal API Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process Request Proposal / Submit Proposal for MICE
| 
| @author: rangga.muharam@arkamaya.co.id 
| @update: May 20th, 2021
*/

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\LOG\ErorrLogController;
use App\Models\RequestProposal;
use App\Models\Hotel;
use App\Models\Msystem;
use App\Models\Halls;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailRequestProposal;

class RequestProposalController extends Controller
{
     // Construct Class
     public function __construct() {
        $this->LogController = new ErorrLogController;
    }

    /*
	 * Function: save Request Proposal
	 * body: 
	 *	$request	: 
	 **/
    public function save_request_proposal(Request $request)
    {
        try {
            //dd($request->all());
           $validator = Validator::make($request->all(), [
                'hotel_id' => 'required',
                'hall_id' => 'required',
                'full_name' => 'required',
                'email' => 'required|email',
                'capacity' => 'required|numeric|min:0|not_in:0',
                'phone' => 'required',
                'proposed_dt' => 'required|date',
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

            // Cek jika param additional_request tidak dikirim
            if(empty($request['additional_request']))
                $request['additional_request'] = null;

            // Cek jika param category_id tidak dikirim
            if(empty($request->category_id))
                $request['category_id'] = 1;

            // Insert db
            $insert = RequestProposal::add_request_proposal($request->all());
            // $insert = $request->all();

            // Make email notification
            // get dataHotel from hotels table
            $dataHotel = Hotel::where('id', $request->hotel_id)->get();
            // get dataHall from Hall table
            $dataHall = Halls::where('id', $request->hall_id)->get();
            // Get data category from tabel Mice Category
            $category_name = Msystem::where('system_type', 'mice_category')
                                    ->where('system_cd', $request['category_id'])->get();
            // dd($category_name[0]->system_value);
            
            if( ($dataHotel[0]->mice_email != null) && ($category_name[0]->system_value != null) && ($dataHall[0]->name != null)){
                $email = $dataHotel[0]->mice_email;
                $category_name = $category_name[0]->system_value;
                // Construct data
                $data = [
                    'name' => $request->full_name,
                    'email' => $request->email,
                    'phone' =>$request->phone,
                    'hotel_name' =>$dataHotel[0]->name,
                    'hall_name' =>$dataHall[0]->name,
                    'capacity' =>$request->capacity,
                    'proposed_dt' =>$request->proposed_dt,
                    'additional_request' =>$request->additional_request,
                    'category_name' => $category_name
                ];
                $kirim = Mail::to($email)->cc($request->email)->send(new SendMailRequestProposal($data));
            }
            $response = [
                'status' => true,
                'code' => 200,
                'message' => __('message.data_saved_success'),
                'data' => $insert                
            ];
            return response()->json($response, 200);
            
        } 
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'save_request_proposal',
                'actions' => 'Save data Request Proposal',
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
