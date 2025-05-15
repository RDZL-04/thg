<?php
/*
|--------------------------------------------------------------------------
| Hall WEB Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process Hall, Hall Category data
| 
| @author: rangga.muharam@arkamaya.co.id 
| @update: April 1st, 2021
*/

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\LOG\ErorrLogController;
use GuzzleHttp\Client;
use Validator;
use Auth;

class HallController extends Controller
{
    // Construct Class
    public function __construct() {
        // Set header for API Request
        $this->headers = [
            'x-api-key' => env('API_KEY'),
          ];
          $this->LogController = new ErorrLogController;
    }

    /**
     * Function: route to view index Hall
     * body: 
     *	$request	: 
    */
    public function hall_index(){
        try{
              // Validasi Permission, hanya Admin dan User dengan mice-hall-list
              if((session()->get('role')=='Admin') || Auth::user()->permission('mice-hall-list'))
              {
                  $name = session('full_name');
          
                  // Get Hotel List
                  $client = new Client(); //GuzzleHttp\Client
                  if(strtolower(session()->get('role')) == 'admin') {
                    $response_hotel = $client->request('GET', url('api').'/mice/get_hotel_mice',[
                        'verify' => false,
                        'headers'       => $this->headers,
                        '/delay/5',
                        ['connect_timeout' => 3.14]
                    ]);
                  } else {
                    $response_hotel = $client->request('GET', url('api').'/mice/get_hotel_mice_with_hotel_user?user_id='.session()->get('id'),[
                      'verify' => false,
                      'headers'       => $this->headers,
                      '/delay/5',
                      ['connect_timeout' => 3.14]
                    ]);
                  }
                  $body = $response_hotel->getBody();
                  $response_hotel = json_decode($body, true);
                  $dataHotel=$response_hotel['data'];

                  // Get data Hall
                  $client = new Client(); //GuzzleHttp\Client
                  if(strtolower(session()->get('role')) == 'admin') {
                        $response = $client->request('GET', url('api').'/mice/get_hall_all',[
                          'verify' => false,
                          'headers' => $this->headers,
                          '/delay/5',
                          ['connect_timeout' => 3.14]
                        ]);
                  } else {
                    $response = $client->request('GET', url('api').'/mice/get_hall_all?user_id='.session()->get('id'),[
                      'verify' => false,
                      'headers'  => $this->headers,
                      '/delay/5',
                      ['connect_timeout' => 3.14]
                    ]);
                  }
                  $body = $response->getBody();
                  $response = json_decode($body, true);
                  $data = $response['data'];
                  $dataCategory = $response['dataCategory'];
                  
                  return view('hall.list',[
                                          'data' => $data,
                                          'dataHotel' => $dataHotel,
                                          'dataCategory' => $dataCategory
                                          ]);
              } else {
                return redirect('home');
              }
        }
        catch (Throwable $e) {
          report($e);
          $error = ['modul' => 'hall_index',
                      'actions' => 'Goto View Hall List Index',
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
        
    } // end function List

    /**
     * Function: go to view Add Hall
     * body:  
     *	$request	: 
    */
    public function hall_new(){
      try {
            // Validasi Permission, hanya Admin dan User dengan mice-hall-create
            if((session()->get('role')=='Admin') || Auth::user()->permission('mice-hall-create'))
            {
                $name = session('full_name');
                // GET data Hotel
                // Validasi jika user merupakan admin
                $client = new Client(); //GuzzleHttp\Client
                if(strtoupper(session('role')) == 'ADMIN'){
                    $response = $client->request('GET', url('api').'/mice/get_hotel_mice',[
                        'verify' => false,
                        'headers'       => $this->headers,
                        '/delay/5',
                        ['connect_timeout' => 3.14]
                    ]);
                } else {
                // get all Mice Category berdasarkan User Hotel
                    $response = $client->request('GET', url('api').'/mice/get_hotel_mice_with_hotel_user?user_id='. session('id'),[
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
                $body_msystem = $response_system->getBody();
                $response_msystem = json_decode($body_msystem, true);
                $dataCategory = $response_msystem['data'];

                // Get Seq No 
                $responseSequence = $client->request('GET', url('api').'/mice/get_hall_all',[
                    'verify' => false,
                    'headers'       => $this->headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
                ]);
                $bodySeq = $responseSequence->getBody();
                $responseSequence = json_decode($bodySeq, true);
                $dataSeq = $responseSequence['data'];
                if(count($dataSeq) == 0){
                  $SeqNo = 1;
                } else {
                  $SeqNo = $dataSeq[count($dataSeq)-1]['seq'] + 1;
                }
                
                return view('hall.add',[
                                        'judul' => __('button.add'),
                                        'dataCategory' => $dataCategory,
                                        'dataHotel' => $dataHotel,
                                        'seqNo' => $SeqNo
                                        ]
                );
            } else {
                return redirect('home');
            }
      }  
      catch (Throwable $e) {
          report($e);
          $error = ['modul' => 'hall_new',
                      'actions' => 'Goto View Add Hall',
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
     * Function: delete data Hall dan Hall Category
     * body: 
     *	$request	: 
    */
    public function delete_hall($id){
      try{
            // Validasi Permission, hanya Admin dan User dengan mice-hall-delete
            if((session()->get('role')=='Admin') || Auth::user()->permission('mice-hall-delete'))
            {
                $data = ['id' => $id];
                $client = new Client(); //GuzzleHttp\Client
                    $response = $client->request('POST', url('api').'/mice/delete_hall',[
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
     * Function: get data Mice Category Name dari Mice Category, Hotel dan Msystem
     * param : hotel_id
     *	$request	: 
    */
    public function get_hotel_mice_msystem($hotel_id){
      try{
          $client = new Client(); //GuzzleHttp\Client
              $response = $client->request('GET', url('api').'/mice/get_hotel_mice_msystem?hotel_id='.$hotel_id,[
              'verify' => false,
              'headers' => $this->headers,
              '/delay/5',
              ['connect_timeout' => 3.14]
          ]);
          $body = $response->getBody();
          $response = json_decode($body, true);
          if($response['status'] == true){
              return response()->json($response['data'], 200);
                          
              // echo'send message succes';
          }else{
              return back()->with('error',$response['message']);
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
     * Function: save hall data dan return to View Edit Hall
     * utk add Hall Images
     * param : 
     *	$request	: 
    */
    public function hall_add(Request $request){
      try{

          $name = session('full_name');
          $validator = Validator::make($request->all(), [
              'selectIdHotel' => 'required',
              'txtName' => 'required',
              'txtSize' => 'required',
              'miceCategoryId' => 'required',
              'txtCapacity' => 'required',
              'txtSeqNo' => 'required',
              'description' => 'required',
              'layout' => 'required'
          ]);
          if ($validator->fails()) {
              // return response gagal
              return back()->withInput($request->all())
                          ->with('error',$validator->errors()->first());
          }
          // Construct $data param to Hit API add Hall
          $file = request('layout');
          $data = [
              [
                'name'     => 'name',
                'contents' => $request->post('txtName'),
              ],
              [
                'name'     => 'description',
                'contents' => $request->post('description'),
              ],
              [
                'name'     => 'capacity',
                'contents' => $request->post('txtCapacity'),
              ],
              [
                'name'     => 'size',
                'contents' => $request->post('txtSize'),
              ],
              [
                'name'     => 'seq',
                'contents' => $request->post('txtSeqNo'),
              ],
              [
                'name'     => 'layout',
                'contents' => fopen($file, 'r')
              ],
              [
                'name'     => 'created_by',
                'contents' => $name,
              ]
            ];
          
          // Check request 
          if(!empty(request('mice_offers')) && (request('mice_offers') != null)) {
            $fileMice = request('mice_offers');
            $dataOffers[] = ['name' => 'mice_offers', 'contents' => fopen($fileMice, 'r')] ;
            $data = array_merge($data, $dataOffers);
          }
          // Looping Mice Category utk prepare data
          foreach($request->miceCategoryId as $key => $mice_cat_id){
            $dataMice[$key] = ['name' => 'mice_category_id['.$key.']', 'contents' => $mice_cat_id];
          }
          $reqDummy = $request->all();
          // dd($data);
          // data are all setup
          $data = array_merge($data,$dataMice);
          // Guzzle init
          $client = new Client(); //GuzzleHttp\Client
              $response = $client->request('POST', url('api').'/mice/add_hall',[
              'verify' => false,
              'multipart' => $data,
              'headers'  => $this->headers,
              '/delay/5',
              ['connect_timeout' => 3.14]
          ]);
          $body = $response->getBody();
          $response = json_decode($body, true);
          if($response['status'] == true){
            $id = $response['data']['id'];
            return redirect()->route('hall.get_edit', ['id' =>  $id])->with('success', $response['message']);  
                
          }else{
              $resCat =  $client->request('GET', url('api').'/mice/get_hotel_mice_msystem?hotel_id='.$reqDummy['selectIdHotel'],[
                'verify' => false,
                'headers' => $this->headers,
                '/delay/5',
                ['connect_timeout' => 3.14]
              ]);
              $body = $resCat->getBody();
              $resCat = json_decode($body, true);
              $reqDummy['theCat'] = $resCat['data'];
              // dd($reqDummy);
              return back()->withInput($reqDummy)->with('error',$response['message']);
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
     * Function: to view edit Hall
     * body: data outlet
     *	$request	: 
    */
    public function get_edit_hall($id){
      try{
            // Validasi Permission, hanya Admin dan User dengan mice-hall-edit
            if((session()->get('role')=='Admin') || Auth::user()->permission('mice-hall-edit'))
            {
                // Get data Hall Detail
                $client = new Client(); //GuzzleHttp\Client
                  $response = $client->request('GET',url('api').'/mice/get_hall_detail?hall_id='.$id,[
                    'verify' => false,
                    'headers' => $this->headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
                ]);
                $body = $response->getBody();
                $response = json_decode($body, true);
                $data = $response['data'];
                $hotel_id = $data['hotel_id'];
                // Get Data Mice Category berdasarkan Hotel_id
                $client = new Client(); //GuzzleHttp\Client
                  $responseMice = $client->request('GET',url('api').'/mice/get_hotel_mice_msystem?hotel_id='.$hotel_id,[
                    'verify' => false,
                    'headers' => $this->headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
                ]);
                $bodyMice = $responseMice->getBody();
                $responseMice = json_decode($bodyMice, true);
                $dataMice = $responseMice['data'];

                return view('hall.edit',[ 
                                        'judul' => 'Edit',
                                        'data' => $data,
                                        'dataMice' => $dataMice
                                        ]);
            } else {
                return redirect('home');
            }
      } catch (Throwable $e) {
        report($e);
        $error = ['modul' => 'get_edit_hall',
                    'actions' => 'Goto View Edit Hall',
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
     * Function: Hit API edit_hall
     * body: data image
     *	$request	: 
    */
    public function edit_hall(Request $request){
      try{
          // dd($request->all());
          $name = session('full_name');
          // Jika Layout Hall masih yang lama, maka pakai nilai oldLayout
          if(empty($request['layout'])){
              $validator = Validator::make($request->all(), [
                'id' => 'required',
                'selectIdHotel' => 'required',
                'txtName' => 'required',
                'txtSize' => 'required',
                'miceCategoryId' => 'required',
                'txtCapacity' => 'required',
                'txtSeqNo' => 'required',
                'description' => 'required',
                'oldLayout' => 'required'
              ]);               
          } else {
              $validator = Validator::make($request->all(), [
                'id' => 'required',
                'selectIdHotel' => 'required',
                'txtName' => 'required',
                'txtSize' => 'required',
                'miceCategoryId' => 'required',
                'txtCapacity' => 'required',
                'txtSeqNo' => 'required',
                'description' => 'required',
                'layout' => 'required'
              ]);               
          }
          // Validator Check
          if ($validator->fails()) {
            // return response gagal
            return back()->withInput($request->all())
                        ->with('error',$validator->errors()->first());
          }

          // Jika file layout tidak ada, maka data layout masih yang lama
          if(empty($request['layout'])){
            $data = [
              [
                'name'     => 'id',
                'contents' => $request->post('id'),
              ],
              [
                'name'     => 'name',
                'contents' => $request->post('txtName'),
              ],
              [
                'name'     => 'description',
                'contents' => $request->post('description'),
              ],
              [
                'name'     => 'capacity',
                'contents' => $request->post('txtCapacity'),
              ],
              [
                'name'     => 'size',
                'contents' => $request->post('txtSize'),
              ],
              [
                'name'     => 'seq',
                'contents' => $request->post('txtSeqNo'),
              ],
              [
                'name'     => 'oldLayout',
                'contents' => $request->post('oldLayout'),
              ],
              [
                'name'     => 'updated_by',
                'contents' => $name,
              ]
            ];

          } else {
            // ini data baru untuk Layout nya
            $file = request('layout');
            $data = [
                [
                  'name'     => 'id',
                  'contents' => $request->post('id'),
                ],
                [
                  'name'     => 'name',
                  'contents' => $request->post('txtName'),
                ],
                [
                  'name'     => 'description',
                  'contents' => $request->post('description'),
                ],
                [
                  'name'     => 'capacity',
                  'contents' => $request->post('txtCapacity'),
                ],
                [
                  'name'     => 'size',
                  'contents' => $request->post('txtSize'),
                ],
                [
                  'name'     => 'seq',
                  'contents' => $request->post('txtSeqNo'),
                ],
                [
                  'name'     => 'layout',
                  'contents' => fopen($file, 'r')
                ],
                [
                  'name'     => 'updated_by',
                  'contents' => $name,
                ]
              ];
          }

          if(!empty($request['mice_offers'])){
            $fileMice = request('mice_offers');
            $dataOffers[] = ['name' => 'mice_offers', 'contents' => fopen($fileMice, 'r')] ;
            $data = array_merge($data, $dataOffers);
          }elseif(!empty($request['oldMiceOffers'])) {
            $dataOffers[] = ['name' => 'oldMiceOffers', 'contents' => $request['oldMiceOffers']] ;
            $data = array_merge($data, $dataOffers);
          }
          // dd($data);
          // Looping Mice Category utk prepare data
          foreach($request->miceCategoryId as $key => $mice_cat_id){
            $dataMice[$key] = ['name' => 'mice_category_id['.$key.']', 'contents' => $mice_cat_id];
          }
          // data are all setup
          $data = array_merge($data,$dataMice);
          // dd($data);
          // Guzzle init
          $client = new Client(); //GuzzleHttp\Client
          $response = $client->request('POST',url('api').'/mice/edit_hall',[
              'verify' => false,
              'headers' => $this->headers,
              'multipart' => $data,
              '/delay/5',
              ['connect_timeout' => 3.14]
          ]);
          $bodyMice = $response->getBody();
          $response = json_decode($bodyMice, true);
          $dataMice = $response['data'];

          if($response['status'] == true){
            $id = $response['data']['id'];
            return redirect()->route('hall.get_edit', ['id' =>  $id])->with('success', $response['message']);  
          }else{
              return back()->with('error',$response['message']);
          }

      } catch (Throwable $e) {
          report($e);
          $error = ['modul' => 'edit_hall',
                'actions' => 'data edit Hall',
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
    * Function: to view Add Hall Images
    * body: data outlet
    *	$request	: 
    */
    public function images_hall(Request $request){
        // Validasi Permission, hanya Admin dan User dengan mice-hall-create dan mice-hall-edit
        if((session()->get('role')=='Admin') || Auth::user()->permission('mice-hall-create') || Auth::user()->permission('mice-hall-edit'))
        {
            // dd($request->all());
            // Get Seq No 
            $client = new Client(); //GuzzleHttp\Client
            $responseSequence = $client->request('GET', url('api').'/mice/get_hall_images?hall_id='.$request->txtId,[
                'verify' => false,
                'headers'       => $this->headers,
                '/delay/5',
                ['connect_timeout' => 3.14]
            ]);
            $bodySeq = $responseSequence->getBody();
            $responseSequence = json_decode($bodySeq, true);
            $dataSeq = $responseSequence['data'];
            if(count($dataSeq) == 0){
              $SeqNo = 1;
            } else {
              $SeqNo = $dataSeq[count($dataSeq)-1]['seq'] + 1;
            }
            return view('hall.add-images',['judul' => 'Add Images',
                                            'id' =>$request->post('txtId'),
                                            'name' => $request->post('txtName'),
                                            'seqNo' => $SeqNo,
                                            'data' => null
                                            ]);
        } else {
            return redirect('home');
        }
    }

    /**
     * Function: go to view edit data Image Hall/ view nya pake yg add Hall Images
     * body: data image
     *	$request	: 
    */
    public function get_edit_images(Request $request){
      try{
            // Validasi Permission, hanya Admin dan User dengan mice-hall-create dan mice-hall-edit
            if((session()->get('role')=='Admin') || Auth::user()->permission('mice-hall-create') || Auth::user()->permission('mice-hall-edit'))
            {
                $request=json_decode($request['data']);
                $data = [ 'id' => $request->id,
                          'hall_id' => $request->hall_id,
                          'name' => $request->name,
                          'filename' => $request->filename,
                          'status' => $request->status,
                          'seq' => $request->seq ]; 
                          return view('hall.add-images',['judul' => 'Edit Image',
                                                  'id' =>$data['id'],'data' =>$data]);
            } else {
                return redirect('home');
            }
                                          
          }
          catch (Throwable $e) {
              report($e);
              $error = ['modul' => 'get_edit_images',
                    'actions' => 'get data edit image Hall',
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
     * Function: save/update data image Halls
     * body: data image
     *	$request	: 
    */
    public function save_image(Request $request){
      try{
        $name = session('full_name');
        if(empty($request['oldImages'])){
          $validator = Validator::make($request->all(), [
            'filename' => 'required|image:jpeg,jpg|max:2048',
            'txtName' => 'required|max:50|regex:/^[A-Za-z0-9-_!,. ]+$/',
            'txtSeqNo' => 'required',
            'selectStatus' => 'required',
            'txtHallId' => 'required',
          ]);
        }
        else{
          $validator = Validator::make($request->all(), [
            'txtId' => 'required',
            'txtName' => 'required|max:50|regex:/^[A-Za-z0-9-_!,. ]+$/',
            'txtSeqNo' => 'required',
            'txtHallId' => 'required',
            'oldImages' => 'required',
            'selectStatus' => 'required',
            'images' => 'image:jpeg,jpg|max:2048',
          ]);
        }
        if ($validator->fails()) {
            // return response gagal
            return back()->withInput($request->all())
                        ->with('error',$validator->errors()->first());
        }

        $client = new Client(); //GuzzleHttp\Client
        if(empty($request['filename'])){
            $response = $client->request('POST', url('api').'/mice/add_hall_images',[
              'verify' => false,
              'multipart' => [
                  [
                    'name'     => 'id',
                    'contents' => $request->post('txtId')
                  ],
                  [
                      'name'     => 'name',
                      'contents' => $request->post('txtName')
                  ],
                  [
                    'name'     => 'hall_id',
                    'contents' => $request->post('txtHallId')
                  ],
                  [
                    'name'     => 'seq',
                    'contents' => $request->post('txtSeqNo')
                  ],
                  [
                    'name'     => 'status',
                    'contents' => $request->post('selectStatus')
                  ],
                  [
                      'name'     => 'oldImages',
                      'contents' => $request['oldImages']
                  ],
                  [
                    'name'     => 'updated_by',
                    'contents' => $name
                  ],
              ],      
              'headers' => $this->headers,
              '/delay/5',
              ['connect_timeout' => 3.14]
            ]);
        }
        else{
              $file = request('filename');
              //guzzle client
              $response = $client->request('POST', url('api').'/mice/add_hall_images',[
                  'verify' => false,
                  'multipart' => [
                      [
                        'name'     => 'id',
                        'contents' => $request->post('txtId')
                      ],
                      [
                          'name'     => 'name',
                          'contents' => $request->post('txtName')
                      ],
                      [
                        'name'     => 'hall_id',
                        'contents' => $request->post('txtHallId')
                      ],
                      [
                        'name'     => 'status',
                        'contents' => $request->post('selectStatus')
                      ],
                      [
                        'name'     => 'seq',
                        'contents' => $request->post('txtSeqNo')
                      ],
                      [
                          'name'     => 'filename',
                          'contents' => fopen($file, 'r')
                      ],
                      [
                        'name'     => 'created_by',
                        'contents' => $name
                      ]
                  ],
                      
                  'headers' => $this->headers,
                  '/delay/5',
                  ['connect_timeout' => 3.14]
                ]);
        } // end else if empty request filename
          $body = $response->getBody();
          $response = json_decode($body, true);
          // dd($response);
          if($response['status'] == true && $response['data'] != null){    
            return redirect()->route('hall.get_edit', ['id' => $request->post('txtHallId')])
                    ->with('success', $response['message']);
          } else{
            return back()->withInput($request->all())
                        ->with('error',$response['message']);
          }
      } catch (Throwable $e) {
              report($e);
              $error = ['modul' => 'save_image',
                  'actions' => 'save data image Hall',
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
     * Function: delete data Hall Images
     * body: 
     *	$request	: 
    */
    public function delete_hall_images($id){
      try{
            // Validasi Permission, hanya Admin dan User dengan mice-hall-create dan mice-hall-edit
            if((session()->get('role')=='Admin') || Auth::user()->permission('mice-hall-create') || Auth::user()->permission('mice-hall-edit'))
            {
                $data = ['id' => $id];
                $client = new Client(); //GuzzleHttp\Client
                    $response = $client->request('POST', url('api').'/mice/delete_image_hall',[
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
       * Function: Get Data Hall based From Hotel ID
       * body:  
       *	$request	: 
      */
      public function get_hotel_hall($request){
        try {
            $name = session('full_name');
            // Validasi jika user merupakan admin
            $client = new Client(); //GuzzleHttp\Client
            if(strtoupper(session('role')) == 'ADMIN'){
                $response = $client->request('GET', url('api').'/mice/get_hall?hotel_id='.$request,[
                    'verify' => false,
                    'headers'       => $this->headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
                ]);   
            } else {
                $response = $client->request('GET', url('api').'/mice/get_hall?hotel_id='.$request.'&user_id='. session('id'),[
                    'verify' => false,
                    'headers'       => $this->headers,
                    '/delay/5',
                    ['connect_timeout' => 3.14]
                ]);
            }
            $body = $response->getBody();
            $response = json_decode($body, true);
            $data = $response;
            return $data;
        }  
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_hotel_hall',
                        'actions' => 'Get Data Hall from hotel_id',
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
