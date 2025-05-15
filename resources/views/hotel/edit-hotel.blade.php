<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Master Hotel</title>
    <script
      src="{{ $url_google_maps }}"
      defer></script>
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">
    <style>
    .pac-card {
            margin: 10px 10px 0 0;
            border-radius: 2px 0 0 2px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            outline: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            background-color: #fff;
            font-family: Roboto;
        }

        #pac-container {
            padding-bottom: 12px;
            margin-right: 12px;
        }

        .pac-controls {
            display: inline-block;
            padding: 5px 11px;
        }

        .pac-controls label {
            font-family: Roboto;
            font-size: 13px;
            font-weight: 300;
        }

        #pac-input {
            background-color: #fff;
            font-family: Roboto;
            font-size: 15px;
            font-weight: 300;
            margin-left: 0px;
            padding: 0 11px 0 13px;
            text-overflow: ellipsis;
            width: 150px;
        }

        #pac-input:focus {
            border-color: #4d90fe;
        }
        #loaderHotelUser {
        display:none;
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 120px;
        height: 120px;
        -webkit-animation: spin 2s linear infinite; /* Safari */
        animation: spin 2s linear infinite;
        }
        #loaderHotelUser.show {
        display : block;
        }
        #hotelUser.hide {
        display : none;
        }

        #loader {
        display:none;
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 120px;
        height: 120px;
        -webkit-animation: spin 2s linear infinite; /* Safari */
        animation: spin 2s linear infinite;
        }
        #loader.show {
        display : block;
        }
        #facility.hide {
        display : none;
        }
        /* Safari */
        @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
        }
        .dropdown-toggle {
        height: 40;
        }
        th, td {
        white-space: nowrap;
        }
        div.dataTables_wrapper {
            width: 100%;
            margin: 0 auto;
        }
