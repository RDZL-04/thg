<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Validator;
use Jenssegers\Agent\Agent;
use App\Http\Controllers\LOG\ErorrLogController;
use App\Models\User;
use Svg\Tag\Rect;
use Auth;

/*
|--------------------------------------------------------------------------
| User Web Controller
|--------------------------------------------------------------------------
|
| Validate, Authorize user.
| This user controller will control user data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: December 11, 2020
*/

class UserController extends Controller
{
    //
    public function __construct() {
      // Set header for API Request
      $this->headers = [
          'x-api-key' => env('API_KEY'),
        ];
      $this->LogController = new ErorrLogController;
    }

    public function index(Request $request) 
     {
      $request->session()->forget('error');
      $request->session()->forget('success');
         return view('auth.login-user');
     }

     /**
	 * Function: acton login web
	 * body: email, password
	 *	$request	: 
	*/
    public function action_login(Request $request)
    {
      $agent = new Agent();
      $browser = $agent->browser();
    // dd($browser);
        $data = [
            'email' => $request->email,
            'password' => $request->password,
            'device' => $browser,
         ];
        $data = $data+['device' => 'web'];

        //  $headers = [
        //     'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
        //   ];
          $client = new Client(); //GuzzleHttp\Client
          // $response = $client->request('POST', 'https://thg.arkamaya.net/api/login',[
          $response = $client->request('POST', url('api').'/login',[  
            // $response = $client->request('POST', 'http://thg-web/api/login',[
              'verify' => false,
              'form_params'   => $data,
              'headers'       => $this->headers,
              '/delay/5',
               ['connect_timeout' => 3.14]
          ]);
          $body = $response->getBody();
          $response = json_decode($body, true);
          $data=$response['data'];
        //   dd($data);
          if($response['status'] == true){
            //input data to session
              session()->put('id',$data['id']);
              session()->put('full_name',$data['full_name']);
              session()->put('role',$data['nm_role']);
              $image = $data['image'];
              if($image == null){
                $image = "user-images/default.jpg";
              }
              session()->put('image',$image);
              $value = session('token');
              session()->put('token',$data['token']);
            //   $data = json_decode(json_encode($data),true);
              // return $this->dashboard();
			   //$credentials = $request->only('email', 'password');
				
				$fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';
				if(auth()->attempt(array($fieldType => $request->email, 'password' => $request->password)))
				{
				//if (Auth::attempt($credentials)) {
					// Authentication passed...
					$data = array('id' => session('id'), 'token' => session('token'));
					User::update_token($data);
					return redirect()->intended('home');
				}
				
              return redirect('home');
            //   echo $value;
          }else{
            return back()->withInput($request->all())
                            ->with('error',$response['message']);
          }
    } 

    /**
     * go to dashboard page
     */
    public function dashboard(){
      $fullname = session()->get('full_name');
      // dd($fullname);
      return view('dashboard',['fullname' => $fullname]);
    }

