<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Validator;
use Response;
use File;
use Milon\Barcode\DNS1D;
use Illuminate\Support\Facades\Storage;
use DNS2D;
use PDF;
use App\Http\Controllers\LOG\ErorrLogController;
use Auth;

/*
|--------------------------------------------------------------------------
| Table Web Controller
|--------------------------------------------------------------------------
|
| Validate, Authorize user.
| This table controller will control table data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: 25 January , 2021
*/


class TableController extends Controller
{
    public function __construct() {
        // Set header for API Request
        $this->headers = [
            'x-api-key' => env('API_KEY'),
          ];
        
        $this->LogController = new ErorrLogController;
      }

    public function get_table()
    {
        try{
            // Validasi Permission, hanya Admin dan User dengan outlet-table-list
            if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-table-list"))
            {
                // $name = session()->get('full_name');
                $role =session()->get('role');
                $id = session()->get('id');
                $client = new Client(); //GuzzleHttp\Client
                if(strtolower($role) == 'admin'){
                    $url = '/table/get_all';
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
                }else{
                    $url = '/table/get_table_by_user?user_id='.$id;
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

                $body = $response_outlet->getBody();
                $response_outlet = json_decode($body, true);
                $data_outlet = $response_outlet['data'];
        
                $body = $response_hotel->getBody();
                $response_hotel = json_decode($body, true);
                $data_hotel = $response_hotel['data'];

                    $client = new Client(); //GuzzleHttp\Client
                        $response = $client->request('GET', url('api').$url,[
                        'verify' => false,
                        'headers' => $this->headers,
                        '/delay/5',
                        ['connect_timeout' => 3.14]
                    ]);
                    $body = $response->getBody();
                    $response = json_decode($body, true);
                    $data=$response['data'];
                    return view('table.list',[
                                            'judul' => 'Table',
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
            return view('table.list',['judul' => 'Table',
            'data' =>null]);
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
            $client = new Client(); //GuzzleHttp\Client
                $response = $client->request('GET', url('api').'/table/get_all',[
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
                'actions' => 'get data table',
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
     * Function: get data table by id hotel
     * body: 
     *	$request	: 
    */
    public function get_data_by_hotel($id_hotel)
    {
        // dd($id_hotel);
        try{
            $name = session('full_name');
            $client = new Client(); //GuzzleHttp\Client
                $response = $client->request('GET', url('api').'/table/get_by_hotel?id_hotel='.$id_hotel,[
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
            $error = ['modul' => 'get_data_by_hotel',
                'actions' => 'get data table by hotel',
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
     * Function: get data table by id hotel with user
     * body: 
     *	$request	: 
    */
    public function get_data_by_hotel_with_user(Request $request)
    {
        // dd($request['id_user']);
        try{
            $name = session('full_name');
            $client = new Client(); //GuzzleHttp\Client
                $response = $client->request('GET', url('api').'/table/get_table_by_user?hotel_id='.$request['id_hotel'].'&user_id='.$request['id_user'],[
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
            $error = ['modul' => 'get_data_by_hotel',
                'actions' => 'get data table by hotel',
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
     * Function: get data table by id outlet
     * body: 
     *	$request	: 
    */
    public function get_data_by_outlet($id_outlet)
    {
        // dd($id_hotel);
        try{
            $name = session('full_name');
            $client = new Client(); //GuzzleHttp\Client
                $response = $client->request('GET', url('api').'/table/get_by_outlet?fboutlets_id='.$id_outlet,[
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
            $error = ['modul' => 'get_data_by_outlet',
                'actions' => 'get data table by outlet',
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
     * Function: get data table by id outlet
     * body: 
     *	$request	: 
    */
    public function get_data_by_id($id)
    {
        // dd($id_hotel);
        try{
            $name = session('full_name');
            $client = new Client(); //GuzzleHttp\Client
                $response = $client->request('GET', url('api').'/table/get_by_id?id='.$id,[
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
            $error = ['modul' => 'get_data_by_id',
                'actions' => 'get data table by id',
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
	 * Function: save data outlet table
	 * body: 
	 *	$request	: 
	*/
  public function save_data(Request $request)
  {
      try{
            // Validasi Permission, hanya Admin dan User dengan outlet-table-create
            if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-table-create"))
            {
                $client = new Client(); //GuzzleHttp\Client
                $response = $client->request('POST',url('api').'/table/add_table',[
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
            } else {
                return redirect('home');
            }
      }
      catch (Throwable $e) {
          report($e);
          $error = ['modul' => 'save_data',
                'actions' => 'save data table',
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
	 * Function: edit data outlet table
	 * body: 
	 *	$request	: 
	*/
    public function edit_data(Request $request)
    {
      try{
            // Validasi Permission, hanya Admin dan User dengan outlet-table-edit
            if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-table-edit"))
            {
                $client = new Client(); //GuzzleHttp\Client
                $response = $client->request('POST',url('api').'/table/edit_table',[
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
            } else {
                return redirect('home');
            }
      }
      catch (Throwable $e) {
          report($e);
          $error = ['modul' => 'edit_data',
                'actions' => 'edit data table',
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
	 * Function: delete data table
	 * body: param[id]
	 *	$request	: 
    */
    public function delete_table($request)
    {
      try
      {
          // Validasi Permission, hanya Admin dan User dengan outlet-table-delete
          if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-table-delete"))
          {
                $client = new Client(); //GuzzleHttp\Client
                    $response = $client->request('POST',url('api').'/table/delete_table',[
                    'verify' => false,
                    'form_params'   => ['id' => $request],
                    'headers'       => $this->headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
                ]);
                $body = $response->getBody();
                $response = json_decode($body, true);
                //   dd($response['message']);
                
                if($response['status'] == true){
                    // $response = [
                    //     'status' => true,
                    //     'message' => __('message.succes'),
                    //     'code' => 200,
                    //     'data' => $response['data'], 
                    // ];
                    // return response()->json($response, 200);    
                    return redirect()->route('table')
                        ->with('success', $response['message']);  
                }
                return back()
                        ->with('error', $response['message']); 
          } else {
              return redirect('home');
          }
      }
      catch (Throwable $e) {
          report($e);
          $error = ['modul' => 'delete_table',
                'actions' => 'delete data table',
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
    
  public function barcode($request)
  {
    try{
        // Validasi Permission, hanya Admin dan User dengan outlet-table-list
        if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-table-list"))
        {
            $data= [];
            $explode = explode(",", $request);
            foreach($explode as $data_tabel){
                $data_tabel = explode("*",$data_tabel);
                $barcode = $data_tabel[0];
                $outlet = $data_tabel[1];
                // echo $outlet;
                $data_barcode = ['barcode' => $barcode,
                                'outlet' => $outlet];
                array_push($data, $data_barcode);
                // $data = $data+$data_barcode;
            }
            //to blade
            // return view('table.barcode',['data' => $data]);
            //to pdf
            // dd($data);
            $pdf = PDF::loadView('table.barcode', ['data' => $data])->setPaper('a4', 'landscape'); 
            return $pdf->stream();
        } else {
            return redirect('home');
        }
    }
    catch(Throwable $e){
        report($e);
        $error = ['modul' => 'barcode',
                'actions' => 'generate data barcode table',
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