</style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
		{{ $judul }}
        </h2>
        </x-slot>
        
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div id="app">
        @include('/flash-message')
        @yield('content')
    </div>
            <br>
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                    <form id="editHotel" name="editHotel" action="{{ route('hotel.edit') }}" method="post">
                    <fieldset>
                <legend>{{ __('hotel.general-info') }}</legend>
                        <div class="form-row">
                        
                            <div class="col col-md-4">
                                <div class="form-group">
                                <input type="hidden" class="form-control" id="txtId" placeholder="id" name="txtId" value="{{ $data['id'] }}" readonly>
                                    <label for="txtName">{{__('hotel.hotel-name')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" id="txtName" placeholder="Name" name="txtName" value="{{ $data['name'] }}" autofocus required >
                                </div>
                                <div class="form-group">
                                    <label for="selectStar">{{__('hotel.star')}}</label><label style="color:red">* </label>
                                    <select name="selectStar" class="form-control select2" >
                                        <option value="{{ $data['hotel_star'] }}"> {{ $data['hotel_star'] }} </option>
                                        <option value ="1"> 1</option>
                                        <option value ="2"> 2</option>
                                        <option value ="3"> 3</option>
                                        <option value ="4"> 4</option>
                                        <option value ="5"> 5</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="txtDescription">{{__('hotel.description')}}</label><label style="color:red">* </label>
                                    <textarea class="form-control" id="txtDescription" name="txtDescription" rows="3" placeholder="Enter Description" required>{{ $data['description'] }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="selectStatus">{{__('hotel.status')}}</label><label style="color:red">* </label>
                                    <select name="selectStatus" class="form-control select2" >
                                        @if($data['status'] == 1)
                                        <option value="{{ $data['status'] }}" selected> 
                                            {{__('hotel.active')}}
                                        </option>
                                        <option value ="0">{{__('hotel.not-active')}}</option>
                                        @else
                                        <option value="{{ $data['status'] }}" selected> 
                                            {{__('hotel.not-active')}}
                                        </option>
                                            <option value ="1"> {{__('hotel.active')}}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="txtCity">{{__('hotel.city')}} </label><label style="color:red">* </label>
                                    <select class="form-control md-6 selectpicker" name="txtCity" id="txtCity" data-size="5" data-live-search="true">
                                    </select>
                                </div> 
                                <div class="form-group">
                                    <label for="txtAddress">{{__('hotel.address')}}</label><label style="color:red">* </label>
                                    <textarea class="form-control" id="txtAddress" name="txtAddress" rows="3" placeholder="Enter Address" required>{{ $data['address'] }}</textarea>
                                </div>
                                
                            </div>
                            <div class="col col-md-4">
							@if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-apikey-update'))
                                <div class="form-group">
                                    <label for="txtBeHotelId">{{__('hotel.be-hotel-id')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtBeHotelId" id="txtBeHotelId" placeholder="BE Hotel Id" value="{{ $data['be_hotel_id'] }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="txtBeSecreetKey">{{__('hotel.be-secreet-key')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtBeSecreetKey" id="txtBeSecreetKey" placeholder="BE Secreet Key" value="{{ $data['be_secret_key'] }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="txtBeApiKey">{{__('hotel.be-api-key')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtBeApiKey" id="txtBeApiKey" placeholder="BE API Key" value="{{ $data['be_api_key'] }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgMerchantId">{{__('hotel.mpg-merchant-id')}}</label>
                                    <input type="text" class="form-control" name="txtMpgMerchantId" id="txtMpgMerchantId" placeholder="MPG Merchant Id" value="{{ $data['mpg_merchant_id'] }}">
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgSecreetKey">{{__('hotel.mpg-secreet-key')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtMpgSecreetKey" id="txtMpgSecreetKey" placeholder="MPG Secreet Key" value="{{ $data['mpg_secret_key'] }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgApikey">{{__('hotel.mpg-api-key')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtMpgApiKey" id="txtMpgApiKey" placeholder="MPG API Key" value="{{ $data['mpg_api_key'] }}" required>
                                </div>
								
							@else
								
								<div class="form-group">
                                    <label for="txtBeHotelId">{{__('hotel.be-hotel-id')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtBeHotelId" id="txtBeHotelId" placeholder="BE Hotel Id" value="{{ $data['be_hotel_id'] }}" required readonly>
                                </div>
                                <div class="form-group">
                                    <label for="txtBeSecreetKey">{{__('hotel.be-secreet-key')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtBeSecreetKey" id="txtBeSecreetKey" placeholder="BE Secreet Key" value="{{ $data['be_secret_key'] }}" required readonly>
                                </div>
                                <div class="form-group">
                                    <label for="txtBeApiKey">{{__('hotel.be-api-key')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtBeApiKey" id="txtBeApiKey" placeholder="BE API Key" value="{{ $data['be_api_key'] }}" required readonly>
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgMerchantId">{{__('hotel.mpg-merchant-id')}}</label>
                                    <input type="text" class="form-control" name="txtMpgMerchantId" id="txtMpgMerchantId" placeholder="MPG Merchant Id" value="{{ $data['mpg_merchant_id'] }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgSecreetKey">{{__('hotel.mpg-secreet-key')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtMpgSecreetKey" id="txtMpgSecreetKey" placeholder="MPG Secreet Key" value="{{ $data['mpg_secret_key'] }}" required readonly>
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgApikey">{{__('hotel.mpg-api-key')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtMpgApiKey" id="txtMpgApiKey" placeholder="MPG API Key" value="{{ $data['mpg_api_key'] }}" required readonly>
                                </div>
							@endif
							
                                <div class="form-group">
                                    <label for="txtEmail">{{__('hotel.email_notice')}}</label><label style="color:red">* </label>
                                    <input type="email" class="form-control" name="txtEmail" id="txtEmail" placeholder="Email Notification" value="{{ $data['email_notification'] }}" required>
                                </div>
								<div class="form-group">
                                    <label for="txtEmail">{{__('hotel.email_mice')}}</label><label style="color:red">* </label>
                                    <input type="email" class="form-control" name="txtEmailMice" id="txtEmailMice" placeholder="Email Mice" value="{{ $data['mice_email'] }}" required>
                                </div>
								<div class="form-group">
                                    <label for="txtEmail">{{__('hotel.mice_wa')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtMiceWA" id="txtMiceWA" placeholder="Mice WA" value="{{ $data['mice_wa'] }}" data-validation="number" 
                                    data-validation-allowing="number" datavalidation-ignore="$" minlength="9" maxlength="15" required> <!--pattern="^(\+\d{0,8}\s)?\(?\d{4}\)?[\s.-]?\d{4}[\s.-]?\d{7}$" required>-->
                                </div>
                            </div>
                            <div class="col col-md-4">
                                <div class="form-group">
                                        <label for="Maps">{{__('hotel.maps')}}</label>
                                        <input
                                        id="pac-input"
                                        class="controls"
                                        type="text"
                                        placeholder="Search Box"
                                    />
                                        <div id="googleMap" style="width:100%;height:380px;">
                                        </div>
                                        <input type="hidden" class="form-control" id="txtLongitude" name="txtLongitude" placeholder="Longitude" value="{{ $data['longitude'] }}" required>
                                        <input type="hidden" class="form-control" id="txtLatitude" name="txtLatitude" placeholder="Latitude" value="{{ $data['latitude'] }}" required>
                                        <input type="hidden" class="form-control" name="updated_by" id="updated_by" value="{{session()->get('full_name')}}">
                                </div>
                            </div>    
                                
                        </div>
                        @csrf
                        <div class="d-flex flex-row-reverse">
                            <div class="p-2">
                            <input type="button" class="btn btn-warning float-right" value="{{ __('button.cancel') }}" onclick="window.location.href = '{{ url('hotel')}}'" >
                                
                            </div>
                           <div class="p-2">
                                <button type="submit" class="btn btn-primary float-right" onkeypress="return event.keyCode != 13;" >{{ __('button.save') }}</button>
                           </div>
                    </fieldset>
                    </form>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                
                    <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                        
                        <br>
                        <fieldset>
                        <legend>{{__('hotel.hotel-user')}}</legend>
                        </fieldset>
                        <thead>
                        <center>
                    <div class="max-w-5xl mx-auto sm:px-6 lg:px-4" id="loaderHotelUser"></div>
                    </center>
                    @if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-user-add'))
                            <form id="addUser" name="addUser" action="{{ route('add_user_hotel') }}" method="get">
                            <!-- <div class="d-flex flex-row-reverse"> -->
							   <!-- <div class="p-2"> -->
									<input type="hidden" class="form-control" id="txtId" placeholder="Id" name="txtId" value="{{ $data['id'] }}">
									<input type="hidden" class="form-control" id="txtName" placeholder="Id" name="txtName" value="{{ $data['name'] }}">
									<button type="submit" class="btn btn-primary float-right" onkeypress="return event.keyCode != 13;" >{{ __('button.add-user') }}</button>
							   <!-- </div> -->
							<!-- </div>  -->
                            </form>
                            @else
                            <br>
                            @endif
                            <br>
                        <div id="hotelUser">
                            <table class="table-auto table-bordered" id="hotelUserTable" class="display" style="width:100%" >
                            <thead>
                                <tr class="bg-gray-100" style="text-align : center">
                                    <th class="px-4 py-2">{{__('hotel.no')}}</th>
                                    @if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-user-delete'))
                                    <th class="px-4 py-2">{{__('hotel.action')}}</th>
                                    @endif
                                    <th class="px-4 py-2">{{__('hotel.name')}}</th>
                                    <th class="px-4 py-2">{{__('hotel.email')}}</th>
                                    <th class="px-4 py-2">{{__('hotel.role')}}</th>
                                    <th class="px-4 py-2">{{__('hotel.created-by')}}</th>
                                    <th class="px-4 py-2">{{__('hotel.created-at')}}</th>
                                </tr>
                            </thead>
                                <tbody>
                                @if ($data_user_hotel != null)
                                
                                @foreach ($data_user_hotel as $data_user)
                                
                                <tr id="{{ $data_user['id'] }}" style="text-align : center">
                                                <td>{{$loop->iteration}}</td>
                                                @if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-user-delete'))
                                                <td>
                                                <a href="{{ url('hotel/delete_hotel_user/') }}/{{$data_user['id']}}/{{ $data['id'] }}" class="button delete-confirm-user"><i class="fas fa-trash"></i></a>
                                                </td>
                                                @endif
                                                <td style="text-align:left">{{ $data_user['full_name']}}</td>
                                                <td style="text-align:left">{{ $data_user['email']}}</td>
                                                <td style="text-align:left">{{ $data_user['role_nm']}}</td>
                                                <td style="text-align:left">{{ $data_user['created_by']}}</td>
                                                <td style="text-align:center">{{ $data_user['created_at']}}</td>
                                    </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>  
                            <br>                          
                    </div>
                </div>
            </div>
        </div>

        
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                    
                    <br>
                    <fieldset>
                    <legend>{{__('hotel.facilities')}}</legend>
                    </fieldset>
                    <thead>
                    <center>
                    <div class="max-w-5xl mx-auto sm:px-6 lg:px-4" id="loader"></div>
                    </center>
                    @if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-facility-add'))
							<form id="addFacility" name="addFacility" action="{{ route('facility') }}" method="get">
								<input type="hidden" class="form-control" id="txtId" placeholder="Id" name="txtId" value="{{ $data['id'] }}">
								<button type="submit" class="btn btn-primary float-right" >{{ __('button.add-facility') }}</button>    
							</form>
							@endif
                            <br>
                        <div id="facility">
                            <table class="table-auto table-bordered facility" id="facilityTable" class="display" style="width:100%" >
                            <thead>
                                <tr class="bg-gray-100" style="text-align : center">
                                    <th class="px-4 py-2">
                                    @if(!empty(Request::get('checked')))
                                    <input type="checkbox" class = "check_All" id_hotel="{{ $data['id'] }}" id="checkAll" name="checkAll" checked></th>
                                    @elseif(Request::get('checked') == 1)
                                    <input type="checkbox" class = "check_All" id_hotel="{{ $data['id'] }}" id="checkAll" name="checkAll"></th>
                                    @else
                                    <input type="checkbox" class = "check_All" id_hotel="{{ $data['id'] }}" id="checkAll" name="checkAll" ></th>
                                    @endif
									
									@if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-facility-update') || Auth::user()->permission('hotel-facility-delete'))
                                    <th class="px-4 py-2">{{__('hotel.action')}}</th>
									@endif
									
                                    <th class="px-4 py-2">{{__('hotel.name-facilities')}}</th>
                                    <th class="px-4 py-2">{{__('hotel.icon')}}</th>
                                    <th class="px-4 py-2">{{__('hotel.seq-no')}}</th>
                                    <th class="px-4 py-2" style="text-align:center" valign="middle">{{__('hotel.created-at')}}</th>
                                    <th class="px-4 py-2" style="text-align:center" valign="middle">{{__('hotel.updated-at')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if ($data_facility != null)
                            @foreach ($data_facility as $df)
                            
                            <tr id="{{ $df['id'] }}" style="text-align : center">
                                            <td>
                                            @if($df['hotel_id'] == $data['id'])
                                            <input type="checkbox" class = "check_facility" id="check" id_hotel="{{ $data['id'] }}"  id_facility="{{ $df['id'] }}" name="check" checked >
                                            @else
                                            <input type="checkbox" class="check_facility" id="check" id_hotel="{{ $data['id'] }}" id_facility="{{ $df['id'] }}" name="check">
                                            @endif
                                            </td>
							@if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-facility-update') || Auth::user()->permission('hotel-facility-delete'))
                                            <td class="px-4 py-2">
								@if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-facility-update'))
												<a href="{{ url('edit_hotel_facility?data=') }}{{json_encode($df)}}&id_hotel={{$data['id']}}"><i class="fas fa-pen"></i></a>
												&nbsp;&nbsp;&nbsp;
								@endif
								@if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-facility-delete'))
												<a href="{{ url('delete_facility') }}/{{$df['id']}}" class="button delete-confirm"><i class="fas fa-trash"></i></a>
								@endif
                                            <!-- <a onclick="deleteData('{{ url('delete_facility') }}','{{$df['id']}}')" class="button delete-confirm"><i class="fas fa-trash"></i></a> -->
                                            </td>
							@endif
                                            <td style="text-align:left">{{ $df['name']}}</td>
                                            
                                            <td align="center" ><img src="{{url($df['icon'])}}" alt="Image" width="50" height="50" /></td>
                                            <td>{{ $df['seq_no'] }}</td>
                                            <td style="text-align:center" valign="middle">{{ $df['created_at'] }}</td>
                                            <td style="text-align:center" valign="middle">{{ $df['updated_at'] }}</td>
                                </tr>
                                @endforeach
                            @endif
                            </tbody>
                            </table>
                            <br>
                        </div>
                </div>
                <br>
            </div>
            </div>
	
	<!-- Start near attraction -->
    <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                
                    <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                        <br>
                        <fieldset>
                        <legend>{{__('hotel.near_attraction_hotel')}}</legend>
                        </fieldset>
                        <thead>
                        <center>
                    <div class="max-w-5xl mx-auto sm:px-6 lg:px-4" id=""></div>
                    </center>
                    @if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-attraction-add'))
                            <form id="addAttraction" name="addAttraction" action="{{ route('add_attraction') }}" method="get">
                            <!-- <div class="d-flex flex-row-reverse"> -->
							   <!-- <div class="p-2"> -->
									<input type="hidden" class="form-control" id="txtId" placeholder="Id" name="id_hotel" value="{{ $data['id'] }}">
									<input type="hidden" class="form-control" id="txtName" placeholder="Id" name="name_hotel" value="{{ $data['name'] }}">
									<button type="submit" class="btn btn-primary float-right" onkeypress="return event.keyCode != 13;" >{{ __('attraction.add_attraction') }}</button>
							   <!-- </div> -->
							<!-- </div>  -->
                            </form>
                            @else
                            <br>
                            @endif
                            <br>
                        <div id="hotelUser">
                            <table class="table-auto table-bordered facilities" id="attractionTable" class="display" style="width:100%" >
                            <thead>
                                <tr class="bg-gray-100" style="text-align : center">
                                    <th class="px-4 py-2" style="text-align:center">{{__('hotel.no')}}</th>
                                    @if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-facility-update') || Auth::user()->permission('hotel-facility-delete'))
                                    <th class="px-4 py-2" style="text-align:center">{{__('hotel.action')}}</th>
                                    @endif
                                    <th class="px-4 py-2" style="text-align:center">{{__('hotel.name')}}</th>
                                    <th class="px-4 py-2" style="text-align:center">{{__('hotel.category')}}</th>
                                    <th class="px-4 py-2" style="text-align:center">{{__('hotel.icon')}}</th>
                                    <th class="px-4 py-2" style="text-align:center">{{__('hotel.distance')}}</th>
                                    <th class="px-4 py-2" style="text-align:center">{{__('hotel.created-by')}}</th>
                                    <th class="px-4 py-2" style="text-align:center">{{__('hotel.created-at')}}</th>
                                    <th class="px-4 py-2" style="text-align:center">{{__('hotel.updated-by')}}</th>
                                    <th class="px-4 py-2" style="text-align:center">{{__('hotel.updated-at')}}</th>                              
                                </tr>
                            </thead>
                                <tbody>
                                @if ($data_near_attraction != null)
                                @foreach ($data_near_attraction as $near_attraction)       
                                <?php
                                    $spliter = explode(';',$near_attraction['attr_category']);
                                    $category = $spliter[0];
                                    $icon_type = $spliter[1];
                                    $icon_src = $spliter[2];
                                ?>                         
                                <tr id="{{ $near_attraction['id'] }}" style="text-align : center">
                                                <td>{{$loop->iteration}}</td>
                                                @if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-attraction-update') || Auth::user()->permission('hotel-attraction-delete'))
                                                <td>
                                                @if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-attraction-update'))
                                                <a href="{{ url('near/edit_attraction/') }}?id={{ $near_attraction['id']}}&name_hotel={{ $data['name'] }}" class="button"><i class="fas fa-pen"></i></a>
                                                @endif
                                                @if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-attraction-delete'))
                                                &nbsp;&nbsp;&nbsp;
                                                <a href="{{ url('near/delete_attraction/') }}/{{ $near_attraction['id'] }}" class="button delete-confirm-user"><i class="fas fa-trash"></i></a>
                                                @endif
                                                </td>
                                                @endif
                                                <td style="text-align:left">{{ $near_attraction['attraction_nm']}}</td>
                                                <td style="text-align:left">
                                                                            {{ $category}}
                                                </td>
                                                <td style="text-align:center">
                                                                            @if($icon_type == 'Ionicons')
                                                                            <ion-icon name="{{ $icon_src }}"></ion-icon>
                                                                            @elseif($icon_type == 'MaterialIcons')
                                                                            <span class="material-icons">{{ $icon_src }}</span>
                                                                            @endif
                                                </td>
                                                <td style="text-align:left">{{ $near_attraction['distance']}}</td>
                                                <td style="text-align:left">{{ $near_attraction['created_by']}}</td>
                                                <td style="text-align:center">{{ $near_attraction['created_at']}}</td>
                                                <td style="text-align:left">{{ $near_attraction['created_by']}}</td>
                                                <td style="text-align:center">{{ $near_attraction['created_at']}}</td>
                                    </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                            <br>
                            
                    </div>
                </div>
            </div>
        </div>
	<!-- End near attraction -->		
			
            <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                    
                    <br>
                    
                    <fieldset>
                    <legend>{{__('hotel.images-hotel')}}</legend>
                    @if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-image-add'))
                    <form id="addImage" name="addimage" action="{{ route('images_hotel') }}" method="get">
                    <input type="hidden" class="form-control" id="txtId" placeholder="Id" name="txtId" value="{{ $data['id'] }}">
                    <button type="submit" class="btn btn-primary float-right" >{{ __('button.add-image') }}</button>    
                    </form>
					@endif
                    <br>
                    @if ($data_img == null)
                    {{__('hotel.no-images')}}
                    @else
                    @foreach (array_chunk($data_img,3) as $img)
                    <div class="row">
                        @foreach ($img as $data_img)        
                        <div class="col-sm-4">
                            <div class="card">
                            <div class="card-body">
                                <center>
                                <img  src=" {{url($data_img['file_name'])}}" alt="Card image cap text-center h-100" style="width:100%;align:center">
                                </center>
                                #{{ $data_img['seq_no']}}
                                <p class="card-text">
								<small class="text-muted"> 
								@if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-image-update'))
								<a href="{{ url('get_edit_hotel_images?data=') }}{{json_encode($data_img)}}&id_hotel={{$data['id']}}">{{ __('button.edit') }}</a>
                                &nbsp;
								@endif
								@if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-image-delete'))
                                <a href="{{ url('delete_hotel_images') }}/{{$data_img['id']}}" onclick="deleteData()"class="button delete-confirm">{{ __('button.delete') }}</a>
								@endif
                                <!-- <a onclick="deleteData('{{ url('delete_hotel_images') }}','{{$data_img['id']}}')"class="button delete-confirm">{{ __('button.delete') }}</a> -->
                                </small>
								</p>
                            </div>
                            </div>
                        </div>
                        @endforeach
                        </div>
                        <br>
                    @endforeach
                    @endif
					
					
                </div>
                <br>
            </div>
</div>
</x-app-layout>
<script type="module" src="https://unpkg.com/ionicons@5.4.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule="" src="https://unpkg.com/ionicons@5.4.0/dist/ionicons/ionicons.js"></script>
<script>
   // define variable untuk menampung markers
   var markers = [];
    // fungsi initialize untuk mempersiapkan peta
    function initMap() {
        latitude = document.getElementById('txtLatitude').value;
        longitude =document.getElementById('txtLongitude').value;
        // console.log(latitude);
        const myLatlng = { lat: parseFloat(latitude) , lng: parseFloat(longitude) };
        // console.log(myLatlng)
        map = new google.maps.Map(document.getElementById("googleMap"), {
            zoom: 15,
            center: myLatlng,
            mapTypeId: "terrain",
        });
        // This event listener will call addMarker() when the map is clicked.
        map.addListener("click", (event) => {
            var textLng = document.getElementById('txtLongitude');
            var textLat = document.getElementById('txtLatitude');
            deleteMarkers();
            addMarker(event.latLng);
            var koordinat = JSON.parse(JSON.stringify(event.latLng.toJSON(), null, 2));
            var lat = koordinat['lat'];
            var lng = koordinat['lng'];                
            textLng.value = lng;
            textLat.value = lat;
        });
        // Adds a marker at the center of the map.
        addMarker(myLatlng);

            // Create the search box and link it to the UI element.
            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
            // Bias the SearchBox results towards current map's viewport.
            map.addListener("bounds_changed", () => {
                searchBox.setBounds(map.getBounds());
            });

            let markers = [];
            // Listen for the event fired when the user selects a prediction and retrieve
            // more details for that place.
            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();

                if (places.length == 0) {
                return;
                }
                // Clear out the old markers.
                markers.forEach((marker) => {
                marker.setMap(null);
                });
                markers = [];
                // For each place, get the icon, name and location.
                const bounds = new google.maps.LatLngBounds();
                places.forEach((place) => {
                if (!place.geometry) {
                    console.log("Returned place contains no geometry");
                    return;
                }
                const icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25),
                };
                // Create a marker for each place.
                // markers.push(
                //     new google.maps.Marker({
                //     map,
                //     icon,
                //     title: place.name,
                //     position: place.geometry.location,
                //     })
                // );

                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
                });
                map.fitBounds(bounds);
            });
       
    } // end initMap

    // Adds a marker to the map and push to the array.
    function addMarker(location) {
        const marker = new google.maps.Marker({
            position: location,
            map: map,
        });
        markers.push(marker);
    }

    // Sets the map on all markers in the array.
    function setMapOnAll(map) {
        for (let i = 0; i < markers.length; i++) {
            markers[i].setMap(map);
        }
    }

    // Removes the markers from the map, but keeps them in the array.
    function clearMarkers() {
        setMapOnAll(null);
    }

    // Deletes all markers in the array by removing references to them.
    function deleteMarkers() {
        clearMarkers();
        markers = [];
    }

    function loaderFacility() {
    var x = document.getElementById("loader");
    var y = document.getElementById("facility");
    y.className = "hide";
    x.className = "show";
    // setTimeout(function(){ x.className = x.className.replace("show", "hide"); }, 2000);
    }
    function loaderUser() {
    var x = document.getElementById("loaderHotelUser");
    var y = document.getElementById("hotelUser");
    y.className = "hide";
    x.className = "show";
    // setTimeout(function(){ x.className = x.className.replace("show", "hide"); }, 2000);
    }
    function getCity(){
        var url="{{url('system/get_city')}}";
        var city ="{{ $data['city'] }}";
        // console.log(city);
        $.get(url,function (data)
            {
                // console.log(data);
                $('#txtCity').empty();
                $('#txtCity').append('<option value="">-- Select City --</option>');
                $.each(data, function(index, data)
                {
                    if(city ==data.system_cd){
                        $('#txtCity').append('<option value="'+data.system_cd+'" selected>'+data.system_value+'</option>');    
                    }else{
                        $('#txtCity').append('<option value="'+data.system_cd+'">'+data.system_value+'</option>');
                    }
                    $('#txtCity').selectpicker('refresh');
                });
                // if (city != null){
                //     $('#txtCity').selectedIndex = city;
                // }
                
            });
    }

    function deleteData(url,id){
    url = url+'/'+id;
    console.log(url);
    // event.preventDefault();
    // const url = $(this).attr('href');
        swal({
            title: 'Are you sure?',
            text: 'This record and it`s details will be permanently deleted!',
            icon: 'warning',
            buttons: ["Cancel", "Yes!"],
        }).then(function(value) {
            if (value) {
                loaderFacility();
                window.location.href = url;
            }
        });
}
$(document).ready(function() {
    // fungsi initialize untuk mempersiapkan peta
    // loaderFacility() ;
    getCity();
    // userDataTable();
    
    $('#facilityTable').DataTable({
        scrollX: true,
        scrollY:"200px",
        scrollCollapse: true,
        info: false,
        ordering: false,
        lengthChange: false,
        paging:   false,
        searching: true,
    });
	
	$('#attractionTable').DataTable({
        scrollX: true,
        scrollY:"200px",
        scrollCollapse: true,
        info: true,
        lengthChange: false,
        autoWidth: true,
        ordering: false
    });
    

    // $('#hotelUserTable').DataTable();
    
    $('#hotelUserTable').DataTable({
        scrollX: true,
        scrollY:"200px",
        scrollCollapse: true,
        info: true,
        ordering: false
    });


    $('.delete-confirm').on('click', function (event) {
    console.log('ada');
    event.preventDefault();
    const url = $(this).attr('href');
        swal({
            title: '{{__('message.are_you_sure')}}',
            text: '{{__('message.delete_permanent')}}',
            icon: 'warning',
            buttons: ["{{__('message.btn_delete_cancel')}}", "{{__('message.btn_delete_yes')}}"],
        }).then(function(value) {
            if (value) {
                loaderFacility();
                window.location.href = url;
            }
        });
    });
    $('.delete-confirm-user').on('click', function (event) {
    event.preventDefault();
    const url = $(this).attr('href');
        swal({
            title: '{{__('message.are_you_sure')}}',
            text: '{{__('message.delete_not_permanent')}}',
            icon: 'warning',
            buttons: ["{{__('message.btn_delete_cancel')}}", "{{__('message.btn_delete_yes')}}"],
        }).then(function(value) {
            if (value) {
                loaderUser();
                window.location.href = url;
            }
        });
    });
    document.getElementById('checkAll').onclick = function() {
            var checkboxes = document.getElementsByName('check');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
            var id_hotel = $(this).attr("id_hotel")
                var data = {
                    hotel_id : id_hotel
                }
                var my_data = JSON.stringify(data)
            if(this.checked) {
                 //Checkbox has been checked
                loaderFacility();
                window.location.href = "{{ url('add_hotel_facility_all?data=') }}"+my_data;
            } else {
                    //Checkbox has been unchecked
                    loaderFacility();
                    window.location.href = "{{ url('delete_hotel_facility_all?data=') }}"+my_data;
                    }
    }
        
        $(".check_facility").change(function() {
        console.log('ada');
            var id_facility = $(this).attr("id_facility")
            var id_hotel = $(this).attr("id_hotel")
            
            var data = {
                facility_id : id_facility,
                hotel_id : id_hotel
            }
            var my_data = JSON.stringify(data)
        if(this.checked) {
            loaderFacility();
            setTimeout(window.location.href = "{{ url('add_hotel_facility?data=') }}"+my_data, 2000);
        } else {
                //Checkbox has been unchecked
                const url = "{{ url('delete_hotel_facility?data=') }}"+my_data; 
                loaderFacility();
                window.location.href = url;
                    // swal({
                    //     title: 'Are you sure?',
                    //     text: 'This record and it`s details will be permanently deleted!',
                    //     icon: 'warning',
                    //     buttons: ["Cancel", "Yes!"],
                    // }).then(function(value) {
                    //     if (value) {
                            
                           
                    //     }
                    // });
                }
        });
});


    </script>