    /**
     * Function: route and send data to index
     * body: 
     *	$request	: 
    */
    public function indexUser()
    {
      try{
        if((session()->get('role')=='Admin') || (strtoupper(session()->get('role'))=='ADMIN IT') ){
          $name = session('full_name');
          $role =session('role');
          if(strtolower($role) == 'admin' || strtoupper($role) == 'ADMIN IT' ){
              $url = '/user/get_user_all';
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
          return view('user.list',['judul' => 'User',
          'data' =>$data]);
        }          
          
      }catch (Throwable $e) {
          return view('user.list',['judul' => 'User',
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
          // if((session()->get('role')=='Admin')){
          if(Auth::user()->permission('utility')){
            $name = session('full_name');
            // $headers = [
            //   'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
            // ];
    
            $client = new Client(); //GuzzleHttp\Client
                // $response = $client->request('GET', 'http://thg-web/api/hotel/get_hotel_all',[
                $response = $client->request('GET', url('api').'/user/get_user_all',[
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
        }catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_data',
                'actions' => 'get data user',
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
     * Function: get user by id
     * body: 
     *	$request	: 
    */
    public function get_user_id($id_user)
    {
        // dd($id_user);
        try{
          // if((session()->get('role')=='Admin')){
          if(Auth::user()->permission('utility')){
            $name = session('full_name');
            // $headers = [
            //   'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
            // ];
    
            $client = new Client(); //GuzzleHttp\Client
                // $response = $client->request('GET', 'http://thg-web/api/hotel/get_hotel_all',[
                $response = $client->request('GET', url('api').'/user/get_user?id='.$id_user,[
                'verify' => false,
                'headers'       => $this->headers,
                '/delay/5',
                 ['connect_timeout' => 3.14]
            ]);
            $body = $response->getBody();
            $response = json_decode($body, true);
            $data=$response['data'];
            // dd($data);
            return $data;
          }
        }catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_user_id',
                'actions' => 'get data user by id',
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
    public function get_province(Request $request)
    {
      try{
        $client = new Client();
        $responseProvince = $client->request('GET', url('api').'/msytem/get_system_type_cd?system_type=province_'.$request->country_id,[
          'verify' => false,
          'headers' => $this->headers,
          '/delay/5',
          ['connect_timeout' => 3.14]
        ]);
        $bodyProvince = $responseProvince->getBody();
        $responseProvince = json_decode($bodyProvince, true);
        if($responseProvince['status']){
          return $responseProvince['data'];
        }
        else {
          $responseProvince['data'] = null;
          return $responseProvince['data'];
        }
        
      }catch (Throwable $e) {
        report($e);
        $error = ['modul' => 'get_province',
            'actions' => 'get data province',
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
     * Function: get detail user
     * body: 
     *	$request	: 
    */
    public function get_user(Request $request)
    {
        // dd($id);
        try{
            $id_user = session('id');
            $token = session()->get('token');
            // dd($token);
            // dd($id_user);
            // $headers = [
            //   'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
            // ];
    
            $client = new Client(); //GuzzleHttp\Client
                // $response = $client->request('GET', 'http://thg-web/api/hotel/get_hotel_all',[
                $response = $client->request('GET', url('api').'/user/get_user?id='.$id_user,[
                'verify' => false,
                'headers'       => $this->headers,
                '/delay/5',
                 ['connect_timeout' => 3.14]
                ]);
                $responseCountry = $client->request('GET', url('api').'/msystem/get_list_country',[
                  'verify' => false,
                  'headers' => $this->headers,
                  '/delay/5',
                  ['connect_timeout' => 3.14]
                ]);
                $responseGender = $client->request('GET', url('api').'/msytem/get_system_type_cd?system_type=gender',[
                  'verify' => false,
                  'headers' => $this->headers,
                  '/delay/5',
                  ['connect_timeout' => 3.14]
              ]);
            // dd($responseGender);
            $body = $response->getBody();
            $bodyCountry = $responseCountry->getBody();
            $bodyGender = $responseGender->getBody();
            $response = json_decode($body, true);
            $responseCountry = json_decode($bodyCountry, true);
            $responseGender = json_decode($bodyGender, true);
            if($responseGender['status'] && $responseCountry['status'] && $response['status']){
              $data=$response['data'];
              if($data['country'] != null){
                $responseProvince = $client->request('GET', url('api').'/msytem/get_system_type_cd?system_type=province_'.$data['country'],[
                  'verify' => false,
                  'headers' => $this->headers,
                  '/delay/5',
                  ['connect_timeout' => 3.14]
                ]);
                $bodyProvince = $responseProvince->getBody();
                $responseProvince = json_decode($bodyProvince, true);
                if($responseProvince['status']){
                  return view('user.profile',['judul' => 'Profile',
                              'data' => $data,
                              'dataCountry' => $responseCountry['data'],
                              'dataGender' => $responseGender['data'],
                              'dataProvince' => $responseProvince['data'],
                              ]);
                }
                return view('user.profile',['judul' => 'Profile',
                                          'data' => $data,
                                          'dataCountry' => $responseCountry['data'],
                                          'dataGender' => $responseGender['data'],
                                          'dataProvince' => null,
                                          ]);
              }
              return view('user.profile',['judul' => 'Profile',
                                          'data' => $data,
                                          'dataCountry' => $responseCountry['data'],
                                          'dataGender' => $responseGender['data'],
                                          'dataProvince' => null,
                                          ]);
            }else{
              return back()->with('error',$response['message']);  
            }
            
            
        }catch (Throwable $e) {
            report($e);
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
	 * Function: Save data user
	 * body: 
	 *	$request	: 
	*/
  public function save_data(Request $request)
  {
      try{
        // if((session()->get('role')=='Admin')){
        if(Auth::user()->permission('utility')){
          // $headers = [
          //     'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
          // ];
          
          $client = new Client(); //GuzzleHttp\Client
              $response = $client->request('POST',url('api').'/user/add_user',[
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
      }catch (Throwable $e) {
          report($e);
          $error = ['modul' => 'save_user',
                'actions' => 'save data user',
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
          // $headers = [
          //     'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
          // ];
          
          $client = new Client(); //GuzzleHttp\Client
              $response = $client->request('POST',url('api').'/user/edit_user',[
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
      }catch (Throwable $e) {
          report($e);
          $error = ['modul' => 'edit_data',
                'actions' => 'edit data user',
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
	 * Function: delete data user
	 * body: param[id]
	 *	$request	: 
	*/
    
  public function delete_user(Request $request)
  {
    try
    {
      // if((session()->get('role')=='Admin')){
      if(Auth::user()->permission('utility')){
        // $headers = [
        //     'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
        // ];
        
        $client = new Client(); //GuzzleHttp\Client
            $response = $client->request('POST',url('api').'/user/delete_user',[
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
    }catch (Throwable $e) {
        report($e);
        $error = ['modul' => 'delete_user',
                'actions' => 'delete data user',
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

  public function edit_profile(Request $request){

    try{
      // dd($request->all());
        $token = session('token');
      if($request['date_of_birth'] == null){
          $request['date_of_birth'] = "";
      }
      if($request['city'] == null){
          $request['city'] = "";
      }
      if($request['country'] == null){
          $request['country'] = "";
      }
      if($request['state_province'] == null){
          $request['state_province'] = "";
      }
      if($request['postal_cd'] == null){
          $request['postal_cd'] = "";
      }
      if($request['address'] == null){
          $request['address'] = "";
      }
      if($request['gender'] == null){
              $request['gender'] = ""; 
      }                
      
      
         $headers = 
            $this->headers + [ 
            'Authorization' => 'Bearer '.$token,];
        // dd($headers);
          
          $client = new Client(); //GuzzleHttp\Client
              $response = $client->request('POST', url('api').'/profil/edit_profile',[
                'verify' => false,
                'headers' => $headers,
                '/delay/5',
                'form_params' => $request->all(),
                ['connect_timeout' => 3.14]
          ]);
          // dd($response);
          $body = $response->getBody();
          $response = json_decode($body, true);
          // dd($response);   
          if($response['status'] == true && $response['data'] != null){
            $data = $response['data'];
            $id = $data['id'];
            return redirect()->route('user.detail.profile')
                ->with('success', $response['message']);  
            // return redirect()->route('hotel.get_edit', ['id' =>  $id])
            // ->with('success', $response['message']);               
            // echo'send message succes';
          }else{
            return back()->withInput($request->all())
                        ->with('error',$response['message']);
          }
    }
    catch (Throwable $e) {
        report($e);
        $error = ['modul' => 'edit_profile',
                'actions' => 'edit data profile',
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
  

  public function edit_profile_image(Request $request){
    // dd($request->all());
    // echo 'ada';
    try{
      $messages = array(
          'images.required' => __('message.img_required'),
          'images.max' => __('message.img_max'),
          'images.image' => __('message.img_type'),
          'txtSeqNo.required' => __('message.seq_no_required'),
          'txtIdUser.required' => __('message.id_user_required'),
          'oldImages.required' => __('message.old_img_required'),
        );
        $validator = Validator::make($request->all(), [
            'txtIdUser' => 'required',
            'image' => 'required|image:jpeg,jpg,png,PNG|max:2048'
        ],$messages);
        // dd($request->all());
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
        $token = session('token');
         $headers = 
          $this->headers +[
            'Authorization' => 'Bearer '.$token,];
          $file               = request('image');
        //   dd(request('images'));
        //   $file_path          = request('images')->getPathname();
        //   $file_mime          = $file->getMimeType('image');
        //   $file_uploaded_name = $file->getClientOriginalName();
        $client = new Client();//guzzle client
                      $response = $client->request('POST', url('api').'/profil/add_image',[
                      'verify' => false,
                      'multipart' => [
                        [
                          'name'     => 'id',
                          'contents' => $request->post('txtIdUser'),
                        ],
                        [
                            'name'     => 'image',
                            'contents' => fopen($file, 'r')
                        ],
                            ],
                          
                    'headers'       => $headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
                ]);
          $body = $response->getBody();
          $response = json_decode($body, true);
        //   dd($response);
          if($response['status'] == true && $response['data'] != null){
            
            return redirect()->route('user.detail.profile')
                ->with('success', $response['message']);    
            // return redirect()->route('hotel.get_edit', ['id' =>  $id])
            // ->with('success', $response['message']);               
            // echo'send message succes';
          }else{
            return back()->withInput($request->all())
                        ->with('error',$response['message']);
          }
    }
    catch (Throwable $e) {
      report($e);
      $error = ['modul' => 'edit_profile_image',
              'actions' => 'edit data profile image',
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
	 * Function: Forgot Password, Goto View auth.forgot-password
	 * body: 
	 *	$request	: 
	*/
  public function forgot_password(Request $request)
  {
    try
    {
      return view('auth.forgot-password');
    }
    catch (Throwable $e) {
        report($e);
        $error = ['modul' => 'forgot_password',
                'actions' => 'Goto View Forgot Password User Outlet',
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
	 * Function: Send Email, HIT API forgot_password_user_outlet
	 * body: param @email
	 *	$request	: 
	*/
  public function forgot_pass_send_email(Request $request)
  {
    try
    {
      // Construct $data param
      $data = [ 'email' => $request->email ];
      $client = new Client(); //GuzzleHttp\Client
      $response = $client->request('POST', url('api').'/outlet/forgot_password_user_outlet',[  
          'verify' => false,
          'form_params'   => $data,
          'headers'       => $this->headers,
          '/delay/5',
          ['connect_timeout' => 3.14]
      ]);
      $body = $response->getBody();
      $response = json_decode($body, true);
      // dd($response);
      if($response['status']){
        $data=$response['data'];
          // Return to View Input OTP with email value
          // return view('auth.input-otp',[ 'email' => $data['email'] ])->with('success', 'Please Check your email');
            // edit : arka.moharifrifai 2022-06-03
        return view('auth.input-otp',[ 'email' => $data['email'], 'next_send' => $data['next_send'] ])->with('success', 'Please Check your email');
            // edit : arka.moharifrifai 2022-06-03
      } else {
        return back()->withInput($request->all())->with('error',$response['message']);
      }
    }
    catch (Throwable $e) {
        report($e);
        $error = ['modul' => 'forgot_pass_send_email',
                'actions' => 'HIT API forgot_password_user_outlet',
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
	 * Function: Send Email, HIT API forgot_password_user_outlet
	 * body: param @email
	 *	$request	: 
	*/
  public function forgot_pass_resend_email(Request $request)
  {
    try
    {
      $request->session()->forget('error');
      // Construct $data param
      $data = [ 'email' => $request->email ];
      $client = new Client(); //GuzzleHttp\Client
      $response = $client->request('POST', url('api').'/outlet/forgot_password_user_outlet',[  
          'verify' => false,
          'form_params'   => $data,
          'headers'       => $this->headers,
          '/delay/5',
          ['connect_timeout' => 3.14]
      ]);
      $body = $response->getBody();
      $response = json_decode($body, true);
      return $response;
    }
    catch (Throwable $e) {
        report($e);
        $error = ['modul' => 'forgot_pass_send_email',
                'actions' => 'HIT API forgot_password_user_outlet',
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
	 * Function: Check Input Request OTP, HIT API otp_verifiy_user_outlet
	 * body: param @email
	 *	$request	: 
	*/
  public function forgot_pass_check_otp(Request $request)
  {
    try
    {
      // Session Clear
      $request->session()->forget('error');
      // Construct $data param
      $data = [ 'email' => $request->email, 'otp' => $request->otp ];
      $client = new Client(); //GuzzleHttp\Client
      $response = $client->request('POST', url('api').'/outlet/otp_verifiy_user_outlet',[  
          'verify' => false,
          'form_params'   => $data,
          'headers'       => $this->headers,
          '/delay/5',
          ['connect_timeout' => 3.14]
      ]);
      $body = $response->getBody();
      $response = json_decode($body, true);
      // dd($response);
      if($response['status']){
        $data=$response['data'];
          // Return to View Input NEW Password with data return from API
          return view('auth.reset-password',[ 'data' => $data ])->with('success', 'Create your New Password');
      } else {
          $data=$response['data'];
          $request->session()->flash('error',$response['message']); 
          // return view('auth.input-otp',[ 'email' => $data['email'] ])->with('error', $response['message']);
          // edit : arka.moharifrifai 2022-06-03
          return view('auth.input-otp',[ 'email' => $data['email'], 'next_send' => $data['next_send'] ])->with('error', $response['message']);
          // edit : arka.moharifrifai 2022-06-03
      }
    }
    catch (Throwable $e) {
        report($e);
        $error = ['modul' => 'forgot_pass_send_email',
                'actions' => 'HIT API forgot_password_user_outlet',
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
	 * Function: Check Password Change then HIT API outlet/update_password_user_outlet
	 * body: param @email
	 *	$request	: 
	*/
  public function forgot_pass_reset_password(Request $request)
  {
    try
    {
      $validator = Validator::make($request->all(), [
        'email' => 'required',
        'token' => 'required',
        'password' => 'required|min:8|max:15|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{6,}$/'
      ]);
      if ($validator->fails()) {
          $response = [
              'status' => false,
              'code' => 400 ,
              'message' => $validator->errors()->first(),
          ];
          $request->session()->flash('error',$response['message']); 
          return view('auth.reset-password',[ 'data' => $request->all() ]);
      }
      // Session Clear
      $request->session()->forget('error');
      // Construct $data param
      $data = [ 'email' => $request->email, 'token' => $request->token, 'password' => $request->password ];
      $client = new Client(); //GuzzleHttp\Client
      $response = $client->request('POST', url('api').'/outlet/update_password_user_outlet',[  
          'verify' => false,
          'form_params'   => $data,
          'headers'       => $this->headers,
          '/delay/5',
          ['connect_timeout' => 3.14]
      ]);
      $body = $response->getBody();
      $response = json_decode($body, true);
      // dd($response);
      if($response['status']){
        $data=$response['data'];
          // Return to View Input NEW Password with data return from API
          $request->session()->flash('success', $response['message']); 
          return view('auth.success-change');
      } else {
          $request->session()->flash('error',$response['message']); 
          return view('auth.input-otp',[ 'email' => $data['email'] ])->with('error', $response['message']);
      }
    }
    catch (Throwable $e) {
        report($e);
        $error = ['modul' => 'forgot_pass_reset_password',
                'actions' => 'HIT API forgot_pass_reset_password',
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
  
    public function logout(Request $request)
	{
		$data = array('id' => session('id'), 'token' => null);
		User::update_token($data);
		
		Auth::logout();
		$request->session()->flush();
		
		return redirect('login_user');
    }


    /**
	 * Function: change password from dashboard user
	 * body: id, password, old_password
	 *	$request	: 
	*/
    public function change_password(Request $request){

      try{
        // return $request->all();
          $token = session('token');
          $agent = new Agent();
      $browser = $agent->browser();
    // dd($browser);
        $data = [
            'id' => $request->id,
            'old_password' => $request->old_password,
            'password' => $request->password,
            'device' => $browser,
         ];
           $headers = 
              $this->headers + [ 
              'Authorization' => 'Bearer '.$token,];
          // dd($headers);
            
            $client = new Client(); //GuzzleHttp\Client
                $response = $client->request('POST', url('api').'/user/change_password',[
                  'verify' => false,
                  'headers' => $headers,
                  '/delay/5',
                  'form_params' => $data,
                  ['connect_timeout' => 3.14]
            ]);
            // dd($response);
            $body = $response->getBody();
            $response = json_decode($body, true);
            // dd($response);   
            if($response['status'] == true && $response['data'] != null){
              // return $response;
              $data = [
                'email' => $response['data']['email'],
                'password' => $request->password,
             ];
             session()->put('token',$response['data']['token']);
             $fieldType = filter_var($response['data']['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';
             if(auth()->attempt(array($fieldType => $response['data']['email'], 'password' => $request->password)))
             {
             //if (Auth::attempt($credentials)) {
               // Authentication passed...
               $data = array('id' => session('id'), 'token' => session('token'));
               User::update_token($data);
              //  return redirect()->intended('home');
              return $response;
             }
             $response = [
              'status' => true,
              'message' => __('message.relogin_failed'),
              'code' => 200,
              'data' => null, 
          ];
             return $response;
            }else{
              return $response;
            }
      }
      catch (Throwable $e) {
          report($e);
          $error = ['modul' => 'edit_profile',
                  'actions' => 'edit data profile',
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
