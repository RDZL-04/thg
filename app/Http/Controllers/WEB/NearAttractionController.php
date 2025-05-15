<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
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

class NearAttractionController extends Controller
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
    public function add_attraction(Request $request)
	{
		if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-attraction-add'))
		{
			$client = new Client(); //GuzzleHttp\Client
			$resMSystem = $client->request('GET', url('api').'/msytem/get_system_type_cd?system_type=near_attr_cat',[
				'verify' => false,
				'headers'       => $this->headers,
				'/delay/5',
				['connect_timeout' => 3.14]
			]);
			$body = $resMSystem->getBody();
			$resMSystem = json_decode($body, true);
			$resMSystem = $resMSystem['data'];
			// dd($resMSystem);
			return view('attraction.add_attraction',['judul' => __('attraction.add_attraction'),
											'id_hotel' =>$request['id_hotel'],
											'name_hotel' => $request['name_hotel'],
											'data_category' => $resMSystem]);
		}
		else
			abort(401);
    }

	/**
	 * Function: go to view edit attraction
	 * body: id_hotel
	 *	$request	: 
	*/
    public function edit_attraction(Request $request)
	{
		// dd($request->all());
		if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-attraction-update'))
		{
			$client = new Client(); //GuzzleHttp\Client
			$getData = $client->request('GET', url('api').'/near/get_near_attraction_by_id?id='.$request['id'],[
				'verify' => false,
				'headers'       => $this->headers,
				'/delay/5',
				['connect_timeout' => 3.14]
			]);
			$body = $getData->getBody();
			$data = json_decode($body, true);
			if($data['data']!=null){
				$data = $data['data'];
			}else{
				$data = null;
			}
			$resMSystem = $client->request('GET', url('api').'/msytem/get_system_type_cd?system_type=near_attr_cat',[
				'verify' => false,
				'headers'       => $this->headers,
				'/delay/5',
				['connect_timeout' => 3.14]
			]);
			$body = $resMSystem->getBody();
			$resMSystem = json_decode($body, true);
			$resMSystem = $resMSystem['data'];
			// dd($resMSystem);
			return view('attraction.add_attraction',['judul' => __('attraction.edit_attraction'),
													'data_category' => $resMSystem,
													'name_hotel' => $request['name_hotel'],
													'data' => $data]);
		}
		else
			abort(401);
    }

  /**
	 * Function: save/update data facility
	 * body: icon, name, seqNo0
	 *	$request	: 
	*/
    public function save_attraction(Request $request)
	{
      try
		{
			if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-facility-add') || Auth::user()->permission('hotel-facility-update'))
			{
				$messages = array(
					'txtName.max' => __('message.name_max_length_100'),
					'txtName.regex' => __('message.name_regex'),
					'txtName.required' => __('message.name_required'),
					'txtDistance.max' => __('message.distance_max_length'),
					'txtDistance.regex' => __('message.distance_regex'),
					'txtDistance.required' => __('message.distance_required'),
			  );
				if(!empty($request['txtId'])){
					$validator = Validator::make($request->all(), [
						'txtId' => 'required',
						'selectCategory' => 'required',
						'txtIdHotel' => 'required',
						'txtName' => 'required|max:100|regex:/^[A-Za-z0-9-_!,. ]+$/',
						'txtDistance' => 'required|max:50|regex:/^[A-Za-z0-9-_!,. ]+$/',
				  ],$messages);
				}else{
				  $validator = Validator::make($request->all(), [
						'txtIdHotel' => 'required',
						'selectCategory' => 'required',
						'txtName' => 'required|max:100|regex:/^[A-Za-z0-9-_!,. ]+$/',
						'txtDistance' => 'required|max:50|regex:/^[A-Za-z0-9-_!,. ]+$/',
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
				  if(!empty($request['txtId'])){
					  $data = [
								[
								'name'     => 'hotel_id',
								'contents' => $request->post('txtIdHotel'),
								],
								[
								'name'     => 'id',
								'contents' => $request->post('txtId'),
								],
								[
									'name'     => 'attraction_nm',
									'contents' => $request->post('txtName'),
								],
								[
								'name'     => 'category_id',
								'contents' => $request->post('selectCategory'),
								],
								[
									'name'     => 'distance',
									'contents' => $request['txtDistance'],
								],
								[
									'name'     => 'created_by',
									'contents' => $request['created_by'],
								]
							];
				  }else{
					$data = [
								[
								'name'     => 'hotel_id',
								'contents' => $request->post('txtIdHotel'),
								],
								[
									'name'     => 'attraction_nm',
									'contents' => $request->post('txtName'),
								],
								[
								'name'     => 'category_id',
								'contents' => $request->post('selectCategory'),
								],
								[
									'name'     => 'distance',
									'contents' => $request['txtDistance'],
								],
								[
									'name'     => 'created_by',
									'contents' => $request['created_by'],
								]
							];
				  }
				//   dd($data);
				  if(!empty($data)){
					$response = $client->request('POST', url('api').'/near/add_update_near_attraction',[
						'verify' => false,
						'multipart' => $data,						  
						'headers'       => $this->headers,
						'/delay/5',
						['connect_timeout' => 3.14]
					]);
				  }				
				  $body = $response->getBody();
				  $response = json_decode($body, true);
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

    public function delete_attraction($id)
	{
      
        try
		{
			if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-attraction-delete'))
			{	
				$data = ['id' => $id];
				  $client = new Client(); //GuzzleHttp\Client
					  // $response = $client->request('POST', 'http://thg-web/api/hotel/delete_facility',[
					  $response = $client->request('POST', url('api').'/near/delete_near_attraction',[
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
}
