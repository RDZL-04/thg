<?php
/*
|--------------------------------------------------------------------------
| Outlet WEB Controller
|--------------------------------------------------------------------------
|
| This controller will process Outlet data
| 
| @author: rangga.muharam@arkamaya.co.id 
| @update: December 27
*/

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Validator;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;
use Auth;

class OutletController extends Controller
{
  public function __construct() {
    // Set header for API Request
    $this->headers = [
        'x-api-key' => env('API_KEY'),
      ];
  }
  
    /**
	 * Function: route to view index outlet
	 * body: 
	 *	$request	: 
	*/
    public function index(){
        try{
            // Validasti hanya Admin, Admin Outlet dan Admin Hotel yang bisa akses
            if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-list'))
            {
                  $name = session('full_name');
                  $client = new Client(); //GuzzleHttp\Client
                  if(strtolower(session()->get('role')) == 'admin') {
                    $response_hotel = $client->request('GET', url('api').'/hotel/get_hotel_all',[
                        'verify' => false,
                        'headers'       => $this->headers,
                        '/delay/5',
                        ['connect_timeout' => 3.14]
                    ]);
                  } else {
                    $response_hotel = $client->request('GET', url('api').'/hotel/get_hotel_all_with_user_outlet?user_id='.session()->get('id'),[
                      'verify' => false,
                      'headers'       => $this->headers,
                      '/delay/5',
                      ['connect_timeout' => 3.14]
                    ]);
                  }
                  
                  $body = $response_hotel->getBody();
                  $response_hotel = json_decode($body, true);
                  $data_hotel=$response_hotel['data'];

                  $client = new Client(); //GuzzleHttp\Client
                  if(strtolower(session()->get('role')) == 'admin') {
                        $response_outlet = $client->request('GET', url('api').'/outlet/get_outlet_all',[
                          'verify' => false,
                          'headers' => $this->headers,
                          '/delay/5',
                          ['connect_timeout' => 3.14]
                        ]);
                  } else {
                    $response_outlet = $client->request('GET', url('api').'/outlet/get_outlet_all_with_user?user_id='.session()->get('id'),[
                      'verify' => false,
                      'headers'  => $this->headers,
                      '/delay/5',
                      ['connect_timeout' => 3.14]
                    ]);
                  }
                  $body = $response_outlet->getBody();
                  $response_outlet = json_decode($body, true);
                  if($response_outlet['status']){
                    $data_outlet=$response_outlet['data'];

                    return view('outlet.list',[
                                            'data' => $data_hotel,
                                            'data_outlet' => $data_outlet
                                            ]);
                  }else{
                    return view('outlet.list',[
                      'data' => null,
                      'data_outlet' => null
                      ]);
                  }
            } else {
              return redirect('home');
            }
        }
        catch (Throwable $e) {
          report($e);
          $response = [
              'status' => false,
              'message' => __('message.internal_erorr'),
              'code' => 500,
              'data' => null, 
          ];
          return view('outlet.list',[
            'data' => null,
            'data_outlet' => null
            ]);
          // return response()->json($response, 500);
      }
        
    } // end function List

    /**
	 * Function: get all outlet
	 * body: 
	 *	$request	: 
	*/
  public function get_outlet_all(){
    try{
      $name = session('full_name');
            $role =session('role');
            $id = session('id');
            // dd($role);
            // $headers = [
            //   'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
            // ];
            if(strtolower($role) == 'admin'){
              $url = '/outlet/get_outlet_all';
            }else{
              $url = '/outlet/get_outlet_all_with_user?user_id='.$id;
            }

      $client = new Client(); //GuzzleHttp\Client
          $response_outlet = $client->request('GET', url('api').$url,[
          'verify' => false,
          'headers'       => $this->headers,
          '/delay/5',
          ['connect_timeout' => 3.14]
      ]);
      $body = $response_outlet->getBody();
      $response_outlet = json_decode($body, true);
      $data_outlet=$response_outlet['data'];
      return $data_outlet=$response_outlet['data'];
    }
    catch (Throwable $e) {
      report($e);
      $response = [
          'status' => false,
          'message' => __('message.internal_erorr'),
          'code' => 500,
          'data' => null, 
      ];
      return response()->json($response, 500);
    } 
    
  } // end function List

