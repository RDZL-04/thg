
<title>Edit Master {{ __('outlet.title') }}</title>
<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script
       src="{{ $url_google_maps }}"
      defer
    ></script>
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
    </style>

    <style>
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
            Edit {{ __('outlet.title') }}
        </h2>
    </x-slot>
    
    <!-- General Info -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg"> @include('/flash-message')
            <div id="app">
        {{-- @include('/flash-message') --}}


        @yield('content')
    </div>
            <br>
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                    <form id="addOutlet" name="addOutlet" action="{{ route('outlet.edit') }}" method="post">
                    <fieldset>
                <legend>{{ __('outlet.general-info') }}</legend>
                        <div class="form-row">
                            <div class="col col-md-4">
                                <div class="form-group">
                                    <label for="selectHotel">{{ __('outlet.hotel-name') }} </label><label style="color:red">* </label>
                                    <input type="hidden" name="selectIdHotel" value="{{ $data['hotel_id'] }}">
                                    <select name="selectIdHotel" class="form-control select2" required disabled>
                                        @if($data['hotel_id']!=null)
                                            <option value="{{ $data['hotel_id'] }}" selected> {{  $data['hotel_name'] }} </option>
                                        @endif
                                        {{-- @foreach ($datax as $dataz)
                                            @if($data['hotel_id'] != $dataz['id'] )
                                                <option value="{{ $dataz['id'] }}">{{ $dataz['name'] }}</option>
                                            @endif
                                        @endforeach --}}
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" class="form-control" id="txtId" placeholder="id" name="txtId" value="{{ $data['id'] }}" readonly>
                                    <label for="txtName">{{ __('outlet.restaurant-name') }} </label><label style="color:red">* </label>
                                    <input type="text" class="form-control" id="txtRestaurantName" placeholder="Name" name="txtRestaurantName" value="{{ $data['name'] }}" autofocus required>
                                </div>
                               
                                <div class="form-group">
                                    <label for="txtAddress">{{ __('outlet.address') }} </label><label style="color:red">* </label>
                                    <textarea class="form-control" id="txtAddress" name="txtAddress" rows="3" placeholder="Enter Address" required>{{ $data['address']  }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="selectStatus">Status</label><label style="color:red">* </label>
                                    <select name="selectStatus" class="form-control select2" required>
                                    @if($data['status'] !=null)
                                        <option value="{{ $data['status']  }}"> 
                                        @if($data['status'] == 1)
                                            {{ __('outlet.active') }}
                                            <option value ="0"> {{ __('outlet.not-active') }}</option>
                                        @else
                                            {{ __('outlet.not-active') }}
                                            <option value ="1"> {{ __('outlet.active') }}</option>
                                        @endif
                                        </option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                <label for="selectSequence">{{ __('outlet.seq-no') }} </label><label style="color:red">* </label>
                                    <input type="number" class="form-control" id="txtSeqNo" name="txtSeqNo" value="{{ $data['seq_no'] }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="txtOpen">{{ __('outlet.open_at') }}</label></label><label style="color:red">* </label>
                                    <input type="time" class="form-control" name="txtOpen" id="txtOpen" placeholder="{{ __('outlet.open_at') }}" value="{{ $data['open_at'] }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="txtClose">{{ __('outlet.close_at') }}</label></label><label style="color:red">* </label>
                                    <input type="time" class="form-control" name="txtClose" id="txtClose" placeholder="{{ __('outlet.close_at') }}" value="{{ $data['close_at'] }}" required>
                                </div>
                            </div>
                            <div class="col col-md-4">
                                <div class="form-group">
                                    <label for="txtMpgMerchantId">{{ __('outlet.tax') }}</label></label><label style="color:red">* </label>
                                    <input type="number" class="form-control" name="txtTax" id="txtTax" placeholder="10" value="{{ $data['tax'] }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgMerchantId">{{ __('outlet.service') }}</label></label><label style="color:red">* </label>
                                    <input type="number" class="form-control" name="txtService" id="txtService" placeholder="10" value="{{ $data['service'] }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="txtDescription">{{ __('outlet.description') }}</label><label style="color:red">* </label>
                                    <textarea class="form-control" id="txtDescription" name="txtDescription" rows="3" placeholder="Enter Description" required>{{ $data['description']  }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgMerchantId">{{ __('outlet.mpg-merchant-id') }}</label>
                                    @if(session()->get('role')=='Admin')
                                    <input type="text" class="form-control" name="txtMpgMerchantId" id="txtMpgMerchantId" placeholder="MPG Merchant Id" value="{{ $data['mpg_merchant_id'] }}">
                                    @else
                                    <input type="text" class="form-control" name="txtMpgMerchant" id="txtMpgMerchant" placeholder="MPG Merchant Id" value="{{ $data['mpg_merchant_id'] }}" disabled>
                                    <input type="hidden" class="form-control" name="txtMpgMerchantId" id="txtMpgMerchantId" value="{{ $data['mpg_merchant_id'] }}">
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgSecretKey">{{ __('outlet.mpg-secret-key') }}</label><label style="color:red">* </label>
                                    @if(session()->get('role')=='Admin')
                                    <input type="text" class="form-control" name="txtMpgSecretKey" id="txtMpgSecretKey" placeholder="MPG Secret Key" value="{{ $data['mpg_secret_key'] }}" required>
                                    @else
                                    <input type="text" class="form-control" name="txtMpgSecretKey" id="txtMpgSecret" placeholder="MPG Secret Key" value="{{ $data['mpg_secret_key'] }}" required disabled>
                                    <input type="hidden" class="form-control" name="txtMpgSecretKey" id="txtMpgSecretKey" value="{{ $data['mpg_secret_key'] }}">
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgApikey">{{ __('outlet.mpg-api-key') }}</label><label style="color:red">* </label>
                                    @if(session()->get('role')=='Admin')
                                    <input type="text" class="form-control" name="txtMpgApiKey" id="txtMpgApiKey" placeholder="MPG API Key" value="{{ $data['mpg_api_key'] }}" required>
                                    @else
                                    <input type="text" class="form-control" name="txtMpgApi" id="txtMpgApi" placeholder="MPG API Key" value="{{ $data['mpg_api_key'] }}" required disabled>
                                    <input type="hidden" class="form-control" name="txtMpgApiKey" id="txtMpgApiKey" value="{{ $data['mpg_api_key'] }}">
                                    @endif
                                </div>
                            </div>
                            <div class="col col-md-4">
                                <div class="form-group">
                                    <label for="Maps">{{ __('outlet.maps') }}</label><label style="color:red">* </label>
                                    <input
                                        id="pac-input"
                                        class="controls"
                                        type="text"
                                        placeholder="Search Box"
                                    />
                                    <div id="googleMap" style="width:100%;height:380px;"></div>
                                    <input type="hidden" class="form-control" id="txtLongitude" name="txtLongitude" placeholder="Longitude" value="{{ $data['longitude'] }}" required>
                                    <input type="hidden" class="form-control" id="txtLatitude" name="txtLatitude" placeholder="Latitude" value="{{ $data['latitude'] }}" required>
                                </div>
                            </div>
                        </div>
                        @csrf
                        <div class="d-flex flex-row-reverse">
                            <div class="p-2">
                                <input type="button" class="btn btn-warning float-right" value="{{ __('button.cancel') }}" onclick="window.location.href = '{{ url('master/fnboutlet') }}'" >
                            </div>
                            @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-edit')) 
                            <div class="p-2">
                                <button type="submit" class="btn btn-primary float-right" onclick="return validate_time();">{{ __('button.save') }}</button>
                            </div>
                            @endif
                        </div>
                    </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- General Info -->

    <!-- Images -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                    
                    <br>
                    <fieldset>
                    <legend>{{ __('outlet.images') }}</legend>
                    @if ($data['outlet_images'] == null)
                    No Image
                    @else
                    <div class="row">
                    @foreach ($data['outlet_images'] as $img)
                        <div class="col-sm-4">
                            <div class="card">
                            <div class="card-body">
                                <center>
                                <img  src=" {{url($img['filename'])}}" alt="Card image cap text-center h-100" style="width:250px;height:250px;align:center">
                                </center>
                                #{{ $img['seq_no']}}
                                @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-edit'))
                                <p class="card-text"><small class="text-muted"> <a href="{{ url('master/fnboutlet/images/get_edit_outlet_images?data=') }}{{json_encode($img)}}&txtOutletId={{$data['id']}}">{{ __('button.edit') }}</a>
                                &nbsp;
                                <a href="{{ url('master/fnboutlet/images/delete_outlet_images') }}/{{$img['id']}}" class="button delete-confirm">{{ __('button.delete') }}</a>
                                </p></small>
                                @endif
                            </div>
                            </div>
                        </div>
                        
                        <br>
                    @endforeach
                    </div>
                    @endif
                    <br>
                    <form id="addImage" name="addimage" action="{{ route('outlet_images') }}" method="get">
                    <input type="hidden" class="form-control" id="txtOutletId" name="txtOutletId" value="{{ $data['id'] }}">
                    @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-edit'))
                    <button type="submit" class="btn btn-primary float-right" >{{ __('button.add-image') }}</button>    
                    @endif
                    </form>
                </div>
                <br>
            </div>
        </div>
    </div>
    <!-- Images -->

    <!-- Menu -->
    @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-menu-list'))
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                    
                    <br>
                    <fieldset>
                    <legend>{{ __('outlet.menu') }}</legend>
                    </fieldset>
                   
                    <form id="addMenu" name="addMenu" action="{{ route('outlet_menu.add') }}" method="get">
                        <div class="d-flex flex-row-reverse">
                            <div class="p-2">
                                <input type="hidden" class="form-control" id="txtRestName" name="txtRestName" value="{{ $data['name'] }}">
                                <input type="hidden" class="form-control" id="txtOutletId" name="txtOutletId" value="{{ $data['id'] }}">
                                @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-menu-create'))
                                <button type="submit" class="btn btn-primary float-right" >{{ __('button.add-menu') }}</button>    
                                @endif
                            </div>
                        </div>
                    </form>
                    
                    <thead>
                        <table class="table-auto table-bordered" id="menuTable" class="display" >
                            <thead>
                                <tr class="bg-gray-100" style="text-align:center" valign="middle" >
                                    <th class="px-2">{{ __('outlet.no') }}</th>
                                    <th class="px-4">{{ __('outlet.action') }}</th>
                                    <th class="px-5 w-20">{{ __('outlet.menu-name') }}</th>
                                    <th class="px-5 w-20">{{ __('outlet.description') }}</th>
                                    <th class="px-4">{{ __('outlet.price') }}</th>
                                    <th class="px-5 w-20">{{ __('outlet.category') }}</th>
                                    <th class="px-4">Status</th>
                                    <th class="px-4 text-center">{{ __('outlet.seq-no') }}</th>
                                    <th class="px-4 text-center">Promo</th>
                                    <th class="px-5">{{ __('outlet.created-by') }}</th>
                                    <th class="px-5">{{ __('outlet.created-at') }}</th>
                                    <th class="px-5">{{ __('outlet.updated-by') }}</th>
                                    <th class="px-5">{{ __('outlet.updated-at') }}</th>
                                </tr>
                            </thead>
                            <tbody id="tableBodyOutlet">
                                @if ($data['outlet_menus'] == null)
                                {{-- No Menus --}}
                                @else
                                @foreach ($data['outlet_menus'] as $datax)
                                    <tr id="{{ $datax['id'] }}">
                                        <td>{{$loop->iteration}}</td>
                                        <td class="px-4 py-2 text-center">
                                        @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-menu-edit'))
                                        <a href="edit_menu/{{ $data['id'] }}/{{ $datax['id'] }}/" ><i class="fas fa-pen"></i></a>
                                        &nbsp;
                                        @endif
                                        @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-menu-delete'))
                                        <a href="delete_menu/{{ $datax['id'] }}" class="button delete-confirm"><i class="fas fa-trash"></i></a>
                                        @endif
                                        </td>
                                        <td>{{ $datax['name'] }}</td>
                                        <td>{{ $datax['description'] }}</td>
                                        <td>{{ $datax['price'] }}</td>
                                        <td>{{ $datax['cat_name'] }}</td>
                                        <td>@if($datax['menu_sts'] =='1')
                                                Active
                                            @else
                                                Not Active
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $datax['seq_no'] }}</td>
                                        <td class="text-center">@if($datax['is_promo'] =='1')
                                            On
                                        @else
                                            Off
                                        @endif
                                        </td>
                                        <td>{{ $datax['created_by'] }}</td>
                                        <td class="text-center">{{ $datax['created_at'] }}</td>
                                        <td >{{ $datax['changed_by'] }}</td>
                                        <td class="text-center">{{ $datax['updated_at'] }}</td>
                                    </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                  
                    <br>
                </div>
                <br>
            </div>
        </div>
    </div>
    @endif
    <!-- Menu -->

     <!-- User Mapping -->
     <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                    
                    <br>
                    <fieldset>
                    <legend>{{ __('outlet.user') }}</legend>
                    </fieldset>
                   
                    <form id="addUser" name="addUser" action="{{ route('outlet_user.add') }}" method="get">
                        <div class="d-flex flex-row-reverse">
                            <div class="p-2">
                                <input type="hidden" class="form-control" id="txtRestName" name="txtRestName" value="{{ $data['name'] }}">
                                <input type="hidden" class="form-control" id="txtOutletId" name="txtOutletId" value="{{ $data['id'] }}">
                                @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-edit'))
                                <button type="submit" class="btn btn-primary float-right" >{{ __('button.add-user') }}</button>    
                                @endif
                            </div>
                        </div>
                    </form>
                    
                    <thead>
                        <table class="table-auto table-bordered" id="userTable" class="display" >
                            <thead>
                                <tr class="bg-gray-100" style="text-align:center" valign="middle" >
                                    <th class="px-2">{{ __('outlet.no') }}</th>
                                    <th class="px-4">{{ __('outlet.action') }}</th>
                                    <th class="px-5 w-20">{{ __('user.name') }}</th>
                                    <th class="px-5 w-20">{{ __('user.role') }}</th>
                                    <th class="px-5">{{ __('outlet.created-by') }}</th>
                                    <th class="px-5">{{ __('outlet.created-at') }}</th>
                                </tr>
                            </thead>
                            <tbody id="tableBodyOutlet">
                                @if ($data['outlet_user'] == null)
                                {{-- No User --}}
                                @else
                                @foreach ($data['outlet_user'] as $dataz)
                                    <tr id="{{ $dataz['id'] }}">
                                        <td>{{$loop->iteration}}</td>
                                        <td class="px-4 py-2 text-center">
                                            {{-- <a href="edit_outlet_user/{{ $data['id'] }}/{{ $dataz['id'] }}/" ><i class="fas fa-pen"></i></a>
                                            &nbsp; --}}
                                            @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-edit'))
                                            <a href="delete_outlet_user/{{ $dataz['id'] }}" class="button delete-confirm"><i class="fas fa-trash"></i></a>
                                            @endif
                                        </td>
                                        <td>{{ $dataz['full_name'] }}</td>
                                        <td>{{ $dataz['role_nm'] }}</td>
                                        <td>{{ $dataz['created_by'] }}</td>
                                        <td class="text-center">{{ $dataz['created_at'] }}</td>
                                    </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                  
                    <br>
                </div>
                <br>
            </div>
        </div>
    </div>
    <!-- User Mapping -->
        
