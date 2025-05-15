<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Validator;
use App\Http\Controllers\LOG\ErorrLogController;
use App\Models\HotelImages;
use Auth;

/*
|--------------------------------------------------------------------------
| Hotel WEB Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process Hotel data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: December 21
*/

class HotelController extends Controller
{
  public function __construct() {
    // Set header for API Request
    $this->headers = [
		// 'Content-Type' => 'application/json',
        'x-api-key' => env('API_KEY'),
		/* 'Access-Control-Allow-Origin' => '*',
		'Access-Control-Allow-Methods' => 'GET, POST, PUT, OPTIONS',
		'Access-Control-Allow-Headers' => 'X-API-KEY, X-AUTH-TOKEN', */
		'debug' => false
      ];
    $this->LogController = new ErorrLogController;
  }

  /**
     * Function: send data hotel to  table index
     * body: 
     *	$request	: 
    */
    public function list_hotel(Request $request)
    {
        try
		{
			if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-list'))
			{
				$name = session('full_name');
				$role =session('role');
				$id = session('id');
				// dd($id);
				// $headers = [
				//   'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
				// ];
				 if(strtolower($role) == 'admin'){
				  $url = '/hotel/get_hotel_all';
				}elseif(strpos(strtolower($role),'hotel') !== false || strpos(strtolower($role),'it') !== false ){
				  $url = '/hotel/get_hotel_user_id?user_id='.$id;
				}
				else{
				  return view('hotel.list',['judul' => 'Hotel',
							'data' =>null])
						  ->with('error','unautorized');
				}
				// dd($url);
				$client = new Client(); //GuzzleHttp\Client
					// $response = $client->request('GET', 'http://thg-web/api/hotel/get_hotel_all',[
					$response = $client->request('GET', url('api').$url,[
					'verify' => false,
					'headers'       => $this->headers,
					'/delay/5',
					 ['connect_timeout' => 3.14]
				]);
				$body = $response->getBody();
				$response = json_decode($body, true);
				if($response['status']){
				  $data=$response['data'];
				  return view('hotel.list',['judul' => 'Hotel',
				  'data' =>$data]);
				}
				else{
				  return view('hotel.list',['judul' => 'Hotel',
							  'data' =>null]);
				}
			}
			else
				abort(401);
        }
        catch (Throwable $e) 
		{
            $error = ['modul' => 'list_hotel',
                'actions' => 'get data hotel',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            return view('hotel.list',['judul' => 'Hotel',
            'data' =>null]);
        }
    }
  /**
     * Function: send data hotel to  table index
     * body: 
     *	$request	: 
    */
  public function get_data(Request $request)
    {
        try{
            $name = session('full_name');
            $role =session('role');
            $id = session('id');
            // dd($role);
            // $headers = [
            //   'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
            // ];
            if(strtolower($role) == 'admin'){
              $url = '/hotel/get_hotel_all';
            }elseif(strpos(strtolower($role),'hotel') !== false || strpos(strtolower($role),'it') !== false ){
              $url = '/hotel/get_hotel_user_id?user_id='.$id;
            }
            else{
              $url = '/hotel/get_hotel_all_with_user_outlet?user_id='.$id;
            }
    
            $client = new Client(); //GuzzleHttp\Client
                // $response = $client->request('GET', 'http://thg-web/api/hotel/get_hotel_all',[
                $response = $client->request('GET', url('api').$url,[
                'verify' => false,
                'headers'       => $this->headers,
                '/delay/5',
				
                 ['connect_timeout' => 3.14]
            ]);
            $body = $response->getBody();
            $response = json_decode($body, true);
            $data=$response['data'];
            return $data;
          }
          catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_data',
                'actions' => 'get data hotel',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => $response['message'],
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }

    public function add_hotel(){
      // $headers = [
      //   'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
      //   ];

        // Get URL google-maps ke tabel M_System
        $client = new Client(); //GuzzleHttp\Client
        $resMSystem = $client->request('GET', url('api').'/msytem/get_system_type_cd?system_type=url&system_cd=url-googlemaps',[
            'verify' => false,
            'headers'       =>  $this->headers,
            '/delay/5',
            ['connect_timeout' => 3.14]
        ]);
        $body = $resMSystem->getBody();
        $resMSystem = json_decode($body, true);
        $resMSystem = $resMSystem['data'][0]['system_value'];
        // dd($resMSystem);
        
        return view('hotel.add',['judul' => __('hotel.add_hotel'), 'data' => null, 'url_google_maps' => $resMSystem]);
    }

    public function images_hotel(Request $request)
	{
		if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-image-add'))
		{
		  $nextSequence = HotelImages::get_last_sequence($request->post('txtId')) + 1;
		  
		  return view('hotel.add-images',['judul' => __('hotel.add_image'),
										  'id' =>$request->post('txtId'),
										  'name' => $request->post('txtName'),
										  'seqNo' => $nextSequence]);
		}
		else
			abort(401);
	}
  
  /**
     * Function: save user hotel
     * body: data hotel
     *	$request	: 
    */
    public function save_user_hotel(Request $request)
    {		
      // dd($request->post('txtcreated_by'));
        try{
          $messages = array(
              'txtIdHotel.required' => __('message.id_hotel_required'),
              'idUser.required' => __('message.id_user_required'),
              'txtcreated_by.required' => __('message.create_by_required'),
          );
            $validator = Validator::make($request->all(), [
                'txtIdHotel' => 'required',
                'idUser' => 'required',
                'txtcreated_by' => 'required',
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return back()->withInput($request->all())
                            ->with('error',$response['message']);
            }
            // Construct data untuk add User Hotel
            $data = [
                'user_id' => $request->post('idUser'),
                'hotel_id' => $request->post('txtIdHotel'),
                'created_by' => $request->post('txtcreated_by'),
            ];

              // HIT API Add User Hotel
              $client = new Client(); //GuzzleHttp\Client
              $response = $client->request('POST', url('api').'/hotel/add_hotel_user',[
                  'verify' => false,
                  'form_params'   => $data,
                  'headers'       => $this->headers,
                  '/delay/5',
                   ['connect_timeout' => 3.14]
              ]);
              $body = $response->getBody();
              $response = json_decode($body, true);
              // dd($response);
              
              // Check dulu jika User merupakan Admin Hotel, klo bukan jangan Otomatis sebagai User Outlet di Outlet yg ada di Hotel terkait
              $response_user = $client->request('GET', url('api').'/user/get_user?id='.$data['user_id'],[
                'verify' => false,
                'headers'       => $this->headers,
                '/delay/5',
                 ['connect_timeout' => 3.14]
              ]);
              $body = $response_user->getBody();
              $response_user = json_decode($body, true);
              if( (strtolower($response_user['data']['role_nm']) === 'admin hotel') || (strtolower($response_user['data']['role_nm']) === 'admin it') ){
                  // Check jika Hotel mempunyai Outlet, maka User tersebut otomatis di add sebagai User Outlet
                  $response_outlet = $client->request('GET', url('api').'/outlet/get_hotel_outlet?hotel_id='.$data['hotel_id'],[
                    'verify' => false,
                    'headers'       => $this->headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
                  ]);
                  $body = $response_outlet->getBody();
                  $response_outlet = json_decode($body, true);

                  // Jika Outlet Hotel ada recordnya
                  if(!empty($response_outlet['data'])){
                    for($i=0; $i < count($response_outlet['data']); $i++){
                      $data_outlet = [
                        'fboutlet_id' => $response_outlet['data'][$i]['id'],
                        'user_id' => $request->post('idUser'),
                        'created_by' => $request->post('txtcreated_by')
                      ];
                      // Check dulu jika User sudah pernah di add
                      $response_user_outlet = $client->request('POST', url('api').'/outlet/search_outlet_user',[
                        'verify' => false,
                        'headers'       => $this->headers,
                        'form_params'   => $data_outlet,
                        '/delay/5',
                        ['connect_timeout' => 3.14]
                      ]);
                      $body = $response_user_outlet->getBody();
                      $response_user_outlet = json_decode($body, true);
                      // dd($response_user_outlet);
                        if($response_user_outlet['status'] && $response_user_outlet['data'] == null) {
                          $response_user_outlet = $client->request('POST', url('api').'/outlet/save_outlet_user',[
                              'verify' => false,
                              'form_params'   => $data_outlet,
                              'headers'       => $this->headers,
                              '/delay/5',
                              ['connect_timeout' => 3.14]
                            ]);
                        }
                      }
                      $data_outlet = null;
                  }
              }  // End if jika User adalah Admin Hotel

              if($response['status'] == true && $response['data'] != null){
                $id = $response['data'];
                $id = $id['hotel_id'];
                return redirect()->route('hotel.get_edit', ['id' =>  $id])
                ->with('success', $response['message']);               
                // echo'send message succes';
              }else{
                return back()->withInput($request->all())
                            ->with('error',$response['message']);
              }
		}
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'save_user_hotel',
                'actions' => 'save data user hotel',
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
     * Function: save data hotel
     * body: data hotel
     *	$request	: 
    */
    public function save_hotel(Request $request)
    {		
        try{
          $messages = array('txtName.max' => __('message.name_max_length'),
                            'txtName.regex' => __('message.name_regex'),
                            'txtName.required' => __('message.name_required'),
                            'txtDescription.required' => __('message.desc_required'),
                            'txtDescription.max' => __('message.desc_max'),
                            'txtDescription.regex' => __('message.desc_regex'),
                            'selectStar.required' => __('message.star_required'),
                            'selectStatus.required' => __('message.status_required'),
                            'txtAddress.required' => __('message.address_required'),
                            'txtAddress.max' => __('message.address_max'),
                            'txtAddress.regex' => __('message.address_regex'),
                            'txtCity.required' => __('message.city_required'),
                            'txtCity.max' => __('message.city_max'),
                            'txtCity.regex' => __('message.city_regex'),
                            'txtBeHotelId.required' => __('message.be_hotel_id_required'),
                            'txtBeHotelId.numeric' => __('message.be_hotel_id_numeric'),
                            'txtBeSecreetKey.required' => __('message.be_secret_key_required'),
                            'txtBeSecreetKey.max' => __('message.be_secret_key_max'),
                            'txtBeSecreetKey.alpha_num' => __('message.be_secret_key_alpha_num'),
                            'txtBeApiKey.required' => __('message.be_api_key_required'),
                            'txtBeApiKey.max' => __('message.be_api_key_max'),
                            'txtBeApiKey.alpha_num' => __('message.be_api_key_alpha_num'),
                            'txtMpgSecreetKey.required' => __('message.mpg_secret_required'),
                            'txtMpgSecreetKey.max' => __('message.mpg_secret_key_max'),
                            'txtMpgSecreetKey.alpha_num' => __('message.mpg_secret_key_alpha_num'),
                            'txtMpgApiKey.required' => __('message.mpg_api_key_required'),
                            'txtMpgApiKey.max' => __('message.mpg_api_key_max'),
                            'txtMpgMerchantId.max' => __('message.mpg_merchant_id_max'),
                            'txtLongitude.required' => __('message.longitude_required'),
                            'txtLatitude.required' => __('message.latitude_required'),
                            'created_by.required' => __('message.create_by_required'),
                            'txtEmail.required' => __('message.email_required'),
                          );
            $validator = Validator::make($request->all(), [
                'txtName' => 'required|max:100|regex:/^[A-Za-z0-9 ]+$/',
                'selectStar' => 'required',
                'txtDescription' => 'required|regex:/^[\r\nA-Za-z0-9-_!:;,.\'&# ]+$/',
                'selectStatus' => 'required',
                'txtAddress' => 'required|max:200|regex:/^[A-Za-z0-9-_!,. ]+$/',
                'txtCity' => 'required|max:100|regex:/^[A-Za-z0-9-_!,. ]+$/',
                'txtBeHotelId' => 'required|numeric',
                'txtBeSecreetKey' => 'required|alpha_num|max:50',
                'txtBeApiKey' => 'required|alpha_num|max:50',
                'txtMpgSecreetKey' => 'required|alpha_num|max:50',
                'txtMpgApiKey' => 'required|alpha_num|max:50',
                'txtMpgMerchantId' => 'max:50',
                'txtLongitude' => 'required',
                'txtLatitude' => 'required',
                'created_by' => 'required',
                'txtEmail' => 'required|max:50',
                'txtEmailMice' => 'required|max:100',
                'txtMiceWA' => 'required|max:15'
            ],$messages);
            
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return back()->withInput($request->all())
                            ->with('error',$response['message']);
            }
            $data = [
                'name' => $request->post('txtName'),
                'address' => $request->post('txtAddress'),
                'description' => $request->post('txtDescription'),
                'be_hotel_id' => $request->post('txtBeHotelId'),
                'be_api_key' => $request->post('txtBeApiKey'),
                'be_secreet_key' => $request->post('txtBeSecreetKey'),
                'hotel_star' => $request->post('selectStar'),
                'status' => $request->post('selectStatus'),
                'mpg_merchant_id' => $request->post('txtMpgMerchantId'),
                'mpg_api_key' => $request->post('txtMpgSecreetKey'),
                'mpg_secreet_key' => $request->post('txtMpgApiKey'),
                'longitude' => $request->post('txtLongitude'),
                'latitude' => $request->post('txtLatitude'),
                'created_by' => $request->post('created_by'),             
                'city' => $request->post('txtCity'),          
                'email_notification' => $request->post('txtEmail'),    
                'mice_email' => $request->post('txtEmailMice'),    
                'mice_wa' => $request->post('txtMiceWA'),    
             ];
            //  $headers = [
            //     'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
            //   ];
    
              $client = new Client(); //GuzzleHttp\Client
                  $response = $client->request('POST', url('api').'/hotel/add_hotel',[
                  'verify' => false,
                  'form_params'   => $data,
                  'headers'       => $this->headers,
                  '/delay/5',
                   ['connect_timeout' => 3.14]
              ]);
              $body = $response->getBody();
              $response = json_decode($body, true);
              if($response['status'] == true && $response['data'] != null){
                $id = $response['data'];
                $id = $id['id'];
                return redirect()->route('hotel.get_edit', ['id' =>  $id])
                ->with('success', $response['message']);               
                // echo'send message succes';
              }else{
                return back()->withInput($request->all())
                            ->with('error',$response['message']);
              }
		}
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'save_hotel',
                'actions' => 'save data hotel',
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
	 * Function: edit data hotel
	 * body: data hotel
	 *	$request	: 
	*/
    public function edit_hotel(Request $request)
    {		
        try{

            $validator = Validator::make($request->all(), [
              // 'txtId' => 'required',
                'txtName' => 'required|max:100|regex:/^[A-Za-z0-9 ]+$/',
                'selectStar' => 'required',
                'txtDescription' => 'required|regex:/^[\r\nA-Za-z0-9-_!:;,.\'&# ]+$/',
                'selectStatus' => 'required',
                'txtAddress' => 'required|max:200|regex:/^[A-Za-z0-9-_!,. ]+$/',
                'txtCity' => 'required|max:100|regex:/^[A-Za-z0-9-_!,. ]+$/',
                'txtBeHotelId' => 'required|numeric',
                'txtBeSecreetKey' => 'required|alpha_num|max:50',
                'txtBeApiKey' => 'required|alpha_num|max:50',
                'txtMpgSecreetKey' => 'required|alpha_num|max:50',
                'txtMpgApiKey' => 'required|alpha_num|max:50',
                'txtMpgMerchantId' => 'max:50',
                'txtLongitude' => 'required',
                'txtLatitude' => 'required',
                'txtEmail' => 'required',
                'txtEmailMice' => 'required|max:50',
                'txtMiceWA' => 'required|max:100',
            ]);

          $messages = array('txtName.max' => __('message.name_max_length'),
          'txtName.regex' => __('message.name_regex'),
          'txtName.required' => __('message.name_required'),
          'txtDescription.required' => __('message.desc_required'),
          'txtDescription.max' => __('message.desc_max'),
          'txtDescription.regex' => __('message.desc_regex'),
          'selectStar.required' => __('message.star_required'),
          'selectStatus.required' => __('message.status_required'),
          'txtAddress.required' => __('message.address_required'),
          'txtAddress.max' => __('message.address_max'),
          'txtAddress.regex' => __('message.address_regex'),
          'txtCity.required' => __('message.city_required'),
          'txtCity.max' => __('message.city_max'),
          'txtCity.regex' => __('message.city_regex'),
          'txtBeHotelId.required' => __('message.be_hotel_id_required'),
          'txtBeHotelId.numeric' => __('message.be_hotel_id_numeric'),
          'txtBeSecreetKey.required' => __('message.be_secret_key_required'),
          'txtBeSecreetKey.max' => __('message.be_secret_key_max'),
          'txtBeSecreetKey.alpha_num' => __('message.be_secret_key_alpha_num'),
          'txtBeApiKey.required' => __('message.be_api_key_required'),
          'txtBeApiKey.max' => __('message.be_api_key_max'),
          'txtBeApiKey.alpha_num' => __('message.be_api_key_alpha_num'),
          'txtMpgSecreetKey.required' => __('message.mpg_secret_required'),
          'txtMpgSecreetKey.max' => __('message.mpg_secret_key_max'),
          'txtMpgSecreetKey.alpha_num' => __('message.mpg_secret_key_alpha_num'),
          'txtMpgApiKey.required' => __('message.mpg_api_key_required'),
          'txtMpgApiKey.max' => __('message.mpg_api_key_max'),
          'txtMpgMerchantId.max' => __('message.mpg_merchant_id_max'),
          'txtLongitude.required' => __('message.longitude_required'),
          'txtLatitude.required' => __('message.latitude_required'),
          'created_by.required' => __('message.create_by_required'),
          'txtEmail.required' => __('message.email_required'),
          'txtEmailMice.required' => __('message.email_required'),
          'txtMiceWA.required' => __('message.phone_required'),
        );
        
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return back()->withInput($request->all())
                            ->with('error',$response['message']);
            }
            $data = [
                'id' => $request->post('txtId'),
                'name' => $request->post('txtName'),
                'address' => $request->post('txtAddress'),
                'description' => $request->post('txtDescription'),
                'be_hotel_id' => $request->post('txtBeHotelId'),
                'be_api_key' => $request->post('txtBeApiKey'),
                'be_secreet_key' => $request->post('txtBeSecreetKey'),
                'hotel_star' => $request->post('selectStar'),
                'status' => $request->post('selectStatus'),
                'mpg_merchant_id' => $request->post('txtMpgMerchantId'),
                'mpg_api_key' => $request->post('txtMpgApiKey'),
                'mpg_secreet_key' => $request->post('txtMpgSecreetKey'),
                'longitude' => $request->post('txtLongitude'),
                'latitude' => $request->post('txtLatitude'),      
                'updated_by' => $request->post('updated_by'),    
                'city' => $request->post('txtCity'),
                'email_notification' => $request->post('txtEmail'),       
                'mice_email' => $request->post('txtEmailMice'),       
                'mice_wa' => $request->post('txtMiceWA'),       
             ];
            //  dd($data);
            //  $headers = [
            //     'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
            //   ];
    
              $client = new Client(); //GuzzleHttp\Client
                  $response = $client->request('POST', url('api').'/hotel/edit_hotel',[
                    
                  'verify' => false,
                  'form_params'   => $data,
                  'headers'       => $this->headers,
                  '/delay/5',
                   ['connect_timeout' => 3.14]
              ]);
              
              $body = $response->getBody();
              $response = json_decode($body, true);
              // dd($response);
              if($response['status'] == true && $response['data'] != null){
                $id = $response['data'];
                $id = $id['id'];                
                return back()->withInput($request->all())
                              ->with('success',$response['message']);        
                // echo'send message succes';
              }else{
                return back()->withInput($request->all())
                            ->with('error',$response['message']);
              }
		}
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'edit_hotel',
                'actions' => 'edit data hotel',
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

    public function add_user_hotel(Request $request)
	{
      // $headers = [
      //   'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
      // ];
	  if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-user-add'))
	  {
		 	  
		  $client = new Client(); //GuzzleHttp\Client
			  // $response = $client->request('GET', 'http://thg-web/api/hotel/get_hotel_all',[
			  $response = $client->request('GET', url('api').'/user/get_user_hotel',[
			  'verify' => false,
			  'headers'       => $this->headers,
			  '/delay/5',
			   ['connect_timeout' => 3.14]
		  ]);
		  $body = $response->getBody();
		  $response = json_decode($body, true);
		  $data=$response['data'];
		  // dd($data);
			
		  return view('hotel.add-user',['judul' => __('hotel.add_hotel_user'),
										  'id' =>$request->post('txtId'),
										  'name' =>$request->post('txtName'),
										  'data_user' => $data]);
	  }
	  else
	  {
		  abort(401);
	  }
	  
    }

/**
	 * Function: go to view edit data  image hotel
	 * body: data image
	 *	$request	: 
	*/
public function get_edit_images(Request $request)
{
  
  try{
	  if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-image-update'))
	  {
		$id_hotel =$request['id_hotel'];
		 $request=json_decode($request['data']);
		 $data = ['id' => $request->id,
				  'name' => $request->name,
				  'file_name' => $request->file_name,
				  'seq_no' => $request->seq_no]; 
              return view('hotel.add-images',['judul' => __('hotel.edit_image'),
                                      'id' =>$id_hotel,'data' =>$data]);
	  }
	  else
		  abort(401);
                                      
      }
      catch (Throwable $e) {
          report($e);
          $error = ['modul' => 'get_edit_images',
                'actions' => 'get data edit image hotel',
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
	 * Function: save/update data image hotel
	 * body: data image
	 *	$request	: 
	*/
  public function save_image(Request $request){
    try
	{
	  if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-image-add') || Auth::user()->permission('hotel-image-update'))
	  {
		  $messages = array('txtName.max' => __('message.name_max_length_15'),
			  'txtName.regex' => __('message.name_regex'),
			  'txtName.required' => __('message.name_required'),
			  'images.required' => __('message.img_required'),
			  'images.max' => __('message.img_max'),
			  'images.image' => __('message.img_type'),
			  'txtSeqNo.required' => __('message.seq_no_required'),
			  'txtIdHotel.required' => __('message.id_hotel_required'),
			  'oldImages.required' => __('message.old_img_required'),
			);
		  if(empty($request['oldImages'])){        
			$validator = Validator::make($request->all(), [
			  'images' => 'required|image:jpeg,jpg,png|max:2048',
			  'txtName' => 'required|max:15|regex:/^[A-Za-z0-9-_!,. ]+$/',
			  'txtSeqNo' => 'required',
			  'txtIdHotel' => 'required',
		  ],$messages);
		  }
		  else{
			$validator = Validator::make($request->all(), [
			  'txtId' => 'required',
			  'txtName' => 'required|max:15|regex:/^[A-Za-z0-9-_!,. ]+$/',
			  'txtSeqNo' => 'required',
			  'txtIdHotel' => 'required',
			  'oldImages' => 'required',
			  'images' => 'image:jpeg,jpg,png|max:2048',
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
		  if(empty($request['images'])){
			// $response = $client->request('POST', 'http://thg-web/api/hotel/add_image_hotel',[
			$response = $client->request('POST', url('api').'/hotel/add_image_hotel',[
			  
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
				  'name'     => 'hotel_id',
				  'contents' => $request->post('txtIdHotel'),
			  ],
				[
				  'name'     => 'seq_no',
				  'contents' => $request->post('txtSeqNo'),
			  ],
				[
					'name'     => 'oldImages',
					'contents' => $request['oldImages'],
				],
					],
				  
			'headers'       => $this->headers,
			'/delay/5',
			['connect_timeout' => 3.14]
			]);
		  }
		  else{
				$file               = request('images');
				// $file_path          = $file->getPathname();
				// $file_mime          = $file->getMimeType('image');
				// $file_uploaded_name = $file->getClientOriginalName();
						  //guzzle client
							$response = $client->request('POST', url('api').'/hotel/add_image_hotel',[
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
								'name'     => 'hotel_id',
								'contents' => $request->post('txtIdHotel'),
							],
							  [
								'name'     => 'seq_no',
								'contents' => $request->post('txtSeqNo'),
							],
							  [
								  'name'     => 'images',
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
				  }
				  else{
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
            $error = ['modul' => 'save_image',
                'actions' => 'save data image hotel',
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
	 * Function: to view edit hotel
	 * body: data hotel
	 *	$request	: 
	*/
    public function get_edit_hotel($id)
	{
		if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-update'))
		{
			// dd($id);
			$data = ['id' => $id];
			// $headers = [
			//     'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
			//   ];
			  // Get URL google-maps ke tabel M_System
			$client = new Client(); //GuzzleHttp\Client
			$resMSystem = $client->request('GET', url('api').'/msytem/get_system_type_cd?system_type=url&system_cd=url-googlemaps',[
				'verify' => false,
				'headers'       => $this->headers,
				'/delay/5',
				['connect_timeout' => 3.14]
			]);
			$body = $resMSystem->getBody();
			$resMSystem = json_decode($body, true);
			$resMSystem = $resMSystem['data'][0]['system_value'];

			  $client = new Client(); //GuzzleHttp\Client
				$response = $client->request('GET', url('api').'/hotel/get_hotel_id?id='.$data['id'],[
				  'verify' => false,
				  'form_params'   => $data,
				  'headers'       => $this->headers,
				  '/delay/5',
				   ['connect_timeout' => 3.14]
			  ]);
			  $response_facility = $client->request('GET', url('api').'/hotel/get_facility?id='.$data['id'],[
				'verify' => false,
				'form_params'   => $data,
				'headers'       => $this->headers,
				'/delay/5',
				 ['connect_timeout' => 3.14]
			]);
			$response_hotel_user = $client->request('GET', url('api').'/hotel/get_hotel_user?id_hotel='.$data['id'],[
			  'verify' => false,
			  'form_params'   => $data,
			  'headers'       => $this->headers,
			  '/delay/5',
			   ['connect_timeout' => 3.14]
		  ]);
		  
			$response_near_attraction = $client->request('GET', url('api').'/near/get_near_attraction_hotel?id_hotel='.$data['id'],[
			  'verify' => false,
			  'form_params'   => $data,
			  'headers'       => $this->headers,
			  '/delay/5',
			   ['connect_timeout' => 3.14]
		  ]);
			  $body = $response->getBody();
			  $response = json_decode($body, true);
			  $response_facility = json_decode($response_facility->getBody(), true);
			  $response_hotel_user = json_decode($response_hotel_user->getBody(), true);
			  $response_near_attraction = json_decode($response_near_attraction->getBody(), true);
        
			  if($response_near_attraction['data'] != null){
          $near_attraction = $response_near_attraction['data'];
        }else{
          $near_attraction = null;
        }
			  $data = $response['data'];
			  $hotel = $data['data'];
					$data_hotel = $hotel[0];
					
					if($data_hotel['hotel_facility'] == null){
					  $data_facility = null;
					}else{
					  $data_facility = $data_hotel['hotel_facility'];
					}
					if($data_hotel['hotel_image'] == null){
					  $data_img = null;
					}else{
					  $data_img = $data_hotel['hotel_image'];
					}
			  return view('hotel.edit-hotel',['judul' =>__('hotel.edit_hotel'),
											  'data' => $data_hotel,
											  'data_facility' => $response_facility['data'],
											  'data_img' => $data_img,
											  'data_user_hotel' => $response_hotel_user['data'],
											  'data_near_attraction' => $near_attraction,
											  'url_google_maps' => $resMSystem]);
		}
		else
			abort(401);
    }

    public function delete_hotel(Request $request)
	{
		try
		{
		  // $headers = [
		  //     'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
		  // ];
		  
		  $client = new Client(); //GuzzleHttp\Client
			  $response = $client->request('POST',url('api').'/hotel/delete_hotel',[
			  'verify' => false,
			  'form_params'   => $request->all(),
			  'headers'       => $this->headers,
			  '/delay/5',
			  ['connect_timeout' => 3.14]
		  ]);
		  $body = $response->getBody();
		  $response = json_decode($body, true);
		  //   dd($response['data']);
		  if($response['status'] == true){
			  $response = [
				  'status' => true,
				  'message' => __('message.data_deleted_success'),
				  'code' => 200,
				  'data' => $response['data'], 
			  ];
			  return response()->json($response, 200);    
		  }
		  $response = [
			  'status' => false,
			  'message' => $response['message'],
			  'code' => 400,
			  'data' => null, 
		  ];
		  return response()->json($response, 200);
		}
		catch (Throwable $e) {
			report($e);
			$error = ['modul' => 'delete_hotel',
					'actions' => 'delete data hotel',
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
  * Function: to delete hotel user
  * data: id hotel user
 */
  public function delete_hotel_user(Request $request){
      
      try{
          $data = ['id' => $request->id, 'hotel_id' => $request->hotel_id];
          if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-user-delete'))
          {
              $client = new Client(); //GuzzleHttp\Client
              $response = $client->request('POST',url('api').'/hotel/delete_hotel_user',[
                'verify' => false,
                'form_params'   => $data,
                'headers'       => $this->headers,
                '/delay/5',
                ['connect_timeout' => 3.14]
              ]);
              $body = $response->getBody();
              $response = json_decode($body, true);
              if($response['status'] == true){
                  return back()->with('success',__('message.data_deleted_success'));
                        
                  // echo'send message succes';
              }else{
                  return back()->with('error',$response['message']);
              }
          }
          else
          {
              return back()->with('error',__('permission.unauthorized'));
          }
    }
    catch (Throwable $e) {
        report($e);
        $error = ['modul' => 'delete_hotel_user',
                'actions' => 'delete data hotel user',
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
	 * Function: delete data image hotel
	 * body: id_image
	 *	$request	: 
	*/
    public function delete_images($id){
      
      // dd($id);
      try{
      $data = ['id' => $id];
      // $headers = [
      //     'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
      //   ];

        $client = new Client(); //GuzzleHttp\Client
            $response = $client->request('POST', url('api').'/hotel/delete_image_hotel',[
            'verify' => false,
            'form_params'   => $data,
            'headers'       =>$this->headers,
            '/delay/5',
             ['connect_timeout' => 3.14]
        ]);
        $body = $response->getBody();
        $response = json_decode($body, true);
        if($response['status'] == true){
          return back()->with('success',__('message.data_deleted_success'));
                      
          // echo'send message succes';
        }else{
          return back()->with('error',$response['message']);
        }
      }
      catch (Throwable $e) {
          report($e);
          $error = ['modul' => 'delete_images',
                'actions' => 'delete data hotel image',
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
