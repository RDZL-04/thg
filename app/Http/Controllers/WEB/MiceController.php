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
| Mice Category WEB Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process Mice Category on Web View
| 
| @author: rangga.muharam@arkamaya.co.id 
| @update: March 30th, 2021
*/
class MiceController extends Controller
{
    // Construct
    public function __construct() {
        // Set header for API Request
        $this->headers = [
            'x-api-key' => env('API_KEY'),
          ];
          $this->LogController = new ErorrLogController;
    }

    /**
	 * Function: go to view list Mice Category
	 * body:  
	 *	$request	: 
	*/
    public function mice_category_index(){
        try {
                // Validasi Permission, hanya Admin dan User dengan mice-category-list
                if((session()->get('role')=='Admin') || Auth::user()->permission('mice-category-list'))
                {
                    $name = session('full_name');
                    // Validasi jika user merupakan admin
                    $client = new Client(); //GuzzleHttp\Client
                    if(strtoupper(session('role')) == 'ADMIN'){
                        $response = $client->request('GET', url('api').'/mice/get_all_mice_category',[
                            'verify' => false,
                            'headers'       => $this->headers,
                            '/delay/5',
                            ['connect_timeout' => 3.14]
                        ]);
                        $responseHotel = $client->request('GET', url('api').'/mice/get_hotel_mice',[
                            'verify' => false,
                            'headers'       => $this->headers,
                            '/delay/5',
                            ['connect_timeout' => 3.14]
                        ]);
                        
                    } else {
                        // get all Mice Category berdasarkan User Hotel
                        $response = $client->request('GET', url('api').'/mice/get_all_mice_category?user_id='. session('id'),[
                            'verify' => false,
                            'headers'       => $this->headers,
                            '/delay/5',
                            ['connect_timeout' => 3.14]
                        ]);

                        // get all Mice Category berdasarkan User Hotel
                        // $responseHotel = $client->request('GET', url('api').'/mice/get_hotel_mice_with_hotel_user?user_id='. session('id'),[
                        $responseHotel = $client->request('GET', url('api').'/hotel/get_hotel_user_id?user_id='. session('id'),[
                            'verify' => false,
                            'headers'       => $this->headers,
                            '/delay/5',
                            ['connect_timeout' => 3.14]
                        ]);
                    }
                    $body = $response->getBody();
                    $response = json_decode($body, true);
                    $data = $response['data'];

                    $body = $responseHotel->getBody();
                    $response = json_decode($body, true);
                    $dataHotel = $response['data'];
                    if(!empty(session('message')))
                    {
                        return view('mice.list',[
                            'judul' => 'Mice Category',
                            'data' => $data,
                            'dataHotel' => $dataHotel,
                            'message' => session('message')
                            ]
                        );
                    }
                    else {
                        return view('mice.list',[
                                            'judul' => 'Mice Category',
                                            'data' => $data,
                                            'dataHotel' => $dataHotel
                                            ]
                        );
                    }
                } else {
                    return redirect('home');
                }
        }  
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'mice_category_index',
                        'actions' => 'Goto View Mice Category List Index',
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
	 * Function: go to get_all_mice_category
	 * body:  
	 *	$request	: 
	*/
    public function get_all_mice_category(){
        try {
            $name = session('full_name');
            // Validasi jika user merupakan admin
            $client = new Client(); //GuzzleHttp\Client
            if(strtoupper(session('role')) == 'ADMIN'){
                $response = $client->request('GET', url('api').'/mice/get_all_mice_category',[
                    'verify' => false,
                    'headers'       => $this->headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
                ]);   
            } else {
                $response = $client->request('GET', url('api').'/mice/get_all_mice_category?user_id='. session('id'),[
                    'verify' => false,
                    'headers'       => $this->headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
                ]);

    
            }
            $body = $response->getBody();
            $response = json_decode($body, true);
            $data = $response['data'];
           
            return $data;
        }  
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_all_mice_category',
                        'actions' => 'Filter Hotel Mice Category List Index',
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
	 * Function: Get Data all Mice Category With Hotel
	 * body:  
	 *	$request	: 
	*/
    public function get_all_mice_category_with_hotel(Request $request){
        try {
            $name = session('full_name');
            // Validasi jika user merupakan admin
            $client = new Client(); //GuzzleHttp\Client
            if(strtoupper(session('role')) == 'ADMIN'){
                $response = $client->request('GET', url('api').'/mice/get_all_mice_category_hotel_user_filter?hotel_id='.$request->hotel_id,[
                    'verify' => false,
                    'headers'       => $this->headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
                ]);   
            } else {
                $response = $client->request('GET', url('api').'/mice/get_all_mice_category_hotel_user_filter?hotel_id='.$request->hotel_id.'user_id='. session('id'),[
                    'verify' => false,
                    'headers'       => $this->headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
                ]);
            }
            $body = $response->getBody();
            $response = json_decode($body, true);
            $data = $response['data'];
           
            return $data;
        }  
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_all_mice_category_with_hotel',
                        'actions' => 'Filter Mice Category List Index With hotel_id',
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
	 * Function: go to view Add Mice Category
	 * body:  
	 *	$request	: 
	*/
    public function new_mice_category(){
        try {
                // Validasi Permission, hanya Admin dan User dengan mice-category-create
                if((session()->get('role')=='Admin') || Auth::user()->permission('mice-category-create'))
                {
                    $name = session('full_name');
                    // GET data Hotel
                    // Validasi jika user merupakan admin
                    $client = new Client(); //GuzzleHttp\Client
                    if(strtoupper(session('role')) == 'ADMIN'){
                        $response = $client->request('GET', url('api').'/hotel/get_hotel_all',[
                            'verify' => false,
                            'headers'       => $this->headers,
                            '/delay/5',
                            ['connect_timeout' => 3.14]
                        ]);
                    } else {
                    // get all Mice Category berdasarkan User Hotel
                        $response = $client->request('GET', url('api').'/hotel/get_hotel_user_id?user_id='. session('id'),[
                            'verify' => false,
                            'headers'       => $this->headers,
                            '/delay/5',
                            ['connect_timeout' => 3.14]
                        ]);
                    }
                    $body = $response->getBody();
                    $response = json_decode($body, true);
                    $dataHotel = $response['data'];

                    // GET MICE Category dari tabel mysystems
                    $response_system = $client->request('GET', url('api').'/mice/get_mice_category_msystem',[
                        'verify' => false,
                        'headers'       => $this->headers,
                        '/delay/5',
                        ['connect_timeout' => 3.14]
                    ]);
                    $client = new Client(); //GuzzleHttp\Client
                    $body_msystem = $response_system->getBody();
                    $response_msystem = json_decode($body_msystem, true);
                    $dataCategory = $response_msystem['data'];

                    return view('mice.add',[
                                            'judul' => __('button.add'),
                                            'dataCategory' => $dataCategory,
                                            'dataHotel' => $dataHotel,
                                            ]
                    );
                } else {
                    return redirect('home');
                }
        }  
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'new_mice_category',
                        'actions' => 'route to View Add Mice Category',
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
	 * Function: add Mice Category
	 * body:  
	 *	$request	: 
	*/
    public function add_mice_category(Request $request){
        try {
            // dd($request->all());
            $name = session('full_name');
            $validator = Validator::make($request->all(), [
                'selectIdHotel' => 'required',
                'selectIdMiceCat' => 'required',
                'description' => 'required',
                'images' => 'required'
            ]);
            if ($validator->fails()) {
                // return response gagal
                return back()->withInput($request->all())
                            ->with('error',$validator->errors()->first());
            }
            $file = request('images');
            // Construct $data param to Hit API add mice category
            $data = [
                [
                  'name'     => 'hotel_id',
                  'contents' => $request->post('selectIdHotel')
                ],
                [
                    'name'     => 'category_id',
                    'contents' => $request->post('selectIdMiceCat')
                ],
                [
                  'name'     => 'description',
                  'contents' => $request->post('description')
                ],
                [
                  'name'     => 'created_by',
                  'contents' => $name
                ],
                [
                    'name'     => 'images',
                    'contents' => fopen($file, 'r')
                ],
            ];
            $client = new Client(); //GuzzleHttp\Client
            $response = $client->request('POST', url('api').'/mice/add_mice_category',[
                    'verify' => false,
                    'multipart'   => $data,
                    'headers'       => $this->headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
            ]);
            $body = $response->getBody();
            $response = json_decode($body, true);
            $messageSave = $response['message'];

            if($response['status'] && $response['data'] != null){
                // Validasi jika user merupakan admin
                $client = new Client(); //GuzzleHttp\Client
                if(strtoupper(session('role')) == 'ADMIN'){
                    $response = $client->request('GET', url('api').'/mice/get_all_mice_category',[
                        'verify' => false,
                        'headers' => $this->headers,
                        '/delay/5',
                        ['connect_timeout' => 3.14]
                    ]);
                    $responseHotel = $client->request('GET', url('api').'/mice/get_hotel_mice',[
                        'verify' => false,
                        'headers'       => $this->headers,
                        '/delay/5',
                        ['connect_timeout' => 3.14]
                    ]);
                } else {
                    // get all Mice Category berdasarkan User Hotel
                    $response = $client->request('GET', url('api').'/mice/get_all_mice_category?user_id='. session('id'),[
                        'verify' => false,
                        'headers' => $this->headers,
                        '/delay/5',
                        ['connect_timeout' => 3.14]
                    ]);
                    // get all Mice Category berdasarkan User Hotel
                    $responseHotel = $client->request('GET', url('api').'/mice/get_hotel_mice_with_hotel_user?user_id='. session('id'),[
                        'verify' => false,
                        'headers'       => $this->headers,
                        '/delay/5',
                        ['connect_timeout' => 3.14]
                    ]);
                }
                $body = $response->getBody();
                $response = json_decode($body, true);
                $data = $response['data'];
                $body = $responseHotel->getBody();
                $response = json_decode($body, true);
                $dataHotel = $response['data'];
                return redirect()->route('mice_category')->with([
                    'judul' => 'Mice Category',
                    'data' => $data,
                    'dataHotel' => $dataHotel,
                    'message' => $messageSave   
                ]);
                // return view('mice.list',[
                //                         'judul' => 'Mice Category',
                //                         'data' => $data,
                //                         'dataHotel' => $dataHotel,
                //                         'message' => $messageSave
                //                         ]
                // );
            } else {
                return back()->withInput($request->all())
                            ->with('error',$response['message']);
            }
        }  
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'add_mice_category',
                        'actions' => 'Hit API add Mice Category',
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
	 * Function: go to view Edit Mice Category
	 * body:  
	 *	$request	: 
	*/
    public function get_edit_mice_category(Request $request){
        try {
                // Validasi Permission, hanya Admin dan User dengan mice-category-edit
                if((session()->get('role')=='Admin') || Auth::user()->permission('mice-category-edit'))
                {
                    $name = session('full_name');
                    $data = [
                        'mice_category_id' => $request->id,
                    ];
                    // GET data Hotel
                    // Validasi jika user merupakan admin
                    $client = new Client(); //GuzzleHttp\Client
                    if(strtoupper(session('role')) == 'ADMIN'){
                        // GET MICE Category dari tabel mysystems
                        $response_system = $client->request('POST', url('api').'/mice/get_mice_category_detail',[
                            'verify' => false,
                            'headers' => $this->headers,
                            'form_params' => $data,
                            '/delay/5',
                            ['connect_timeout' => 3.14]
                        ]);
                    } else {
                        $data = $data + [
                            'user_id' => session('id')
                        ];
                        // GET MICE Category dari tabel mysystems
                        $response_system = $client->request('POST', url('api').'/mice/get_mice_category_detail',[
                            'verify' => false,
                            'headers' => $this->headers,
                            'form_params'   => $data,
                            '/delay/5',
                            ['connect_timeout' => 3.14]
                        ]);
                    }

                    $body_msystem = $response_system->getBody();
                    $response_msystem = json_decode($body_msystem, true);
                    $dataCategory = $response_msystem['data'][0];
                    return view('mice.edit',[
                                            'judul' => __('button.edit'),
                                            'dataCategory' => $dataCategory,
                                            ]
                    );
                } else {
                    return redirect('home');
                }
        }  
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_edit_mice_category',
                        'actions' => 'Goto View Edit Mice Category',
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
	 * Function: Edit Mice Category
	 * body:  
	 *	$request	: 
	*/
    public function edit_mice_category(Request $request){
        try {
            // dd($request->all());
            $name = session('full_name');
            if(!empty($request->images)) {
                $file = request('images');
                // Construct $data param to Hit API edit mice category
                $data = [
                    [
                        'name'     => 'id',
                        'contents' => $request->post('id')
                    ],
                    [
                      'name'     => 'hotel_id',
                      'contents' => $request->post('selectIdHotel')
                    ],
                    [
                        'name'     => 'category_id',
                        'contents' => $request->post('selectIdMiceCat')
                    ],
                    [
                      'name'     => 'description',
                      'contents' => $request->post('description')
                    ],
                    [
                      'name'     => 'updated_by',
                      'contents' => $name
                    ],
                    [
                        'name'     => 'images',
                        'contents' => fopen($file, 'r')
                    ],
                ];
            } else {
                // Construct $data param to Hit API edit mice category
                $data = [
                    [
                        'name'     => 'id',
                        'contents' => $request->post('id')
                    ],
                    [
                        'name'     => 'hotel_id',
                        'contents' => $request->post('selectIdHotel')
                    ],
                    [
                        'name'     => 'category_id',
                        'contents' => $request->post('selectIdMiceCat')
                    ],
                    [
                        'name'     => 'description',
                        'contents' => $request->post('description')
                    ],
                    [
                        'name'     => 'updated_by',
                        'contents' => $name
                    ],
                    [
                        'name'     => 'oldImages',
                        'contents' => $request->post('oldImages')
                    ],
                ];
            }
            // dd($data);
            // Setup Guzzle Http Request
            $client = new Client(); //GuzzleHttp\Client

            // GET MICE Category dari tabel mysystems
            $response_system = $client->request('POST', url('api').'/mice/edit_mice_category',[
                'verify' => false,
                'headers' => $this->headers,
                'multipart'   => $data,
                '/delay/5',
                ['connect_timeout' => 3.14]
            ]);
            $body_msystem = $response_system->getBody();
            $response = json_decode($body_msystem, true);
            $messageSave = $response['message'];
            
            if($response['status'] && $response['data'] != null){
            
                // $this->mice_category_index();
                if(strtoupper(session('role')) == 'ADMIN'){
                    $response = $client->request('GET', url('api').'/mice/get_all_mice_category',[
                        'verify' => false,
                        'headers'       => $this->headers,
                        '/delay/5',
                        ['connect_timeout' => 3.14]
                    ]);
                    $responseHotel = $client->request('GET', url('api').'/mice/get_hotel_mice',[
                        'verify' => false,
                        'headers'       => $this->headers,
                        '/delay/5',
                        ['connect_timeout' => 3.14]
                    ]);
                    
                } else {
                    // get all Mice Category berdasarkan User Hotel
                    $response = $client->request('GET', url('api').'/mice/get_all_mice_category?user_id='. session('id'),[
                        'verify' => false,
                        'headers'       => $this->headers,
                        '/delay/5',
                        ['connect_timeout' => 3.14]
                    ]);
    
                     // get all Mice Category berdasarkan User Hotel
                     $responseHotel = $client->request('GET', url('api').'/mice/get_hotel_mice_with_hotel_user?user_id='. session('id'),[
                        'verify' => false,
                        'headers'       => $this->headers,
                        '/delay/5',
                        ['connect_timeout' => 3.14]
                    ]);
                }
                $body = $response->getBody();
                $response = json_decode($body, true);
                $data = $response['data'];
    
                $body = $responseHotel->getBody();
                $response = json_decode($body, true);
                $dataHotel = $response['data'];
                return redirect()->route('mice_category')->with([
                    'judul' => 'Mice Category',
                    'data' => $data,
                    'dataHotel' => $dataHotel,
                    'message' => $messageSave   
                ]);
              
                // return back()->withInput($request->all())
                //               ->with('success',$response['message']);  
            } else {
                return back()->withInput($request->all())
                ->with('error',$response['message']);
            }
        }  
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'edit_mice_category',
                        'actions' => 'Hit API Edit Mice Category',
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
	 * Function: delete data Mice Category
	 * body: outlet_id
	 *	$request	: 
	*/
    public function delete_mice_category($id){
    try{
            // Validasi Permission, hanya Admin dan User dengan mice-category-delete
            if((session()->get('role')=='Admin') || Auth::user()->permission('mice-category-delete'))
            {
                $data = ['id' => $id];
                $client = new Client(); //GuzzleHttp\Client
                    $response = $client->request('POST', url('api').'/mice/delete_mice_category',[
                    'verify' => false,
                    'form_params'   => $data,
                    'headers'       => $this->headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
                ]);
                $body = $response->getBody();
                $response = json_decode($body, true);
                if($response['status'] == true){
                    return back()->with('success','Data deleted successfully!');
                                
                    // echo'send message succes';
                }else{
                    return back()->with('error',$response['message']);
                }
            } else {
                return redirect('home');
            }
    }
    catch (Throwable $e) {
        report($e);
        $error = ['modul' => 'delete_mice_category',
                    'actions' => 'Hit API Delete Mice Category',
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
