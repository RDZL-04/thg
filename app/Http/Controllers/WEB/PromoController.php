<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Validator;
use App\Http\Controllers\LOG\ErorrLogController;
use Auth;

/*
|--------------------------------------------------------------------------
| promo Web Controller
|--------------------------------------------------------------------------
|
| Validate, Authorize user.
| This user controller will control user data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: January , 2021
*/


class PromoController extends Controller
{
    public function __construct() {
        // Set header for API Request
        $this->headers = [
            'x-api-key' => env('API_KEY'),
          ];
        $this->LogController = new ErorrLogController;
      }

    /**
     * Function: route and send data to index
     * body: 
     *	$request	: 
    */
    public function index()
    {
      try{
            // Validasi Permission, hanya Admin dan User dengan outlet-promo-list
            if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-promo-list'))
            {
                    $name = session('full_name');
                    $role = session('role');
                    $id = session('id');
                    $client = new Client(); //GuzzleHttp\Client
                    if(strtolower($role) == 'admin'){
                        $url = '/promo/get_all';
                        $response_outlet = $client->request('GET', url('api').'/outlet/get_outlet_all',[
                            'verify' => false,
                            'headers' => $this->headers,
                            '/delay/5',
                            ['connect_timeout' => 3.14]
                        ]);
                        $response_hotel = $client->request('GET', url('api').'/hotel/get_hotel_all',[
                            'verify' => false,
                            'headers'       => $this->headers,
                            '/delay/5',
                            ['connect_timeout' => 3.14]
                        ]);
                    }
                    else{
                        $url = '/promo/get_promo_outlet_with_user?user_id='.$id;
                        $response_outlet = $client->request('GET', url('api').'/outlet/get_outlet_all_with_user?user_id='.session()->get('id'),[
                            'verify' => false,
                            'headers'  => $this->headers,
                            '/delay/5',
                            ['connect_timeout' => 3.14]
                        ]);
                        // $response_hotel = $client->request('GET', url('api').'/hotel/get_hotel_user_id?user_id='.session()->get('id'),[
                        $response_hotel = $client->request('GET', url('api').'/hotel/get_hotel_all_with_user_outlet?user_id='.session()->get('id'),[
                                'verify' => false,
                                'headers'       => $this->headers,
                                '/delay/5',
                                ['connect_timeout' => 3.14]
                        ]);
                    }
                    $response = $client->request('GET', url('api').$url,[
                        'verify' => false,
                        'headers' => $this->headers,
                        '/delay/5',
                        ['connect_timeout' => 3.14]
                    ]);

                    $body = $response_outlet->getBody();
                    $response_outlet = json_decode($body, true);
                    $data_outlet = $response_outlet['data'];
            
                    $body = $response_hotel->getBody();
                    $response_hotel = json_decode($body, true);
                    $data_hotel = $response_hotel['data'];

                    $body = $response->getBody();
                    $response = json_decode($body, true);
                    $data = $response['data'];
                    return view('promo.list', [ 
                                                'judul' => 'Master Promo',
                                                'data' => $data,
                                                'dataHotel' => $data_hotel,
                                                'dataOutlet' => $data_outlet
                                              ]
                                );
            } else {
                return redirect('home');
            }
        }
        catch (Throwable $e) {
          return view('promo.list',['judul' => 'Master Promo',
          'data' =>null]);
      }
    }
    /*
	 * Function: get data promo search by id
	 * body: 
	 * $request	:
	 **/
    public function get_promo_id($id){
        // dd($id);
        try{
            $name = session('full_name');    
            $client = new Client(); //GuzzleHttp\Client
                $response = $client->request('GET', url('api').'/promo/get_promo_id?id='.$id,[
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
            $error = ['modul' => 'get_promo_id',
                'actions' => 'get data promo by id',
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

    /**
     * Function: send data user to  table index
     * body: 
     *	$request	: 
    */
    public function get_data(Request $request)
    {
        try{
            $name = session('full_name');
            $role = session('role');
            $id = session('id');
            if(strtolower($role) == 'admin'){
                  $url = '/promo/get_all';
            }
            else{
                $url = '/promo/get_promo_outlet_with_user?user_id='.$id;
            }
              $client = new Client(); //GuzzleHttp\Client
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
                'actions' => 'get data promo by outlet user',
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

    /**
     * Function: Get outlet Promo Data with Hotel ID
     * body: 
     *	$request	: 
    */
    public function get_promo_with_hotel_user(Request $request)
    {
        try{
            $name = session('full_name');
            $role = session('role');
            $id = session('id');
            $client = new Client(); //GuzzleHttp\Client
            if(empty($request->user_id) || $request->user_id == null){
                $url = '/promo/get_all_promo_with_hotel?hotel_id='.$request->hotel_id;
                $url_outlet = url('api').'/outlet/get_hotel_outlet?hotel_id='.$request->hotel_id;
            }
            else{
                $url = '/promo/get_all_promo_with_hotel?hotel_id='.$request->hotel_id.'&user_id='.$request->user_id;
                $url_outlet = url('api').'/outlet/get_hotel_outlet_with_user?hotel_id='.$request->hotel_id.'&user_id='.session()->get('id');
            }
            $response = $client->request('GET', url('api').$url,[
                'verify' => false,
                'headers'       => $this->headers,
                '/delay/5',
                 ['connect_timeout' => 3.14]
            ]);
            $body = $response->getBody();
            $response = json_decode($body, true);

            $response_outlet = $client->request('GET', $url_outlet,[
                'verify' => false,
                'headers'  => $this->headers,
                '/delay/5',
                ['connect_timeout' => 3.14]
            ]);
            $body = $response_outlet->getBody();
            $response_outlet = json_decode($body, true);
            
            $data['data'] = $response['data'];
            $data['dataOutlet'] = $response_outlet['data'];
            return $data;
          }
          catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_promo_with_hotel_user',
                'actions' => 'get data promo by hotel user',
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

     /**
     * Function: Get outlet Promo Data with Hotel ID
     * body: 
     *	$request	: 
    */
    public function get_promo_with_outlet_user(Request $request)
    {
        try{
            $name = session('full_name');
            $role = session('role');
            $id = session('id');
            if(empty($request->user_id) || $request->user_id == null){
                $url = '/promo/get_all_promo_with_outlet?outlet_id='.$request->outlet_id;
            }
            else{
                $url = '/promo/get_all_promo_with_outlet?outlet_id='.$request->outlet_id.'&user_id='.$request->user_id;
            }
              $client = new Client(); //GuzzleHttp\Client
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
            $error = ['modul' => 'get_promo_with_outlet_user',
                'actions' => 'get data promo by Outlet user',
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


    /**
     * Function: send data user to  table index
     * body: 
     *	$request	: 
    */
    public function get_data_with_user(Request $request)
    {
        try{
            $client = new Client(); //GuzzleHttp\Client
                // $response = $client->request('GET', 'http://thg-web/api/hotel/get_hotel_all',[
                $response = $client->request('GET', url('api').'/promo/get_promo_outlet_with_user?user_id='.session()->get('id'),[
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
            $error = ['modul' => 'get_data_with_user',
                'actions' => 'get data promo by id user',
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

     /**
	 * Function: edit data user
	 * body: 
	 *	$request	: 
	*/
  public function save_data(Request $request)
  {
      try{
            // Validasi Permission, hanya Admin dan User dengan outlet-promo-create
            if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-promo-create"))
            {
                $client = new Client(); //GuzzleHttp\Client
                    $response = $client->request('POST',url('api').'/promo/add_promo',[
                    'verify' => false,
                    'form_params'   => $request->all(),
                    'headers'       => $this->headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
                ]);
                $body = $response->getBody();
                $response = json_decode($body, true);
                //   dd($response['data']);
                if($response['status'] == true && $response['data'] != null){
                    $response = [
                        'status' => true,
                        'message' => __('message.data_saved_success'),
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
            } else {
                return redirect('home');
            }
      }
      catch (Throwable $e) {
          report($e);
          $error = ['modul' => 'save_data',
                'actions' => 'save data promo',
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
	 * Function: edit data user
	 * body: 
	 *	$request	: 
	*/
  public function edit_data(Request $request)
  {
      try{
            // Validasi Permission, hanya Admin dan User dengan outlet-promo-edit
            if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-promo-edit"))
            {
                $client = new Client(); //GuzzleHttp\Client
                    $response = $client->request('POST',url('api').'/promo/edit_promo',[
                    'verify' => false,
                    'form_params'   => $request->all(),
                    'headers'       => $this->headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
                ]);
                $body = $response->getBody();
                $response = json_decode($body, true);
                //   dd($response['data']);
                if($response['status'] == true && $response['data'] != null){
                    $response = [
                        'status' => true,
                        'message' => __('message.edit-success'),
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
            } else {
                return redirect('home');
            }
      }
      catch (Throwable $e) {
          report($e);
          $error = ['modul' => 'edit_data',
                'actions' => 'edit data promo',
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
	 * Function: delete data promo
	 * body: param[id]
	 *	$request	: 
	*/
    
  public function delete_promo(Request $request)
  {
    try
    {
        // Validasi Permission, hanya Admin dan User dengan outlet-promo-delete
        if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-promo-delete"))
        {
            $client = new Client(); //GuzzleHttp\Client
                $response = $client->request('POST',url('api').'/promo/delete_promo',[
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
                    'message' => __('message.delete-success'),
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
        } else {
            return redirect('home');
        }
    }
    catch (Throwable $e) {
        report($e);
        $error = ['modul' => 'delete_data',
                'actions' => 'delete data promo',
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
