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
| Hotel WEB Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process Hotel data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: December 21
*/

class MsystemController extends Controller
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
            // Validasti hanya Admin, Admin Outlet dan Admin Hotel yang bisa akses
            // if((session()->get('role')=='Admin'))
            if(Auth::user()->permission('utility'))
            {
                $name = session('full_name');
                $role =session('role');
                if(strtolower($role) == 'admin'){
                    $url = '/msytem/get_system';
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
                return view('msystem.list',['judul' => 'Master System',
                'data' =>$data]);
            }
          }
          catch (Throwable $e) {
            $error = ['modul' => 'index',
            'actions' => 'get view index',
            'error_log' => $e,
            'device' => "0" ];
            $report = $this->LogController->error_log($error);
            return view('msystem.list',['judul' => 'Master System',
            'data' =>null]);
        }
      }
    /**
	 * Function: get data mSystem for datatable
	 * body: 
	 *	$request	: 
	*/
    public function view_data(Request $request)
    {
        try{
            // if((session()->get('role')=='Admin'))
            if(Auth::user()->permission('utility'))
            {
                $name = session('full_name');
                // $headers = [
                //   'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
                // ];
        
                $client = new Client(); //GuzzleHttp\Client
                    // $response = $client->request('GET', 'http://thg-web/api/hotel/get_hotel_all',[
                    $response = $client->request('GET', url('api').'/msytem/get_system',[
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
            $error = ['modul' => 'view_data',
                'actions' => 'get data msystem',
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
	 * Function: Save data mSystem
	 * body: 
	 *	$request	: 
	*/
    public function save_data(Request $request)
    {
        // dd($request->system_img);
        try{
            // if((session()->get('role')=='Admin'))
            if(Auth::user()->permission('utility'))
            {
                // $headers = [
                //     'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
                // ];
                // $file               = $request['system_img'];
                // dd($request->system_img);
                //     dd($file);
                $client = new Client(); //GuzzleHttp\Client
                if(!empty($request->system_img)){
                    $file = $request['system_img'];
                    // dd($file);
                    // $file_path          = $file->getPathname();
                    // $file_mime          = $file->getMimeType('image');
                    // $file_uploaded_name = $file->getClientOriginalName();
                        // $response = $client->request('POST', 'http://thg-web/api/hotel/add_facility',[
                        $response = $client->request('POST', url('api').'/msytem/add_system',[  
                            'verify' => false,
                            'multipart' => [
                            [
                                'name'     => 'system_type',
                                'contents' => $request['system_type'],
                            ],
                            [
                                'name'     => 'system_cd',
                                'contents' => $request['system_cd'],
                            ],
                            [
                                'name'     => 'system_value',
                                'contents' => $request['system_value'],
                            ],
                            [
                                'name'     => 'system_img',
                                'contents' => fopen($file, 'r')
                            ],
                            [
                                'name'     => 'created_by',
                                'contents' => $request['created_by'],
                            ],
                                ],
                                
                        'headers'       => $this->headers,
                        '/delay/5',
                        ['connect_timeout' => 3.14]
                    ]);
                }
                else{
                $response = $client->request('POST',url('api').'/msytem/add_system',[
                    'verify' => false,
                    'form_params'   => $request->all(),
                    'headers'       => $this->headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
                ]);
                }
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
            }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'save_data',
                'actions' => 'save data msystem',
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
    public function get_system_id($id){
        // dd($id);
        try{
            // if((session()->get('role')=='Admin'))
            if(Auth::user()->permission('utility'))
            {
                $name = session('full_name');    
                $client = new Client(); //GuzzleHttp\Client
                    $response = $client->request('GET', url('api').'/msystem/get_system_id?id='.$id,[
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
            $error = ['modul' => 'get_system_id',
                'actions' => 'get data msystem by id',
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
	 * Function: Edit data mSystem
	 * body: 
	 *	$request	: 
	*/
    public function edit_data(Request $request)
    {
        
        try{
            // if((session()->get('role')=='Admin'))
            if(Auth::user()->permission('utility'))
            {
                // $headers = [
                //     'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
                // ];
                
                $client = new Client(); //GuzzleHttp\Client
                if(!empty($request->system_img)){
                    $file = $request['system_img'];
                    // dd($file);
                    // $file_path          = $file->getPathname();
                    // $file_mime          = $file->getMimeType('image');
                    // $file_uploaded_name = $file->getClientOriginalName();
                        // $response = $client->request('POST', 'http://thg-web/api/hotel/add_facility',[
                        $response = $client->request('POST', url('api').'/msytem/edit_system',[  
                            'verify' => false,
                            'multipart' => [
                            [
                                'name'     => 'id',
                                'contents' => $request['id'],
                            ],
                                [
                                'name'     => 'system_type',
                                'contents' => $request['system_type'],
                            ],
                            [
                                'name'     => 'system_cd',
                                'contents' => $request['system_cd'],
                            ],
                            [
                                'name'     => 'system_value',
                                'contents' => $request['system_value'],
                            ],
                            [
                                'name'     => 'system_img',
                                'contents' => fopen($file, 'r')
                            ],
                            [
                                'name'     => 'updated_by',
                                'contents' => $request['updated_by'],
                            ],
                                ],
                                
                        'headers'       => $this->headers,
                        '/delay/5',
                        ['connect_timeout' => 3.14]
                    ]);
                }
                else{
                $response = $client->request('POST',url('api').'/msytem/edit_system',[
                    'verify' => false,
                    'form_params'   => $request->all(),
                    'headers'       => $this->headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
                ]);
                }
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
            }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'edit_data',
                'actions' => 'edit data msystem',
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
	 * Function: Delete data mSystem
	 * body: 
	 *	$request	: 
	*/
    public function delete_data(Request $request)
    {
        
        try{
            // if((session()->get('role')=='Admin'))
            if(Auth::user()->permission('utility'))
            {
                $data = $request->data;
                //check total data
                if(count($data) != 0){
                    //loop total data
                    foreach($data as $key) {
                        // $headers = [
                        //         'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
                        //     ];
                            
                            $client = new Client(); //GuzzleHttp\Client
                                $response = $client->request('POST',url('api').'/msytem/delete_system',[
                                'verify' => false,
                                'form_params'   => [
                                    'id' => $key ],
                                'headers'       => $this->headers,
                                '/delay/5',
                                ['connect_timeout' => 3.14]
                            ]);
                            $body = $response->getBody();
                            $response = json_decode($body, true);
                    }
                    if($response['status'] == true){
                        $response = [
                            'status' => true,
                            'message' => __('message.data_deleted_success'),
                            'code' => 200,
                            'data' => $response['data'], 
                        ];
                        return response()->json($response, 200);
                    }
                }else{
                    $response = [
                        'status' => false,
                        'message' => $response['message'],
                        'code' => 400,
                        'data' => null, 
                    ];
                    return response()->json($response, 200);
                }
            }
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'delete_data',
                'actions' => 'delete data msystem',
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
	 * Function: Delete data mSystem
	 * body: 
	 *	$request	: 
	*/
    public function delete_system($request)
    {
        
        try{
            // if((session()->get('role')=='Admin'))
            if(Auth::user()->permission('utility'))
            {
                    $client = new Client(); //GuzzleHttp\Client
                    $response = $client->request('POST',url('api').'/msytem/delete_system',[
                    'verify' => false,
                    'form_params'   => [
                        'id' => $request
                    ],
                    'headers'       => $this->headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
                ]);
                $body = $response->getBody();
                $response = json_decode($body, true);
        
                    if($response['status'] == true){
                        $response = [
                            'status' => true,
                            'message' => __('message.data_deleted_success'),
                            'code' => 200,
                            'data' => $response['data'], 
                        ];
                        return redirect()->route('system')
                        ->with('success', $response['message']);  

                    }
                    else{
                        $response = [
                            'status' => false,
                            'message' => $response['message'],
                            'code' => 400,
                            'data' => null, 
                        ];
                        return back()
                        ->with('error', $response['message']); 
                    }
            }
             
        }
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'delete_system',
                'actions' => 'delete data msystem',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return back()
                ->with('error', $response['message']); 
        }
        
    }

    
    /**
	 * Function: get data get_city from msystem
	 * body: 
	 *	$request	: 
	*/
    public function get_city(Request $request)
    {
        try{
            
                $name = session('full_name');
                // $headers = [
                //   'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
                // ];
        
                $client = new Client(); //GuzzleHttp\Client
                    // $response = $client->request('GET', 'http://thg-web/api/hotel/get_hotel_all',[
                    $response = $client->request('GET', url('api').'/msytem/get_system_type_cd?system_type=city',[
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
            $error = ['modul' => 'get_city',
                'actions' => 'get data city from msystem',
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
}
