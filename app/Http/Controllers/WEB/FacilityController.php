<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Validator;
use App\Http\Controllers\LOG\ErorrLogController;
use Auth;

/*
|--------------------------------------------------------------------------
| Facility WEB Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process facility data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: December 21 2020
*/

class FacilityController extends Controller
{
  public function __construct() {
    // Set header for API Request
    $this->headers = [
        'x-api-key' => env('API_KEY'),
      ];
    
      $this->LogController = new ErorrLogController;
  }
  /**
	 * Function: go to view add facility
	 * body: id_hotel
	 *	$request	: 
	*/
    public function facility(Request $request)
	{
		if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-facility-add'))
		{
			$nextSeq = Facility::get_last_sequence() + 1;
			return view('hotel.add-facility',['judul' => __('hotel.add_facility'),
											'id' =>$request->post('txtId'),
											'txtSeqNo' => $nextSeq]);
		}
		else
			abort(401);
    }

  /**
	 * Function: save/update data facility
	 * body: icon, name, seqNo0
	 *	$request	: 
	*/
    public function save_facility(Request $request)
	{
      try
		{
			if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-facility-add') || Auth::user()->permission('hotel-facility-update'))
			{
				$messages = array('txtName.max' => __('message.name_max_length_15'),
				'txtName.regex' => __('message.name_regex'),
				'txtName.required' => __('message.name_required'),
				'file.required' => __('message.img_required'),
				'file.max' => __('message.img_max'),
				'file.image' => __('message.img_type'),
				'txtSeqNo.required' => __('message.seq_no_required'),
				'txtIdHotel.required' => __('message.id_hotel_required'),
				'oldFile.required' => __('message.old_img_required'),
			  );
				if(empty($request['oldFile'])){
					$validator = Validator::make($request->all(), [
					  'file' => 'required|image:jpeg,png,jpg,gif,svg|max:2048',
					  'txtName' => 'required|max:100|regex:/^[A-Za-z0-9-_!,. ]+$/',
					  'txtSeqNo' => 'required',
				  ],$messages);
				}else{
				  $validator = Validator::make($request->all(), [
					'txtName' => 'required|max:100|regex:/^[A-Za-z0-9-_!,. ]+$/',
					'txtSeqNo' => 'required',
					'txtId' => 'required',
					'oldFile' => 'required',
				],$messages);
				}
				  if ($validator->fails()) {
					  // return response gagal
					  return back()->withInput($request->all())
								  ->with('error',$validator->errors()->first());  
				  }
				  // $headers = [
				  //   'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
				  // ];
				  $client = new Client(); //GuzzleHttp\Client
				  if(empty($request['file'])){
					$response = $client->request('POST', url('api').'/hotel/add_facility',[
					  'verify' => false,
					  'multipart' => [
						[
						  'name'     => 'id',
						  'contents' => $request->post('txtId'),
					  ],
						[
							'name'     => 'name',
							'contents' => $request->post('txtName'),
						],
						[
						  'name'     => 'seq_no',
						  'contents' => $request->post('txtSeqNo'),
					  ],
						[
							'name'     => 'old_icon',
							'contents' => $request['oldFile'],
						],
							],
						  
					'headers'       => $this->headers,
					'/delay/5',
					['connect_timeout' => 3.14]
					]);
				  }else{
					$file               = request('file');
					$file_path          = $file->getPathname();
					$file_mime          = $file->getMimeType('image');
					$file_uploaded_name = $file->getClientOriginalName();
						  // $response = $client->request('POST', 'http://thg-web/api/hotel/add_facility',[
						  $response = $client->request('POST', url('api').'/hotel/add_facility',[  
							'verify' => false,
							'multipart' => [
							  [
								'name'     => 'id',
								'contents' => $request->post('txtId'),
							],
							  [
								  'name'     => 'name',
								  'contents' => $request->post('txtName'),
							  ],
							  [
								'name'     => 'seq_no',
								'contents' => $request->post('txtSeqNo'),
							],
							  [
								  'name'     => 'icon',
								  'contents' => fopen($file, 'r')
							  ],
								  ],
								
						  'headers'       => $this->headers,
						  '/delay/5',
						  ['connect_timeout' => 3.14]
					  ]);
				  }
				
				  $body = $response->getBody();
				  $response = json_decode($body, true);
				  // dd($response);
				  if($response['status'] == true && $response['data'] != null){           
					return redirect()->route('hotel.get_edit', ['id' => $request->post('txtIdHotel')])
							->with('success', $response['message']);
				  }else{
					return back()->withInput($request->all())
								->with('error',$response['message']);
				  }
			}
			else
			  return back()->withInput($request->all())
								->with('error',__('permission.unauthorized'));
		}
		catch (Throwable $e) 
		{
			report($e);
			$error = ['modul' => 'save_facility',
					'actions' => 'save data facility',
					'error_log' => $e,
					'device' => "0" ];
					$report = $this->LogController->error_log($error);
			$response = [
				'status' => false,
				'message' => __('message.internal_erorr'),
				'code' => 500,
				'data' => [], 
			];
			return response()->json($response, 500);
		}
	}
   /**
	 * Function: delete data  facility
	 * body: facility_id
	 *	$request	: 
	*/

