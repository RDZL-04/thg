<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Validator;
use App\Models\OutletMenus;
use File;
use App\Http\Controllers\LOG\ErorrLogController;
use Auth;

/*
|--------------------------------------------------------------------------
| Menu WEB Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process facility data
| 
| @author: ilham.maulana@arkamaya.co.id 
| @update: December 21
*/

class MenuController extends Controller
{
  public function __construct() {
    // Set header for API Request
    $this->headers = [
        'x-api-key' => env('API_KEY'),
      ];
      $this->LogController = new ErorrLogController;
  }

  /**
	 * Function: go to view add menu Outlet
	 * body:  outlet_id
	 *	$request	: 
	*/
  public function get_menu_add(Request $request){
    try {
        // Validasi Permission, hanya admin, admin outlet dan admin hotel yg bisa create
        if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-menu-create'))
        {
            $name = session('full_name');
            if(!empty($request->data_old)){
              $data_old = $request->data_old;
            } else {
              $data_old = null;
            }
            // get data menu category
            $request['fboutlet_id'] = $request['txtOutletId'];
            $response = $this->get_menu_category_all($request);
            // dd($response);
            $data_menu = $response;
            if(empty($request->post('txtRestName'))) {
              $rest_name = $request->rest_name;
            } else {
              $rest_name = $request->post('txtRestName');
            }
            // Check Last Seq_NO menu di Outlet Terkait jika ada
            $client = new Client(); //GuzzleHttp\Client
            $resSeqNo = $client->request('GET', url('api').'/outlet/get_outlet_menu?outlet_id='.$request->txtOutletId,[
                'verify' => false,
                'headers'       => $this->headers,
                '/delay/5',
                  ['connect_timeout' => 3.14]
            ]);

            $body = $resSeqNo->getBody();
            $resSeqNo = json_decode($body, true);
            $resSeqNo = $resSeqNo['data'];
            if($resSeqNo == null) {
              $newSeqNo = 1;
            } else {
              if(count($resSeqNo) > 0) {
                $newSeqNo = $resSeqNo[count($resSeqNo)-1]['seq_no'] + 1;
              } else {
                $newSeqNo = 1;
              }
            }
            // dd($request->post('txtRestName'));
            return view('outlet.add-menus',[
              'judul' =>  __('outlet.add_menu'),
              'data' => null,
              'data_category' => $data_menu,
              'data_sidedish' => null,
              'data_issidedish' => null,
              'data_old' => $data_old,
              'rest_name' => $rest_name,
              'txtOutletId' => $request->post('txtOutletId'),
              'new_seq_no' => $newSeqNo
            ]
          );
        } else {
          return redirect('home');
        }
    }  
    catch (Throwable $e) {
      report($e);
      $error = ['modul' => 'get_menu_add',
                'actions' => 'get data menu',
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
	 * Function: go to view edit menu Outlet
	 * body:  outlet_id
	 *	$request	: 
	*/
  public function get_menu_edit(Request $request){
    try {
        // Validasi Permission, hanya Admin, Admin Outlet dan Admin Hotel yang bisa edit
        if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-menu-edit'))
        {
            // dd($request);
            $name = session('full_name');
            // get Detail Outlet utk ambil Nama Outlet
            $client = new Client(); //GuzzleHttp\Client
            $response = $client->request('GET', url('api').'/outlet/get_outlet_detail?outlet_id='. $request->outlet_id,[
              'verify' => false,
              'headers'       => $this->headers,
              '/delay/5',
              ['connect_timeout' => 3.14]
            ]);
            $body = $response->getBody();
            $response = json_decode($body, true);
            // dd($response);
            $data_outlet = $response['data'];
            $rest_name = $data_outlet['name'];
            
            $client = new Client(); //GuzzleHttp\Client
            $response = $client->request('GET', url('api').'/menu/get_menu_detail?menu_id='. $request->id,[
              'verify' => false,
              'headers'       => $this->headers,
              '/delay/5',
              ['connect_timeout' => 3.14]
            ]);
            $body = $response->getBody();
            $response = json_decode($body, true);
            $data_menu = $response['data'];

            $client = new Client(); //GuzzleHttp\Client
            if(!empty($request->outlet_id)){
              $reqCat['fboutlet_id'] = $request->outlet_id;
              $response = $client->request('POST', url('api').'/menu/categories/get_categories_all',[
                  'verify' => false,
                  'headers'       => $this->headers,
                  'form_params' => $reqCat,
                  '/delay/5',
                  ['connect_timeout' => 3.14]
              ]);
            } else {
              $response = $client->request('POST', url('api').'/menu/categories/get_categories_all',[
                'verify' => false,
                'headers'       => $this->headers,
                '/delay/5',
                ['connect_timeout' => 3.14]
            ]);
            }
            $body = $response->getBody();
            $response = json_decode($body, true);
            $data_categories = $response['data'];

            $multipart = [
              [
                'name'     => 'menu_id',
                'contents' => $request->id,
              ],
              [
                'name'     => 'fboutlet_id',
                'contents' => $request->outlet_id,
              ],
            ];  

            $client = new Client(); //GuzzleHttp\Client
            $response = $client->request('POST', url('api').'/menu/get_menu_sidedish?fboutlet_id',[
                'verify' => false,
                'headers'       => $this->headers,
                'multipart' => $multipart,
                '/delay/5',
                ['connect_timeout' => 3.14]
            ]);
            $body = $response->getBody();
            $response = json_decode($body, true);
            $data_sidedish = $response['data'];

            $multipart = [
              [
                'name'     => 'menu_id',
                'contents' => $request->id,
              ],
              [
                'name'     => 'fboutlet_id',
                'contents' => $request->outlet_id,
              ],
            ];  

            $client = new Client(); //GuzzleHttp\Client
            $response = $client->request('POST', url('api').'/menu/get_sidedish',[
              'verify' => false,
              'multipart' => $multipart,
              'headers'       => $this->headers,
              '/delay/5',
              ['connect_timeout' => 3.14]
            ]);
      
            $body = $response->getBody();
            $response = json_decode($body, true);
            $data_issidedish = $response['data'];
              // dd($data_issidedish);
      
            return view('outlet.add-menus',[
              'judul' =>  __('outlet.edit_menu'),
              'data' => $data_menu[0],
              'data_category' => $data_categories,
              'rest_name' => $rest_name,
              'txtOutletId' => $request->outlet_id,
              'data_sidedish' => $data_sidedish,
              'data_issidedish' => $data_issidedish
            ]
          );
        } else {
          return redirect('home');
        }
    }  
    catch (Throwable $e) {
      report($e);
      $error = ['modul' => 'get_menu_edit',
                'actions' => 'get edit data menu',
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
	 * Function: edit menu
	 * body:  outlet_id, menu_id
	 *	$request	: 
	*/
  public function get_edit(Request $request){
    try {
      // dd($request->all());
      if($request->chkIsPromo == 'on'){
        $txtIsPromo = 1;
      } else {
        $txtIsPromo = 0;
      }
      // txtJenis tidak ada maka Menu Edit, bukan Add atau Tambah Menu
      if(empty($request['txtJenis'])){
        // dd($request->all());
        // Get All Seq_No
        $client = new Client(); //GuzzleHttp\Client
        $resSeqNo = $client->request('GET', url('api').'/outlet/get_outlet_menu?outlet_id='.$request->txtOutletId,[
            'verify' => false,
            'headers'       => $this->headers,
            '/delay/5',
             ['connect_timeout' => 3.14]
        ]);

        $body = $resSeqNo->getBody();
        $resSeqNo = json_decode($body, true);
        $resSeqNo = $resSeqNo['data'];
        foreach ($resSeqNo as $value) {
          $resSeqAll[] = $value['seq_no'];
        }
        // $seqNo[] = $resSeqNo['seq_no'];
        // dd($resSeqAll);

        // Get Seq_No menu_id sebelum Edit
        $client = new Client(); //GuzzleHttp\Client
        $resSeqNo = $client->request('GET', url('api').'/menu/get_menu_detail?menu_id='.$request->txtId,[
            'verify' => false,
            'headers'       => $this->headers,
            '/delay/5',
             ['connect_timeout' => 3.14]
        ]);

        $body = $resSeqNo->getBody();
        $resSeqNo = json_decode($body, true);
        $resSeqNo = $resSeqNo['data'];
        $seqNo[0] = $resSeqNo[0]['seq_no'];
        // dd($seqNoBefore);
        $arrayDiff=array_diff($resSeqAll,$seqNo);
        foreach ($arrayDiff as $value) {
          if($value == $request->txtSeqNo){
            return redirect()->route('outlet_menu.get_edit', ['outlet_id' => $request->txtOutletId, 'id' => $request->txtId, 'data_old' => $request->all() ])
            ->with('error', 'Seq No has been taken');
          }
        }
        // dd($request->all());
        if(!empty($request['images'])){
           // Check Resolution
          // Check Image Resolution ke tabel M_System
            $client = new Client(); //GuzzleHttp\Client
            $resMSystem = $client->request('GET', url('api').'/msytem/get_system_type_cd?system_type=img_resolution&system_cd=menu',[
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
            
            $messages = array('txtName.max' => __('message.name_max_length_150'),
                            'txtName.regex' => __('message.name_regex'),
                            'txtName.required' => __('message.name_required'),
                            'images.required' => __('message.img_required'),
                            'images.max' => __('message.img_max_256'),
                            'images.image' => __('message.img_type'),
                            'txtSeqNo.required' => __('message.seq_no_required'),
                            'txtOutletId.required' => __('message.id_hotel_required'),
                            'txtId.required' => __('message.id_required'),
                            'txtPrice.required' => __('message.price_required'),
                            'txtPrice.numeric' => __('message.price_numeric'),
                            'txtPrice.digits_between' => __('message.price_between'),
                            'selectStatus.required' => __('message.status_required'),
                            'selectCategory.required' => __('message.category_required'),
                            'txtDescription.required' => __('message.desc_required'),
                            'txtDescription.max' => __('message.desc_max'),
                            'txtDescription.regex' => __('message.desc_regex'),
                          );

            if(in_array($arrayDim[0][0],$resDimension)){
              $validator = Validator::make($request->all(), [
                'images' => 'required|mimes:jpeg,jpg|max:256',
                'txtName' => 'required|max:150|regex:/^[A-Za-z0-9-& ]+$/',
                'txtSeqNo' => 'required|unique:fboutlet_images,seq_no,NULL,id,fboutlet_id,'.$request->txtOutletId,
                'txtOutletId' => 'required',
              ]);
            } else {
              return back()->withInput($request->all())
                        ->with('error','Images has invalid dimension');
            }
            
            $validator = Validator::make($request->all(), [
              'images' => 'required|image:jpeg,jpg|max:256',
              'txtOutletId' => 'required',
              'txtId' => 'required',
              'txtName' => 'required|max:150|regex:/^[A-Za-z0-9-& ]+$/',
              'txtDescription' => 'required|regex:/^[\r\nA-Za-z0-9-_!:;,.\'&# ]+$/',
              'txtPrice' => 'required|numeric|digits_between:1,8',
              'selectStatus' => 'required',
              'selectCategory' => 'required',
              'txtSeqNo' => 'required'
            ],$messages);
        } else {
          // dd($request->all());
          $messages = array('txtName.max' => __('message.name_max_length_150'),
                    'txtName.regex' => __('message.name_regex'),
                    'txtName.required' => __('message.name_required'),
                    'oldImages.required' => __('message.img_required'),
                    'txtSeqNo.required' => __('message.seq_no_required'),
                    'txtOutletId.required' => __('message.id_hotel_required'),
                    'txtId.required' => __('message.id_required'),
                    'txtPrice.required' => __('message.price_required'),
                    'txtPrice.numeric' => __('message.price_numeric'),
                    'txtPrice.digits_between' => __('message.price_between'),
                    'selectStatus.required' => __('message.status_required'),
                    'selectCategory.required' => __('message.category_required'),
                    'txtDescription.required' => __('message.desc_required'),
                    'txtDescription.max' => __('message.desc_max'),
                    'txtDescription.regex' => __('message.desc_regex'),
                  );

            $validator = Validator::make($request->all(), [
              'txtOutletId' => 'required',
              'txtId' => 'required',
              'txtName' => 'required | max:150 | regex:/^[A-Za-z0-9-& ]+$/',
              'txtDescription' => 'required|regex:/^[\r\nA-Za-z0-9-_!:;,.\'&# ]+$/',
              'txtPrice' => 'required|numeric|digits_between:1,8',
              'selectStatus' => 'required',
              'selectCategory' => 'required',
              'txtSeqNo' => 'required',
              // 'oldImages' => 'required'
          ],$messages);
        }
        if(!empty($request['oldImages'])){
          $multipart = [
            [
              'name'     => 'id',
              'contents' => $request->post('txtId'),
            ],
            [
              'name'     => 'fboutlet_id',
              'contents' => $request->post('txtOutletId'),
            ],
            [
                'name'     => 'name',
                'contents' => $request->post('txtName'),
            ],
            [
              'name'     => 'description',
              'contents' => $request->post('txtDescription'),
            ],
            [
              'name'     => 'price',
              'contents' => $request->post('txtPrice'),
            ],
            [
              'name'     => 'menu_sts',
              'contents' => $request->post('selectStatus'),
            ],
            [
              'name'     => 'menu_cat_id',
              'contents' => $request->post('selectCategory'),
            ],
            [
              'name'     => 'seq_no',
              'contents' => $request->post('txtSeqNo'),
            ],
            [
              'name'     => 'is_promo',
              'contents' => $txtIsPromo,
            ],
            [
                'name'     => 'oldImages',
                'contents' => $request['oldImages'],
            ],
            [
              'name'     => 'created_by',
              'contents' => $request['created_by'],
            ],
            [
              'name'     => 'changed_by',
              'contents' => session('full_name'),
            ],
          ];
        }else{
          $multipart = [
            [
              'name'     => 'id',
              'contents' => $request->post('txtId'),
            ],
            [
              'name'     => 'fboutlet_id',
              'contents' => $request->post('txtOutletId'),
            ],
            [
                'name'     => 'name',
                'contents' => $request->post('txtName'),
            ],
            [
              'name'     => 'description',
              'contents' => $request->post('txtDescription'),
            ],
            [
              'name'     => 'price',
              'contents' => $request->post('txtPrice'),
            ],
            [
              'name'     => 'menu_sts',
              'contents' => $request->post('selectStatus'),
            ],
            [
              'name'     => 'menu_cat_id',
              'contents' => $request->post('selectCategory'),
            ],
            [
              'name'     => 'seq_no',
              'contents' => $request->post('txtSeqNo'),
            ],
            [
              'name'     => 'is_promo',
              'contents' => $txtIsPromo,
            ],
            [
              'name'     => 'created_by',
              'contents' => $request['created_by'],
            ],
            [
              'name'     => 'changed_by',
              'contents' => session('full_name'),
            ],
          ];
        }     
        
     } else {
      //  dd($request->all());
      $client = new Client(); //GuzzleHttp\Client
      $resSeqNo = $client->request('GET', url('api').'/outlet/get_outlet_menu?outlet_id='.$request->txtOutletId,[
          'verify' => false,
          'headers'       => $this->headers,
          '/delay/5',
           ['connect_timeout' => 3.14]
      ]);

      $body = $resSeqNo->getBody();
      $resSeqNo = json_decode($body, true);
      $resSeqNo = $resSeqNo['data'];
      if(($resSeqNo) != null){
        foreach ($resSeqNo as $value) {
          $resSeqAll[] = $value['seq_no'];
        }

        foreach ($resSeqAll as $value) {
          //$resSeqAll[] = $value['seq_no'];
          if($value == $request->txtSeqNo){
            if(!empty($request->txtId)){
              // dd($request->all());
              return redirect()->route('outlet_menu.get_edit', ['outlet_id' => $request->txtOutletId, 'id' => $request->txtId,'data_old' => $request->all() ])
              ->with('error', 'Seq No has been taken');
            } else {
              // dd($request->all());
              return redirect()->route('outlet_menu.add', ['txtOutletId' => $request->txtOutletId, 'rest_name' => $request->txtRestName, 'data_old' => $request->all() ])
              ->with('error', 'Seq No has been taken');
            }
          }
        }
      }
      // Check Resolution
          // Check Image Resolution ke tabel M_System
        
            $client = new Client(); //GuzzleHttp\Client
            $resMSystem = $client->request('GET', url('api').'/msytem/get_system_type_cd?system_type=img_resolution&system_cd=menu',[
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
            if(!empty($request['images'])){
                $data = getimagesize($request->images);
                $arrayDim[] = [$data[0].'x'.$data[1]];

                if(in_array($arrayDim[0][0],$resDimension)){
                  $validator = Validator::make($request->all(), [
                    'images' => 'required|image:jpeg,jpg|max:256',
                    'txtOutletId' => 'required',
                    'txtName' => 'required|max:150|regex:/^[A-Za-z0-9-& ]+$/',
                    'txtDescription' => 'required|regex:/^[\r\nA-Za-z0-9-_!:;,.\'&# ]+$/',
                    'txtPrice' => 'required|numeric|digits_between:1,8',
                    'selectStatus' => 'required',
                    'selectCategory' => 'required',
                    'txtSeqNo' => 'required'
                    // 'txtSeqNo' => 'required|unique:fboutlet_menus,seq_no,NULL,id,fboutlet_id,'.$request->txtOutletId
                  ]);
                } else {
                  return back()->withInput($request->all())
                            ->with('error','Images has invalid dimension');
                }

              $multipart = [
                [
                  'name'     => 'id',
                  'contents' => $request->post('txtId'),
                ],
                [
                  'name'     => 'fboutlet_id',
                  'contents' => $request->post('txtOutletId'),
                ],
                [
                    'name'     => 'name',
                    'contents' => $request->post('txtName'),
                ],
                [
                  'name'     => 'description',
                  'contents' => $request->post('txtDescription'),
                ],
                [
                  'name'     => 'price',
                  'contents' => $request->post('txtPrice'),
                ],
                [
                  'name'     => 'menu_sts',
                  'contents' => $request->post('selectStatus'),
                ],
                [
                  'name'     => 'menu_cat_id',
                  'contents' => $request->post('selectCategory'),
                ],
                [
                  'name'     => 'seq_no',
                  'contents' => $request->post('txtSeqNo'),
                ],
                [
                  'name'     => 'is_promo',
                  'contents' => $txtIsPromo,
                ],
                [
                    'name'     => 'oldImages',
                    'contents' => $request['oldImages'],
                ],
                [
                  'name'     => 'created_by',
                  'contents' => session('full_name'),
                ],
              ];
            }else{
              // dd('ada');
                  $validator = Validator::make($request->all(), [
                    'txtOutletId' => 'required',
                    'txtName' => 'required|max:150|regex:/^[A-Za-z0-9-& ]+$/',
                    'txtDescription' => 'required|regex:/^[\r\nA-Za-z0-9-_!:;,.\'&# ]+$/',
                    'txtPrice' => 'required|numeric|digits_between:1,8',
                    'selectStatus' => 'required',
                    'selectCategory' => 'required',
                    'txtSeqNo' => 'required'
                    // 'txtSeqNo' => 'required|unique:fboutlet_menus,seq_no,NULL,id,fboutlet_id,'.$request->txtOutletId
                  ]);

              $multipart = [
                [
                  'name'     => 'id',
                  'contents' => $request->post('txtId'),
                ],
                [
                  'name'     => 'fboutlet_id',
                  'contents' => $request->post('txtOutletId'),
                ],
                [
                    'name'     => 'name',
                    'contents' => $request->post('txtName'),
                ],
                [
                  'name'     => 'description',
                  'contents' => $request->post('txtDescription'),
                ],
                [
                  'name'     => 'price',
                  'contents' => $request->post('txtPrice'),
                ],
                [
                  'name'     => 'menu_sts',
                  'contents' => $request->post('selectStatus'),
                ],
                [
                  'name'     => 'menu_cat_id',
                  'contents' => $request->post('selectCategory'),
                ],
                [
                  'name'     => 'seq_no',
                  'contents' => $request->post('txtSeqNo'),
                ],
                [
                  'name'     => 'is_promo',
                  'contents' => $txtIsPromo,
                ],
                [
                  'name'     => 'created_by',
                  'contents' => session('full_name'),
                ],
              ];
            }
              
      }

      if ($validator->fails()) {
        // return response gagal
        return back()->withInput($request->all())
                    ->with('error',$validator->errors()->first());
      }

      $client = new Client(); //GuzzleHttp\Client
      if(empty($request['images'])){
        $response = $client->request('POST', url('api').'/menu/save_menu',[
          'verify' => false,
          'multipart' => $multipart,
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

            $multipart[] = [
              'name'     => 'images',
              'contents' => fopen($file, 'r')
            ];

            //dd($multipart);

            $response = $client->request('POST', url('api').'/menu/save_menu',
                        [
                         'verify' => false,
                         'multipart' => $multipart,
                         'headers'       => $this->headers,
                         '/delay/5',
                         ['connect_timeout' => 3.14]
                        ]);
            }
 
              $body = $response->getBody();
              $response = json_decode($body, true);
              
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
      $error = ['modul' => 'get_edit',
                'actions' => 'get edit menu',
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
	 * Function: delete data menu outlet
	 * body: id
	 *	$request	: 
	*/
  public function delete_menu($id){
    try{
          // Validasi Permission hanya Admin, Admin Outlet dan Admin Hotel yang bisa ( outlet-menu-delete permission)
          if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-menu-delete'))
          {
              // dd($id);
              $data = ['id' => $id];
              // Check apakah menu id merupakan side-dish menu lain
              $client = new Client(); //GuzzleHttp\Client
              $resSide = $client->request('GET', url('api').'/menu/get_menu_is_sidedish?id='.$id,[
                  'verify' => false,
                  'headers'       => $this->headers,
                  '/delay/5',
                  ['connect_timeout' => 3.14]
              ]);
              $body = $resSide->getBody();
              $resSide = json_decode($body, true);
              // dd($resSide['data']);
              if($resSide['data'] != null){
                if(count($resSide['data']) > 0) {
                  return back()->with('error',__('message.menu_is_sidedish'));
                }
              }
              $client = new Client(); //GuzzleHttp\Client
              $response = $client->request('POST', url('api').'/menu/delete_menu',[
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
          } else {
              return redirect('home');
          }
    }
    catch (Throwable $e) {
        report($e);
        $error = ['modul' => 'delete_menu',
                'actions' => 'delete menu',
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
	 * Function: get_menu_from_category untuk select Menu Category di popUp Add Sidedish Button
	 * body: 
	 *	$request	: 
	*/
  public function get_menu_from_category(Request $request)
  {
      try{
          $multipart = [
            [
              'name'     => 'menu_id',
              'contents' => $request->menu_id,
            ],
            [
              'name'     => 'fboutlet_id',
              'contents' => $request->outlet_id,
            ],
            [
              'name'     => 'menu_cat_id',
              'contents' => $request->id,
            ],
          ];  
          $client = new Client(); //GuzzleHttp\Client
          // Get Menu from Category ID
          $response = $client->request('POST',url('api').'/menu/get_sidedish_menu_cat',[
              'verify' => false,
              'multipart' => $multipart,
              'headers' => $this->headers,
              '/delay/5',
              ['connect_timeout' => 3.14]
          ]);
          $body = $response->getBody();
          $resCat = json_decode($body, true);
          // return $response['data'];
          if($resCat['status'] == true && $resCat['data'] != null){
              $response = [
                  'status' => true,
                  'message' => __('message.data_found' ),
                  'code' => 200,
                  'data' => $resCat['data'], 
              ];
              return response()->json($response, 200);
          } else {
            $response = [
                'status' => false,
                'message' => $resCat['message'],
                'code' => 400,
                'data' => null, 
            ];
            return response()->json($response, 200);
          }
      }
      catch (Throwable $e) {
          report($e);
          $error = ['modul' => 'get_menu_from_category',
                'actions' => 'Get Data Menu from Select Category',
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
	 * Function: add side dish
	 * body: 
	 *	$request	: 
	*/
  public function add_sidedish(Request $request)
  {
      try{
          $client = new Client(); //GuzzleHttp\Client
              $response = $client->request('POST',url('api').'/menu/add_sidedish',[
              'verify' => false,
              'form_params'   => $request->all(),
              'headers'       => $this->headers,
              '/delay/5',
              ['connect_timeout' => 3.14]
          ]);
          $body = $response->getBody();
          $response = json_decode($body, true);
            // dd($response['data']);
          if($response['status'] == true && $response['data'] != null){
              $response = [
                  'status' => true,
                  'message' => __('message.success-add-menu'),
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
          $error = ['modul' => 'add_sidedish',
                'actions' => 'add data sidedish',
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
	 * Function: delete side dish
	 * body: 
	 *	$request	: 
	*/
  public function delete_sidedish(Request $request)
  {
      try{
          // Validasi Permission
          if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-menu-edit'))
          {
              //dd($request->menu_id);
              $param = [ 
                'sidedish_id' => $request->sidedish_id  
              ];
              
              $client = new Client(); //GuzzleHttp\Client
                  $response = $client->request('POST',url('api').'/menu/delete_sidedish',[
                  'verify' => false,
                  'form_params'   => $param,
                  'headers'       => $this->headers,
                  '/delay/5',
                  ['connect_timeout' => 3.14]
              ]);
              $body = $response->getBody();
              $response = json_decode($body, true);
              //   dd($response['data']);
              if($response['status'] == true && $response['data'] != null){           
                return redirect()->route('outlet_menu.get_edit', 
                                        ['outlet_id' =>$request->outlet_id,
                                        'id' => $request->menu_id],
                                        )
                                //  ->with('success', $response['message']);
                                ->with('success',__('message.success-delete-menu'));
              }
              else{
                return back()->withInput($request->all())
                            ->with('error',$response['message']);
              }
          } else {
              return redirect('home');
          }
      }
      catch (Throwable $e) {
          report($e);
          $error = ['modul' => 'delete_sidedish',
                'actions' => 'delete data sidedish',
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
	 * Function: go to view add User Outlet
	 * body:  outlet_id
	 *	$request	: 
	*/
  public function add_user(Request $request){
    try {
          // Validasi Permission, hanya Admin, Admin Outlet dan Admin Hotel yang bisa akses
          if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-edit')) {
            $name = session('full_name');
            if(!empty($request->data_old)){
              $data_old = $request->data_old;
            } else {
              $data_old = null;
            }

            $client = new Client(); //GuzzleHttp\Client
                $response = $client->request('GET', url('api').'/outlet/get_outlet_user_avail?fboutlet_id='.$request->post('txtOutletId'),[
                'verify' => false,
                'headers'       => $this->headers,
                '/delay/5',
                ['connect_timeout' => 3.14]
            ]);
            $body = $response->getBody();
            $response = json_decode($body, true);
            // dd($response);
            $data_user = $response['data'];

            if(empty($request->post('txtRestName'))) {
              $rest_name = $request->rest_name;
            } else {
              $rest_name = $request->post('txtRestName');
            }
            // dd($request->post('txtRestName'));
            return view('outlet.add-users',[
                'judul' => 'Add Outlet User',
                'data' => null,
                'data_user' => $data_user,
                'data_old' => $data_old,
                'rest_name' => $rest_name,
                'txtOutletId' => $request->post('txtOutletId'),
                ]
            );
          } else  {
            return redirect('home');
          }
    }  
    catch (Throwable $e) {
      report($e);
      $error = ['modul' => 'add_user',
                'actions' => 'add data user',
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
     * Function: save data outlet
     * body: data outlet
     *	$request	: 
    */
    public function save_user(Request $request)
    {		
        try{
            $validator = Validator::make($request->all(), [
                'selectIdUser' => 'required',
                'txtOutletId' => 'required'
                
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
            $data = [
                'fboutlet_id' => $request->post('txtOutletId'),
                'user_id' => $request->post('selectIdUser'),
                'created_by' => $request->post('created_by')
      
             ];
            //  $headers = [
            //     'x-api-key' => 'c20ad4d76fe97759aa27a0c99bff6710',
            //   ];

              $client = new Client(); //GuzzleHttp\Client
                  $response = $client->request('POST', url('api').'/outlet/save_outlet_user',[
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
                return redirect()->route('outlet.get_edit', ['id' => $request->post('txtOutletId')])
                ->with('success', $response['message']);               
                // echo'send message succes';
              }else{
                return back()->withInput($request->all())
                            ->with('error',$response['message']);
              }
		    }
        catch (Throwable $e) {
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
                'data' => [], 
            ];
            return response()->json($response, 500);
        }
    }

   /**
	 * Function: delete data menu outlet
	 * body: id
	 *	$request	: 
	*/
  public function delete_outlet_user($id)
  {
      
    try{
      // Validasi Permission, hanya Admin, Admin Outlet dan Admin Hotel 
      if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-edit')) {
        $data = ['id' => $id];
        $client = new Client(); //GuzzleHttp\Client
            $response = $client->request('POST', url('api').'/outlet/delete_outlet_user',[
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
      } else {
        return redirect('home');
      }
    }
    catch (Throwable $e) {
        report($e);
        $error = ['modul' => 'delete_outlet_user',
                'actions' => 'delete data user',
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
	 * Function: go to view Menu Categories
	 * body:  
	 *	$request	: 
	*/
  public function view_category(Request $request){
    try {
          // Validasi Permission, hanya Admin dan User dgn outlet-menu-category-list
          if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-menu-category-list'))
          {
            $name = session('full_name');
            $user_id = session('id');
            // dd($request->all());
            $client = new Client(); //GuzzleHttp\Client
            if(strtolower(session()->get('role')) == 'admin') {
                $data = $this->get_menu_category_all($request);
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
            } else {
                $request['user_id'] = $user_id;
                $data = $this->get_menu_category_all_user($request);
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
            // dd($response_outlet);

            $body = $response_hotel->getBody();
            $response_hotel = json_decode($body, true);

            // Get Seq_No from fboutlet_mn_categories
            $response_seqNo = $client->request('POST', url('api').'/menu/categories/get_categories_all_with_seq_no',[
                'verify' => false,
                'headers'       => $this->headers,
                '/delay/5',
                ['connect_timeout' => 3.14]
            ]);
            $body = $response_seqNo->getBody();
            $response_seqNo = json_decode($body, true);
            if($response_seqNo['status'] && $response_seqNo['data'] != null) {
              $seqNo = intval($response_seqNo['data'][count($response_seqNo['data']) - 1]['seq_no']) + 1;
            } else {
              $seqNo = 1;
            }
            if($response_outlet['status']){
                $data_outlet=$response_outlet['data'];
                $data_hotel=$response_hotel['data'];
                // dd($data);
                // dd($request->all());
                // $data = $this->get_menu_category_all($request);
                return view('outlet.menu-category',[
                    'judul' => __('outlet.master_category'),
                    'data' => $data,
                    'dataOutlet' => $data_outlet,
                    'dataHotel' => $data_hotel,
                    'seqNo' => $seqNo
                    ]
                );
            } else {
                return view('outlet.menu-category',[
                    'judul' => __('outlet.master_category'),
                    'data' => null,
                    'dataOutlet' => null
                    ]
                );
            }
          } else {
            return redirect('home');
          }
    }  
    catch (Throwable $e) {
      report($e);
      $error = ['modul' => 'view_category',
                'actions' => 'view category',
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
	 * Function: get data menu category all untuk di display di View
   *  dipanggil dari JS Data tabel View menu-category.blade.php
	 * body:  
	 *	$request	: 
	*/
  public function get_menu_category_all(Request $request) {
    try {
        $name = session('full_name');
        $client = new Client(); //GuzzleHttp\Client
        if(!empty($request['fboutlet_id']) || $request->fboutlet_id != null){
          $response = $client->request('POST', url('api').'/menu/categories/get_categories_all',[
              'verify' => false,
              'headers'       => $this->headers,
              'form_params' => $request->all(),
              '/delay/5',
              ['connect_timeout' => 3.14]
          ]);
        } else {
          $response = $client->request('POST', url('api').'/menu/categories/get_categories_all',[
              'verify' => false,
              'headers'       => $this->headers,
              '/delay/5',
              ['connect_timeout' => 3.14]
          ]);
        }
        $body = $response->getBody();
        $response = json_decode($body, true);
        if(empty($response['data']))
        {
          return $data = null;
        } else {
          return $data = $response['data']; 
        }
        
    }  
    catch (Throwable $e) {
      report($e);
      $error = ['modul' => 'get_menu_category_all',
                'actions' => 'get data menu category',
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
	 * Function: get data menu category all untuk di display di View
   *  dipanggil dari JS Data tabel View menu-category.blade.php
	 * body:  
	 *	$request	: 
	*/
  public function get_menu_category_all_user(Request $request){
    try {
        $name = session('full_name');
        $client = new Client(); //GuzzleHttp\Client
        $response = $client->request('POST', url('api').'/menu/categories/get_categories_all_user',[
            'verify' => false,
            'headers'       => $this->headers,
            'form_params' => $request->all(),
            '/delay/5',
            ['connect_timeout' => 3.14]
        ]);
        
        $body = $response->getBody();
        $response = json_decode($body, true);
        if(empty($response['data']))
        {
          return $data = null;
        } else {
          return $data = $response['data']; 
        }
        
    }  
    catch (Throwable $e) {
      report($e);
      $error = ['modul' => 'get_menu_category_all',
                'actions' => 'get data menu category',
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
	 * Function: get data menu category all dengan Hotel ID untuk di display di View
   *  dipanggil dari JS Data tabel View menu-category.blade.php
	 * body:  
	 *	$request	: 
	*/
  public function get_menu_category_all_hotel(Request $request) {
    try {
        // dd($reqCat->all());
        $name = session('full_name');
        $client = new Client(); //GuzzleHttp\Client
        if(!empty($request['hotel_id']) || $request->hotel_id != null){
          // $reqCat = new Request();
          $reqCat['hotel_id'] = $request->hotel_id;
          $response = $client->request('POST', url('api').'/menu/categories/get_menu_category_all_hotel',[
              'verify' => false,
              'headers'       => $this->headers,
              'form_params' => $reqCat,
              '/delay/5',
              ['connect_timeout' => 3.14]
          ]);
          $response_outlet = $client->request('GET', url('api').'/outlet/get_hotel_outlet?hotel_id='.$request->hotel_id,[
            'verify' => false,
            'headers' => $this->headers,
            '/delay/5',
             ['connect_timeout' => 3.14]
          ]);
        } else {
          $response = $client->request('POST', url('api').'/menu/categories/get_menu_category_all_hotel',[
              'verify' => false,
              'headers'       => $this->headers,
              '/delay/5',
              ['connect_timeout' => 3.14]
          ]);
          $response_outlet = $client->request('GET', url('api').'/outlet/get_outlet_all',[
            'verify' => false,
            'headers' => $this->headers,
            '/delay/5',
            ['connect_timeout' => 3.14]
          ]);
        }
        $body = $response->getBody();
        $response = json_decode($body, true);

        $bodyOutlet = $response_outlet->getBody();
        $response_outlet = json_decode($bodyOutlet, true);
        
        if(empty($response['data']))
        {
          $data['data'] = null;
          $data['dataOutlet'] = $response_outlet['data'];
        } else {
          $data['data'] = $response['data'];
          $data['dataOutlet'] = $response_outlet['data'];
        }
        return $data;
        
    }  
    catch (Throwable $e) {
      report($e);
      $error = ['modul' => 'get_menu_category_all_hotel',
                'actions' => 'get data menu category per hotel',
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
	 * Function: get data menu category all untuk di display di View
   *  dipanggil dari JS Data tabel View menu-category.blade.php
	 * body:  
	 *	$request	: 
	*/
  public function get_menu_category_all_user_hotel(Request $request){
    try {
        $client = new Client(); //GuzzleHttp\Client
        if(!empty($request->hotel_id))
        {
          $reqCat['hotel_id'] = $request->hotel_id;
          $response_outlet = $client->request('GET', url('api').'/outlet/get_hotel_outlet_with_user?hotel_id='.$reqCat['hotel_id'].'&user_id='.session()->get('id'),[
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
        if(!empty($request->user_id))
        {
          $reqCat['user_id'] = $request->user_id;
        }
        $response = $client->request('POST', url('api').'/menu/categories/get_menu_category_all_user_hotel',[
            'verify' => false,
            'headers'       => $this->headers,
            'form_params' => $reqCat,
            '/delay/5',
            ['connect_timeout' => 3.14]
        ]);

        $body = $response->getBody();
        $response = json_decode($body, true);

        $bodyOutlet = $response_outlet->getBody();
        $response_outlet = json_decode($bodyOutlet, true);
        
        if(empty($response['data']))
        {
          $data['data'] = null;
          $data['dataOutlet'] = $response_outlet['data'];
        } else {
          $data['data'] = $response['data'];
          $data['dataOutlet'] = $response_outlet['data'];
        }
        return $data;
        
    }  
    catch (Throwable $e) {
      report($e);
      $error = ['modul' => 'get_menu_category_all_user_hotel',
                'actions' => 'get data menu category per hotel per user',
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
     * Function: save data menu category
     * body: id (jika ada berarti edit, name, created_by, updated_by)
     *	$request	: 
    */
    public function save_menu_categories(Request $request)
    {		
        try{
            $messages = array(
                            'name.required' => __('message.name_required'),
                            'seq_no.required' => __('message.seq_no_required'),
                            'fboutlet_id.required' => __('message.id_outlet_required'),
                            
                          );
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'seq_no' => 'required',
                'fboutlet_id' => 'required',
                'show_in_menu' => 'required'
            ]);
            if ($validator->fails()) {
                // return response gagal
                $response = [
                    'status' => false,
                    'code' => 400 ,
                    'message' => $validator->errors()->first(),
                ];
                return $response;
            }
            if($request->id == null){
              // Edit Menu Category
              // Validasi Permission, hanya Admin dan User dengan outlet-menu-category-edit
              if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-menu-category-edit'))
              {
                  $data = [
                    'name' => $request->post('name'),
                    'seq_no' => $request->post('seq_no'),
                    'fboutlet_id' => $request->post('fboutlet_id'),
                    'show_in_menu' => $request->post('show_in_menu'),
                    'created_by' => $request->post('created_by'),
                    'updated_by' => $request->post('updated_by'),
                  ];
              } else {
                  return redirect('home');
              }
            } else {
              // Create New Menu Category
              // Validasi Permission, hanya Admin dan User dengan outlet-menu-category-create
              if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-menu-category-create'))
              {
                $data = [
                  'id' => $request->post('id'),
                  'name' => $request->post('name'),
                  'seq_no' => $request->post('seq_no'),
                  'fboutlet_id' => $request->post('fboutlet_id'),
                  'show_in_menu' => $request->post('show_in_menu'),
                  'created_by' => $request->post('created_by'),
                  'updated_by' => $request->post('updated_by'),
                ];
              } else {
                return redirect('home');
              }
            }
          
            $client = new Client(); //GuzzleHttp\Client
                $response = $client->request('POST', url('api').'/menu/categories/save_menu_categories',[
                'verify' => false,
                'form_params'   => $data,
                'headers'       => $this->headers,
                '/delay/5',
                  ['connect_timeout' => 3.14]
            ]);
              $client = null;
              $body = $response->getBody();
              $response = json_decode($body, true);
              if($response['status'] == true && $response['data'] != null){
                  return $response;
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
        catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'save_menu_categories',
                'actions' => 'save data menu category',
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
	 * Function: delete data menu outlet
	 * body: id
	 *	$request	: 
	*/
  public function delete_menu_category($id)
  {
    try{
        // Validasi Permission, hanya Admin dan User dengan outlet-menu-category-delete
        if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-menu-category-delete'))
        {
            $data = ['id' => $id];
            $client = new Client(); //GuzzleHttp\Client
                $response = $client->request('POST', url('api').'/menu/categories/delete_menu_categories',[
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
                          
            }else{
              return back()->with('error',$response['message']);
            }
        } else {
          return redirect('home');
        }
    }
    catch (Throwable $e) {
        report($e);
        $error = ['modul' => 'delete_menu_category',
                'actions' => 'delete data menu category',
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
	 * Function: get_outlet_all untuk Web Passing user_id jika ada
	 * body: user_id if exists
	 *	$request	: 
	*/
  public function get_outlet_all(Request $request)
  {
    try{
      // dd($request->user_id);
      if(session()->get('role')=='Admin') {
        $client = new Client(); //GuzzleHttp\Client
        $response = $client->request('GET', url('api').'/outlet/get_outlet_all',[
            'verify' => false,
            'headers'  => $this->headers,
            '/delay/5',
            ['connect_timeout' => 3.14]
        ]);
      } else {
        $client = new Client(); //GuzzleHttp\Client
        $response = $client->request('GET', url('api').'/outlet/get_outlet_all_with_user?user_id='.session()->get('id'),[
            'verify' => false,
            'headers' => $this->headers,
            '/delay/5',
            ['connect_timeout' => 3.14]
        ]);
      }
      $body = $response->getBody();
      $response = json_decode($body, true);
      if($response['status'] == true){
        return $response['data']; 
      } else {
        return null;
      }
    }
    catch (Throwable $e) {
        report($e);
        $error = ['modul' => 'get_outlet_all',
                'actions' => 'Get Data Outlet untuk Menu Category per User_ID',
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
	 * Function: GET Seq NO terakhir based from Outlet ID
	 * body: id_outlet MANDATORY
	 *	$request	: 
	*/
  public function get_seq_no_menu_category(Request $request)
  {
    try{
        $client = new Client(); //GuzzleHttp\Client
        $response = $client->request('GET', url('api').'/menu/get_seq_no?id_outlet='.$request->outlet_id,[
            'verify' => false,
            'headers'  => $this->headers,
            '/delay/5',
            ['connect_timeout' => 3.14]
        ]);
      $body = $response->getBody();
      $response = json_decode($body, true);
      // if($response['status'] == true){
        return $response; 
      // } else {
        // return $response;
      // }
    }
    catch (Throwable $e) {
        report($e);
        $error = ['modul' => 'get_seq_no_menu_category',
                'actions' => 'Get Data Outlet Sequence Number',
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
