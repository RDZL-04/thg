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
| Role WEB Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process Role data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: january 08 2021
*/

class RoleController extends Controller
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
            if((session()->get('role')=='Admin')){
                $name = session('full_name');
            $role =session('role');
            // if(strtolower($role) == 'admin'){
            if(Auth::user()->permission('utility')){
                $url = '/user/get_role';
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
            }
            else{
                $data = null;
            }
            return view('role.list',['judul' => 'Table',
            'data' =>$data]);
            }    
          }
          catch (Throwable $e) {
            return view('role.list',['judul' => 'Table',
            'data' =>null]);
        }
      }
    //
    /**
     * Function: send data user to  table index
     * body: 
     *	$request	: 
    */
    public function get_data(Request $request)
    {
        try{
            // if((session()->get('role')=='Admin')){
            if(Auth::user()->permission('utility')){
                $name = session('full_name');
                $client = new Client(); //GuzzleHttp\Client
                    $response = $client->request('GET', url('api').'/user/get_role',[
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
          }
          catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_data',
                'actions' => 'get data role',
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

    public function delete_role(Request $request)
  {
    try
    {
        // if((session()->get('role')=='Admin')){
        if(Auth::user()->permission('utility')){
            $client = new Client(); //GuzzleHttp\Client
                $response = $client->request('POST',url('api').'/user/delete_role',[
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
                    'message' => __('message.success-delete-role'),
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
    }
    catch (Throwable $e) {
        report($e);
        $error = ['modul' => 'delete_role',
                'actions' => 'delete data role',
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
	 * Function: Save data role
	 * body: 
	 *	$request	: 
	*/
    public function save_data(Request $request)
    {
        try{
            // if((session()->get('role')=='Admin')){
            if(Auth::user()->permission('utility')){
                $client = new Client(); //GuzzleHttp\Client
                $response = $client->request('POST',url('api').'/user/add_role',[
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
                        'message' => $response['message'],
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
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'save_data',
                'actions' => 'save data role',
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
            // if((session()->get('role')=='Admin')){
            if(Auth::user()->permission('utility')){
                $client = new Client(); //GuzzleHttp\Client
                $response = $client->request('POST',url('api').'/user/edit_role',[
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
                        'message' => $response['message'],
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
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'edit_data',
                'actions' => 'edit data role',
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