</x-app-layout>
<script>
 // define variable map
 var map;
 // define variable untuk menampung markers
 var markers = [];
    // fungsi initialize untuk mempersiapkan peta
    function initMap() {
        var textLng = document.getElementById('txtLongitude').value;
        var textLat = document.getElementById('txtLatitude').value;
        //console.log(textLat);
        const myLatlng = { lat: parseFloat(textLat), lng: parseFloat(textLng) };
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

$(document).ready(function() {
    // fungsi initialize untuk mempersiapkan peta

    $('#menuTable').DataTable({
            scrollX: true,
            scrollY:"480px",
            scrollCollapse: true,
            pageLength: 10,
            info: false,
            ordering: false,
            retrieve: true,
            autoWidth: false,
            columnDefs: [
                { width: "50%", targets: 2 }
            ]
        }).columns.adjust()
        // .responsive.recalc()

            $('#menuTable').on( 'column-sizing.th', function ( e, settings ) {
            console.log( 'Column width recalculated in table' );
        } );

        $('#userTable').DataTable({
            scrollX: true,
            scrollY:"480px",
            scrollCollapse: true,
            pageLength: 10,
            info: false,
            ordering: false,
            retrieve: true,
            autoWidth: false,
            columnDefs: [
                { width: "50%", targets: 2 }
            ]
        }).columns.adjust()
        // .responsive.recalc()

            $('#userTable').on( 'column-sizing.th', function ( e, settings ) {
            console.log( 'Column width recalculated in table' );
        } );
 
});

$('.delete-confirm').on('click', function (event) {
    event.preventDefault();
    const url = $(this).attr('href');
        swal({
            title: 'Are you sure?',
            text: 'This record and it`s details will be permanently deleted!',
            icon: 'warning',
            buttons: ["Cancel", "Yes!"],
        }).then(function(value) {
            if (value) {
                window.location.href = url;
            }
        });
    });

//helper delay keystrokes    
function delay(callback, ms) {
  var timer = 0;
  return function() {
    var context = this, args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function () {
      callback.apply(context, args);
    }, ms || 0);
  };
}

   
$('#txtOpen').on('change', delay(function(e){
	validate_time();
}, 1000));

$('#txtClose').on('change', delay(function(e){
	validate_time();
}, 1000));

function validate_time()
{
	var t_one= $('#txtOpen').val();
	var t1 = t_one.split(":");
	var t_two = $('#txtClose').val();
	var t2 = t_two.split(":");
	var time1 = (+t1[0] * (60000 * 60)) + (+t1[1] * 60000);
	var time2 = (+t2[0] * (60000 * 60)) + (+t2[1] * 60000);

	if(time1 > time2) {
		swal({
            title: 'Invalid Time',
            text: 'Open & close times have invalid values',
            icon: 'warning',
			timer: 2000,
            buttons: [""],
        });
		return false;
	}
	
	return true;
}
    </script>