    public function delete_facility($id)
	{
      
        try
		{
			if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-facility-delete'))
			{	
				$data = ['id' => $id];
				// $headers = [
				//     'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
				//   ];
		  
				  $client = new Client(); //GuzzleHttp\Client
					  // $response = $client->request('POST', 'http://thg-web/api/hotel/delete_facility',[
					  $response = $client->request('POST', url('api').'/hotel/delete_facility',[
					  'verify' => false,
					  'form_params'   => $data,
					  'headers'       => $this->headers,
					  '/delay/5',
					   ['connect_timeout' => 3.14]
				  ]);
				  $body = $response->getBody();
				  $response = json_decode($body, true);
				  if($response['status'] == true){
					return back()->with('success','Item delete successfully!');
								
					// echo'send message succes';
				  }else{
					return back()->with('error',$response['message']);
				  }
			}
			else
				return back()->with('error',__('permission.unauthorized'));
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'delete_facility',
                'actions' => 'delete data facility',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => [], 
            ];
            return response()->json($response, 500);
        }
    }
  /**
	 * Function: add all data facility to data hotel facility
	 * body: id_hotel
	 *	$request	: 
	*/
    public function add_facility_all(Request $request)
	{
      try
	  {
		if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-facility-add'))
		{
			 $request=json_decode($request['data']);
			//  $headers = [
			//     'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
			//   ];
			  $data = [
				'hotel_id' => $request->hotel_id   
			 ];
			  $client = new Client(); //GuzzleHttp\Client
				  // $response = $client->request('POST', 'http://thg-web/api/hotel/add_hotel_facility_all',[
				  $response = $client->request('POST', url('api').'/hotel/add_hotel_facility_all',[
				  'verify' => false,
				  'form_params'   => $data,
				  'headers'       => $this->headers,
				  '/delay/5',
				   ['connect_timeout' => 3.14]
			  ]);
			  $body = $response->getBody();
			  $response = json_decode($body, true);
			  if($response['status'] == true && $response['data'] != null){
			   $checked = true;
				return redirect()->route('hotel.get_edit', ['id' =>  $request->hotel_id,
															'checked' => $checked])
				->with('success', $response['message']);   
			  }else{
				return back()->withInput($request->all())
							->with('error',$response['message']);
			  }
		}
		else
			return back()->withInput($request->all())
							->with('error',__('permission.unauthorized'));
       }
		catch (Throwable $e) 
		{
		  report($e);
		  $error = ['modul' => 'add_facility_all',
			'actions' => 'add data facility',
			'error_log' => $e,
			'device' => "0" ];
			$report = $this->LogController->error_log($error);
		  $response = [
			  'status' => false,
			  'message' => __('message.internal_erorr'),
			  'code' => 500,
			  'data' => [], 
		  ];
		  return response()->json($response, 500);
		}
      }

      /**
	 * Function: add data hotel facility
	 * body: id_hotel, facility_id
	 *	$request	: 
	*/
      public function add_facility(Request $request)
	  {
        try
		{
			if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-facility-add'))
			{
			   $request=json_decode($request['data']);
			  //  $headers = [
			  //     'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
			  //   ];
				$data = [
				  'hotel_id' => $request->hotel_id,
				  'facility_id' => $request->facility_id,     
			   ];
				$client = new Client(); //GuzzleHttp\Client
					// $response = $client->request('POST', 'http://thg-web/api/hotel/add_hotel_facility',[
					  $response = $client->request('POST', url('api').'/hotel/add_hotel_facility',[
					'verify' => false,
					'form_params'   => $data,
					'headers'       => $this->headers,
					'/delay/5',
					 ['connect_timeout' => 3.14]
				]);
				$body = $response->getBody();
				$response = json_decode($body, true);
				if($response['status'] == true && $response['data'] != null){
				 
				  return redirect()->route('hotel.get_edit', ['id' =>  $request->hotel_id])
				  ->with('success', $response['message']);   
				}else{
				  return back()->withInput($request->all())
							  ->with('error',$response['message']);
				}
			}
			else
				return back()->withInput($request->all())
							  ->with('error',__('permission.unauthorized'));
				
            
		}
		catch (Throwable $e) {
			report($e);
			$error = ['modul' => 'add_facility',
			'actions' => 'add data facility',
			'error_log' => $e,
			'device' => "0" ];
			$report = $this->LogController->error_log($error);
			$response = [
				'status' => false,
				'message' => __('message.internal_erorr'),
				'code' => 500,
				'data' => [], 
			];
			return response()->json($response, 500);
		}
      }

      /**
	 * Function: delete data hotel facility
	 * body: id_hotel, facility_id
	 *	$request	: 
	*/
      public function delete_hotel_facility(Request $request)
	  {
        try
		{
			$request=json_decode($request['data']);
			
			if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-facility-delete'))
			{
			  //  dd($request);
			  //  $headers = [
			  //     'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
			  //   ];
				$data = [
				  'hotel_id' => $request->hotel_id,
				  'facility_id' => $request->facility_id,     
			   ];
				$client = new Client(); //GuzzleHttp\Client
					// $response = $client->request('POST', 'http://thg-web/api/hotel/delete_hotel_facility',[
					  $response = $client->request('POST', url('api').'/hotel/delete_hotel_facility',[
					'verify' => false,
					'form_params'   => $data,
					'headers'       => $this->headers,
					'/delay/5',
					 ['connect_timeout' => 3.14]
				]);
				$body = $response->getBody();
				$response = json_decode($body, true);
				// dd($response);
				if($response['status'] == true){
				 
				  return redirect()->route('hotel.get_edit', ['id' =>  $request->hotel_id])
				  ->with('success', $response['message']);   
				}else{
				  return redirect()->route('hotel.get_edit', ['id' =>  $request->hotel_id])
							  ->with('error',$response['message']);
				}
			}
			else{
				
				return redirect()->route('hotel.get_edit', ['id' =>  $request->hotel_id])
							  ->with('error',__('permission.unauthorized'));
			}
			
			
            }
            catch (Throwable $e) {
                report($e);
                $error = ['modul' => 'delete_hotel_facility',
                'actions' => 'delete data hotel facility',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
                $response = [
                    'status' => false,
                    'message' => __('message.internal_erorr'),
                    'code' => 500,
                    'data' => [], 
                ];
                return response()->json($response, 500);
        }
      }
/**
	 * Function: delete All data hotel facility
	 * body: id_hotel
	 *	$request	: 
	*/
        public function delete_facility_all(Request $request)
		{
          try
		  {
			if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-facility-delete'))
			{
				 $request=json_decode($request['data']);
				//  $headers = [
				//     'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
				//   ];
				  $data = [
					'hotel_id' => $request->hotel_id   
				 ];
				  $client = new Client(); //GuzzleHttp\Client
					  // $response = $client->request('POST', 'http://thg-web/api/hotel/delete_hotel_facility_all',[
						$response = $client->request('POST', url('api').'/hotel/delete_hotel_facility_all',[
					  'verify' => false,
					  'form_params'   => $data,
					  'headers'       =>  $this->headers,
					  '/delay/5',
					   ['connect_timeout' => 3.14]
				  ]);
				  $body = $response->getBody();
				  $response = json_decode($body, true);
				  // dd($response);
				  if($response['status'] == true){
				   $checked = false;
					return redirect()->route('hotel.get_edit', ['id' =>  $request->hotel_id,
																'checked' => $checked])
					->with('success', $response['message']);   
				  }else{
					return back()->withInput($request->all())
								->with('error',$response['message']);
				  }
			}
			else
				return back()->withInput($request->all())
								->with('error',__('permission.unauthorized'));
		  }
		  catch (Throwable $e) {
			  report($e);
			  $error = ['modul' => 'delete_facility_all',
			'actions' => 'delete data hotel facility',
			'error_log' => $e,
			'device' => "0" ];
			$report = $this->LogController->error_log($error);
			  $response = [
				  'status' => false,
				  'message' => __('message.internal_erorr'),
				  'code' => 500,
				  'data' => [], 
			  ];
			  return response()->json($response, 500);
		  }
		}
        
    /**
	 * Function: go to view edit data facility
	 * body: id_hotel, facility_id
	 *	$request	: 
	*/      
        public function edit_hotel_facility(Request $request)
		{
          try
		  {
			if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-facility-update'))
			{
				$id_hotel =$request['id_hotel'];
				 $request=json_decode($request['data']);
				 $data = ['id' => $request->id,
						  'name' => $request->name,
						  'icon' => $request->icon,
						  'seq_no' => $request->seq_no];
						  return view('hotel.add-facility',['judul' => __('hotel.edit_facility'),
												  'id' =>$id_hotel,'data' =>$data]); 
			}
			else
				abort(401);
		   }
		  catch (Throwable $e) {
			  report($e);
			  $error = ['modul' => 'edit_hotel_facility',
			'actions' => 'edit data hotel facility',
			'error_log' => $e,
			'device' => "0" ];
			$report = $this->LogController->error_log($error);
			  $response = [
				  'status' => false,
				  'message' => __('message.internal_erorr'),
				  'code' => 500,
				  'data' => [], 
			  ];
			  return response()->json($response, 500);
		    }
        }
}