    /**
	 * Function: get_outlet_all_with_user_outlet filtering user yg terdaftar saja
	 * body: 
	 *	$request	: 
	*/
  public function get_outlet_all_with_user_outlet(){
    try{
      $client = new Client(); //GuzzleHttp\Client
          $response_outlet = $client->request('GET', url('api').'/outlet/get_outlet_all_with_user?user_id='.session()->get('id'),[
          'verify' => false,
          'headers'       => $this->headers,
          '/delay/5',
          ['connect_timeout' => 3.14]
      ]);
      $body = $response_outlet->getBody();
      $response_outlet = json_decode($body, true);
      return $response_outlet['data'];
    }
    catch (Throwable $e) {
      report($e);
      $response = [
          'status' => false,
          'message' => __('message.internal_erorr'),
          'code' => 500,
          'data' => null, 
      ];
      return response()->json($response, 500);
    } 
    
  } // end function List

   /**
	 * Function: Get Outlet berdasarkan hotel_id untuk semua user
	 * body: 
	 *	$request	: hotel_id
	 */
    public function get_hotel_outlet(Request $request)
    {
        try{
          $name = session('full_name');
          // $headers = [
          //   'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
          // ];
          $client = new Client(); //GuzzleHttp\Client
          $response_outlet = $client->request('GET', url('api').'/outlet/get_hotel_outlet?hotel_id='.$request->hotel_id,[
              'verify' => false,
              'headers'       => $this->headers,
              '/delay/5',
               ['connect_timeout' => 3.14]
          ]);

          $body = $response_outlet->getBody();
          $response_outlet = json_decode($body, true);
          return $data_outlet=$response_outlet['data'];
        }
        catch (Throwable $e) {
          report($e);
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
	 * Function: Get Outlet berdasarkan hotel_id dan user_id terkait
	 * body: 
	 *	$request	: hotel_id
	 */
  public function get_hotel_outlet_with_user_id(Request $request)
  {
      try{
        $name = session('full_name');
        $client = new Client(); //GuzzleHttp\Client
        $response_outlet = $client->request('GET', url('api').'/outlet/get_hotel_outlet_with_user?hotel_id='.$request->hotel_id.'&user_id='.$request->user_id,[
            'verify' => false,
            'headers' => $this->headers,
            '/delay/5',
             ['connect_timeout' => 3.14]
        ]);

        $body = $response_outlet->getBody();
        $response_outlet = json_decode($body, true);
        return $data_outlet=$response_outlet['data'];
      }
      catch (Throwable $e) {
        report($e);
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
	 * Function: Get Outlet berdasarkan outlet_id
	 * body: 
	 *	$request	: hotel_id
	 */
  public function get_outlet_detail(Request $request)
  {
      try{
        $name = session('full_name');
        $client = new Client(); //GuzzleHttp\Client
        $response_outlet = $client->request('GET', url('api').'/outlet/get_outlet_detail?outlet_id='.$request->outlet_id,[
            'verify' => false,
            'headers'       => $this->headers,
            '/delay/5',
             ['connect_timeout' => 3.14]
        ]);

        $body = $response_outlet->getBody();
        $response_outlet = json_decode($body, true);
        return $data_outlet=$response_outlet['data'];
      }
      catch (Throwable $e) {
        report($e);
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
	 * Function: add outlet
	 * body: 
	 *	$request	: 
	*/
    public function add_outlet(){
      try {
        // Validasi permission khusus untuk Admin dan User yg punya Hak 
        if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-create')) {
            $name = session('full_name');
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
                $response_hotel = $client->request('GET', url('api').'/hotel/get_hotel_all',[
                'verify' => false,
                'headers'       => $this->headers,
                '/delay/5',
                ['connect_timeout' => 3.14]
            ]);
            $body = $response_hotel->getBody();
            $response_hotel = json_decode($body, true);
            $data_hotel=$response_hotel['data'];
            return view('outlet.add',[
                                      'judul' => 'Add Oulet', 
                                      'data' => $data_hotel,
                                      'url_google_maps' => $resMSystem
                                    ]
            );
        } else {
            return redirect('home');
        }
      }
      catch (Throwable $e) {
        report($e);
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
	 * Function: Get Outlet berdasarkan hotel_id
	 * body: 
	 *	$request	: hotel_id
	 */
  public function get_hotel_id(Request $request)
  {
      try{
        $name = session('full_name');
        // $headers = [
        //   'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
        // ];
        $client = new Client(); //GuzzleHttp\Client
        $response_outlet = $client->request('GET', url('api').'/hotel/get_hotel_id?id='.$request->id,[
            'verify' => false,
            'headers'       => $this->headers,
            '/delay/5',
             ['connect_timeout' => 3.14]
        ]);

        $body = $response_outlet->getBody();
        $response_outlet = json_decode($body, true);
        return $data_outlet=$response_outlet['data']['data'];
      }
      catch (Throwable $e) {
        report($e);
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
     * Function: save data outlet
     * body: data outlet
     *	$request	: 
    */
    public function save_outlet(Request $request)
    {		
        try{
          // Validasi permission untuk melakukan add atau save data outlet
          if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-create')) {
            $messages = array('txtName.max' => __('message.name_max_length'),
              'txtName.regex' => __('message.name_regex'),
              'txtName.required' => __('message.name_required'),
              'txtDescription.required' => __('message.desc_required'),
              'txtDescription.max' => __('message.desc_max'),
              'txtDescription.regex' => __('message.desc_regex'),
              'txtAddress.required' => __('message.address_required'),
              'txtAddress.max' => __('message.address_max'),
              'txtAddress.regex' => __('message.address_regex'),
              'txtSeqNo.required' => __('message.seq_no_required'),
              'selectStatus.required' => __('message.status_required'),
              'txtMpgSecreetKey.required' => __('message.mpg_secret_required'),
              'txtMpgSecreetKey.max' => __('message.mpg_secret_key_max'),
              'txtMpgSecreetKey.alpha_num' => __('message.mpg_secret_key_alpha_num'),
              'txtMpgApiKey.required' => __('message.mpg_api_key_required'),
              'txtMpgApiKey.max' => __('message.mpg_api_key_max'),
              'txtLongitude.required' => __('message.longitude_required'),
              'txtLatitude.required' => __('message.latitude_required'),
              'txtTax.required' => __('message.tax_required'),
              'txtTax.numeric' => __('message.tax_numeric'),
              'txtService.required' => __('message.service_required'),
              'txtService.numeric' => __('message.service_numeric'),
              'txtOpen.required' => __('message.open_required'),
              'txtOpen.required' => __('message.close_required'),
            );

              $validator = Validator::make($request->all(), [
                  'selectIdHotel' => 'required',
                  'txtName' => 'required|max:100|regex:/^[A-Za-z0-9 ]+$/',
                  'txtAddress' => 'required|max:200|regex:/^[A-Za-z0-9-_!,. ]+$/',
                  'txtDescription' => 'required|regex:/^[\r\nA-Za-z0-9-_!:;,.\'&# ]+$/',
                  'selectStatus' => 'required',
                  'txtSeqNo' => 'required',
                  'txtMpgSecretKey' => 'required|alpha_num|max:50',
                  'txtMpgApiKey' => 'required|max:50',
                  // 'txtMpgMerchantId' => 'required|max:50',
                  'txtLongitude' => 'required',
                  'txtLatitude' => 'required',
                  'txtTax' => 'required|numeric',
                  'txtService' => 'required|numeric',
                  'txtOpen' => 'required',
                  'txtClose' => 'required',
                  
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
                  'hotel_id' => $request->post('selectIdHotel'),
                  'name' => $request->post('txtName'),
                  'address' => $request->post('txtAddress'),
                  'description' => $request->post('txtDescription'),
                  'seq_no' => $request->post('txtSeqNo'),
                  'status' => $request->post('selectStatus'),
                  // 'mpg_merchant_id' => $request->post('txtMpgMerchantId'),
                  'mpg_api_key' => $request->post('txtMpgApiKey'),
                  'mpg_secret_key' => $request->post('txtMpgSecretKey'),
                  'tax' => $request->post('txtTax'),
                  'service' => $request->post('txtService'),
                  'longitude' => $request->post('txtLongitude'),
                  'latitude' => $request->post('txtLatitude'),
                  'open_at' => $request->post('txtOpen'),
                  'close_at' => $request->post('txtClose'),    
                  'created_by' => session('full_name')          
              ];

              // Check mpg Merchant ID tidak di isi
              if(!empty($request->post('txtMpgMerchantId')) || $request->post('txtMpgMerchantId') != null ) {
                $data['mpg_merchant_id'] = $request->post('txtMpgMerchantId');
              } else {
                $data['mpg_merchant_id'] = "";
              }
      
                $client = new Client(); //GuzzleHttp\Client
                    $response = $client->request('POST', url('api').'/outlet/add_outlet',[
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
                  return redirect()->route('outlet.get_edit', ['id' =>  $id])
                  ->with('success', $response['message']);               
                  // echo'send message succes';
                }else{
                  return back()->withInput($request->all())
                              ->with('error',$response['message']);
                }
          } else {
            return redirect('home');
          }
		}
        catch (Throwable $e) {
            report($e);
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
	 * Function: delete data outlet
	 * body: outlet_id
	 *	$request	: 
	*/
  public function delete_outlet($id){
    try{
        // Validasi permission untuk melakukan delete data outlet
        if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-delete')) {
          $data = ['outlet_id' => $id];
          $client = new Client(); //GuzzleHttp\Client
              $response = $client->request('POST', url('api').'/outlet/delete_outlet',[
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
	 * Function: to view edit outlet
	 * body: data outlet
	 *	$request	: 
	*/
  public function get_edit_outlet($id){
        // Validasi Permission, hanya Admin, Admin Outlet dan Admin Hotel yang bisa akses
        if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-edit'))
        {
            $data = ['id' => $id];
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
              $response = $client->request('GET',url('api').'/outlet/get_outlet_detail?outlet_id='.$data['id'],[
                'verify' => false,
                'form_params'   => $data,
                'headers'       => $this->headers,
                '/delay/5',
                ['connect_timeout' => 3.14]
            ]);
            $body = $response->getBody();
            $response = json_decode($body, true);
            $data = $response['data'];
            $data = $data;
            
            $client = new Client(); //GuzzleHttp\Client
            $response_hotel = $client->request('GET', url('api').'/hotel/get_hotel_all',[
                'verify' => false,
                'headers' => $this->headers,
                '/delay/5',
                ['connect_timeout' => 3.14]
            ]);
            $body = $response_hotel->getBody();
            $response_hotel = json_decode($body, true);
            $data_hotel=$response_hotel['data'];
            // dd($data);
            return view('outlet.edit-outlet',[
                                              'data' => $data,
                                              'datax' => $data_hotel,
                                              'url_google_maps' => $resMSystem
                                              ]
                        );
        } else {
          return redirect('home');
        }
  }

  /**
	 * Function: edit data outlet
	 * body: data outlet
	 *	$request	: 
	*/
  public function edit_outlet(Request $request)
  {		
      try{
        // dd($request->all());
        // Validasi permission untuk melakukan edit data outlet
        if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-edit')) {
          $messages = array('txtRestaurantName.max' => __('message.name_max_length'),
              'txtRestaurantName.regex' => __('message.name_regex'),
              'txtRestaurantName.required' => __('message.name_required'),
              'txtDescription.required' => __('message.desc_required'),
              'txtDescription.max' => __('message.desc_max'),
              'txtDescription.regex' => __('message.desc_regex'),
              'txtAddress.required' => __('message.address_required'),
              'txtAddress.max' => __('message.address_max'),
              'txtAddress.regex' => __('message.address_regex'),
              'txtSeqNo.required' => __('message.seq_no_required'),
              'selectStatus.required' => __('message.status_required'),
              'txtMpgSecreetKey.required' => __('message.mpg_secret_required'),
              'txtMpgSecreetKey.max' => __('message.mpg_secret_key_max'),
              'txtMpgSecreetKey.alpha_num' => __('message.mpg_secret_key_alpha_num'),
              'txtMpgApiKey.required' => __('message.mpg_api_key_required'),
              'txtMpgApiKey.max' => __('message.mpg_api_key_max'),
              'txtLongitude.required' => __('message.longitude_required'),
              'txtLatitude.required' => __('message.latitude_required'),
              'txtTax.required' => __('message.tax_required'),
              'txtTax.numeric' => __('message.tax_numeric'),
              'txtService.required' => __('message.service_required'),
              'txtService.numeric' => __('message.service_numeric'),
              'txtOpen.required' => __('message.open_required'),
              'txtId.required' => __('message.close_required'),
              'selectIdHotel.required' => __('message.id_hotel_required')
            );
            $validator = Validator::make($request->all(), [
                'txtId' => 'required',
                'selectIdHotel' => 'required',
                'txtRestaurantName' => 'required|max:100|regex:/^[A-Za-z0-9 ]+$/',
                'txtAddress' => 'required|max:200|regex:/^[A-Za-z0-9-_!,. ]+$/',
                'txtDescription' => 'required|regex:/^[\r\nA-Za-z0-9-_!:;,.\'&# ]+$/',
                'selectStatus' => 'required',
                'txtSeqNo' => 'required',
                'txtMpgSecretKey' => 'required|max:50',
                'txtMpgApiKey' => 'required|max:50',
                // 'txtMpgMerchantId' => 'required|max:50',
                'txtLongitude' => 'required',
                'txtLatitude' => 'required',
                'txtTax' => 'required|numeric',
                'txtService' => 'required|numeric',
                'txtOpen' => 'required',
                'txtClose' => 'required',
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
                'id' => $request->post('txtId'),
                'hotel_id' => $request->post('selectIdHotel'),
                'name' => $request->post('txtRestaurantName'),
                'address' => $request->post('txtAddress'),
                'description' => $request->post('txtDescription'),
                'seq_no' => $request->post('txtSeqNo'),
                'status' => $request->post('selectStatus'),
                // 'mpg_merchant_id' => $request->post('txtMpgMerchantId'),
                'mpg_api_key' => $request->post('txtMpgApiKey'),
                'mpg_secret_key' => $request->post('txtMpgSecretKey'),
                'longitude' => $request->post('txtLongitude'),
                'latitude' => $request->post('txtLatitude'),
                'tax' => $request->post('txtTax'),
                'service' => $request->post('txtService'),
                'open_at' => $request->post('txtOpen'),
                'close_at' => $request->post('txtClose'), 
                'updated_by' => session('full_name')           
            ];

            // Check mpg Merchant ID tidak di isi
            if(!empty($request->post('txtMpgMerchantId')) || $request->post('txtMpgMerchantId') != null ) {
              $data['mpg_merchant_id'] = $request->post('txtMpgMerchantId');
            } else {
              $data['mpg_merchant_id'] = "";
            }

              $client = new Client(); //GuzzleHttp\Client
                  $response = $client->request('POST', url('api').'/outlet/edit_outlet',[
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
                return back()->withInput($request->all())
                              ->with('success',$response['message']);        
                // echo'send message succes';
              }else{
                return back()->withInput($request->all())
                            ->with('error',$response['message']);
              }
        } else {
          return redirect('home');
        }
  }
      catch (Throwable $e) {
          report($e);
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
	 * Function: to view images outlet
	 * body: data outlet
	 *	$request	: 
	*/
  public function images_outlet(Request $request){
    // validasi Permission hanya Admin, Admin Outlet dan Admin Hotel yang bisa akses
    if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-edit')) {
        return view('outlet.add-images',['judul' => 'Add Images',
                                        'txtOutletId' =>$request->post('txtOutletId'),
                                        'id' =>$request->post('txtId'),
                                        'name' => $request->post('txtName')]);
    } else {
        return redirect('home');
    }
  }

  /**
	 * Function: save/update data image outlet
	 * body: data image
	 *	$request	: 
	*/
  public function save_image(Request $request){
    try{
      // dd($request['txtJenis']);
      if(empty($request['txtJenis'])|| $request['txtJenis'] == null){
          $client = new Client(); //GuzzleHttp\Client
          $resSeqNo = $client->request('GET', url('api').'/outlet/get_image_outlet?id='.$request->txtId,[
              'verify' => false,
              'headers'       => $this->headers,
              '/delay/5',
               ['connect_timeout' => 3.14]
          ]);
          $body = $resSeqNo->getBody();
          $resSeqNo = json_decode($body, true);
          $seqNo[] = $resSeqNo['seq_no'];

          $resSeqNoAll = $client->request('GET', url('api').'/outlet/get_image_seq?id='.$request->txtOutletId,[
            'verify' => false,
            'headers'       => $this->headers,
            '/delay/5',
             ['connect_timeout' => 3.14]
        ]);
        $body = $resSeqNoAll->getBody();
        $resSeqNoAll = json_decode($body, true);
        // dd($resSeqNoAll);
        foreach ($resSeqNoAll as $value) {
            $resSeqAll[] = $value['seq_no'];
        }
        $arrayDiff=array_diff($resSeqAll,$seqNo);
        // dd($arrayDiff);
        // dd($request->txtSeqNo);
        foreach ($arrayDiff as $value) {
          //$resSeqAll[] = $value['seq_no'];
          if($value == $request->txtSeqNo){
            return redirect()->route('outlet.get_edit', ['id' => $request->post('txtOutletId')])
            ->with('error', 'Seq No has been taken');
          }
        }

        $messages = array('txtName.max' => __('message.name_max_length_15'),
          'txtName.regex' => __('message.name_regex'),
          'txtName.required' => __('message.name_required'),
          'images.required' => __('message.img_required'),
          'images.max' => __('message.img_max'),
          'images.image' => __('message.img_type'),
          'txtSeqNo.required' => __('message.seq_no_required'),
          'txtOutletId.required' => __('message.id_outlet_required'),
          'oldImages.required' => __('message.old_img_required'),
        );
          if(!empty($request->images)){
              $validator = Validator::make($request->all(), [
                'images' => 'required|mimes:jpeg,jpg|max:256',
                'txtName' => 'required|max:50|regex:/^[A-Za-z0-9 ]+$/',
                'txtSeqNo' => 'required',
                'txtOutletId' => 'required',
              ],$messages);
          }
          else{
            $validator = Validator::make($request->all(), [
              'txtId' => 'required',
              'txtName' => 'required|max:50|regex:/^[A-Za-z0-9 ]+$/',
              'txtSeqNo' => 'required',
              'txtOutletId' => 'required',
              'oldImages' => 'required',
            ],$messages);
          }
          // Check Resolution edit
            if(!empty($request->oldImages)){
              if(!empty($request->images)){
                $client = new Client(); //GuzzleHttp\Client
                $resMSystem = $client->request('GET', url('api').'/msytem/get_system_type_cd?system_type=img_resolution&system_cd=outlet',[
                    'verify' => false,
                    'headers'       => $this->headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
                ]);
                $body = $resMSystem->getBody();
                $resMSystem = json_decode($body, true);
                
                foreach ($resMSystem['data'] as $value) {
                  $resDimension[] = $value['system_value'];
                
                }
                // dd($resDimension);
                $data = getimagesize($request->images);
                $arrayDim[] = [$data[0].'x'.$data[1]];
        
                // dd($arrayDim[0][0]);
                if(in_array($arrayDim[0][0],$resDimension)){
                  $validator = Validator::make($request->all(), [
                    'images' => 'required|mimes:jpeg,jpg|max:256',
                    
                  ]);
                } else {
                  return back()->withInput($request->all())
                            ->with('error','Images has invalid dimension');
                }
              }
            }
      } else {
          // Check Resolution
         // Check Image Resolution ke tabel M_System
          $client = new Client(); //GuzzleHttp\Client
          $resMSystem = $client->request('GET', url('api').'/msytem/get_system_type_cd?system_type=img_resolution&system_cd=outlet',[
              'verify' => false,
              'headers'       => $this->headers,
              '/delay/5',
              ['connect_timeout' => 3.14]
          ]);
          $body = $resMSystem->getBody();
          $resMSystem = json_decode($body, true);
          
          foreach ($resMSystem['data'] as $value) {
            $resDimension[] = $value['system_value'];
           
          }
          // dd($resDimension);
          $data = getimagesize($request->images);
          $arrayDim[] = [$data[0].'x'.$data[1]];
  
            $messages = array('txtName.max' => __('message.name_max_length_15'),
            'txtName.regex' => __('message.name_regex'),
            'txtName.required' => __('message.name_required'),
            'images.required' => __('message.img_required'),
            'images.max' => __('message.img_max'),
            'images.image' => __('message.img_type'),
            'txtSeqNo.required' => __('message.seq_no_required'),
            'txtOutletId.required' => __('message.id_outlet_required'),
            'oldImages.required' => __('message.old_img_required'),
          );
          if(in_array($arrayDim[0][0],$resDimension)){
            $validator = Validator::make($request->all(), [
              'images' => 'required|mimes:jpeg,jpg|max:256',
              'txtName' => 'required|max:50|regex:/^[A-Za-z0-9 ]+$/',
              'txtSeqNo' => 'required|unique:fboutlet_images,seq_no,NULL,id,fboutlet_id,'.$request->txtOutletId,
              'txtOutletId' => 'required',
            ],$messages);
          } else {
            return back()->withInput($request->all())
                      ->with('error','Images has invalid dimension');
          }
         
      }
      
      if ($validator->fails()) {
          // return response gagal
          return back()->withInput($request->all())
                      ->with('error',$validator->errors()->first());
                     }

      $client = new Client(); //GuzzleHttp\Client
      if(empty($request['images'])){
        $response = $client->request('POST', url('api').'/outlet/add_image_outlet',[
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
              'name'     => 'fboutlet_id',
              'contents' => $request->post('txtOutletId'),
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
            $file_path          = $file->getPathname();
            $file_mime          = $file->getMimeType('image');
            $file_uploaded_name = $file->getClientOriginalName();

                $response = $client->request('POST', url('api').'/outlet/add_image_outlet',[
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
                          'name'     => 'fboutlet_id',
                          'contents' => $request->post('txtOutletId'),
                        ],
                        [
                          'name'     => 'seq_no',
                          'contents' => $request->post('txtSeqNo'),
                        ],
                        [
                          'name'     => 'jenis',
                          'contents' => $request->post('txtJenis'),
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
              //dd($response);
              if($response['status'] == true && $response['data'] != null){           
                return redirect()->route('outlet.get_edit', ['id' => $request->post('txtOutletId')])
                        ->with('success', $response['message']);
              }
              else{
                return back()->withInput($request->all())
                            ->with('error',$response['message']);
              }
      }
        catch (Throwable $e) {
            report($e);
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
	 * Function: go to view edit data  image outlet
	 * body: data image
	 *	$request	: 
	*/
public function get_edit_images(Request $request){
  try{
        // Validasi Permission, hanya Admin, Admin Outlet dan Admin Hotel yang bisa akses
        if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-edit')) {
              $txtOutletId =$request['txtOutletId'];
              $request=json_decode($request['data']);
              $data = ['id' => $request->id,
                        'name' => $request->name,
                        'filename' => $request->filename,
                        'seq_no' => $request->seq_no]; 
                        return view('outlet.add-images',['judul' => 'Edit Image',
                                                'txtOutletId' =>$txtOutletId,'data' =>$data]);
        } else {
              return redirect('home');
        }
      }
      catch (Throwable $e) {
          report($e);
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
	 * Function: delete data image outlet
	 * body: id_image
	 *	$request	: 
	*/
  public function delete_images($id){
    try{
      // Validasi permission hanya Admin, Admin Outlet dan Admin Hotel yg bisa
      if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-edit')) {
        $data = ['id' => $id];
        $client = new Client(); //GuzzleHttp\Client
            $response = $client->request('POST', url('api').'/outlet/delete_image_outlet',[
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
