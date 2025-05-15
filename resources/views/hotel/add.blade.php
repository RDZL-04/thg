<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add Master Hotel</title>
<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script
      src="{{ $url_google_maps }}"
      defer
    ></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    
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
        .dropdown-toggle {
        height: 40;
    }
    </style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
		{{ $judul }}
        </h2>
    </x-slot>
    <div id="app">
        @include('/flash-message')


        @yield('content')
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <br>
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                    <form id="addHotel" name="addhotel" action="{{ route('hotel.store') }}" method="post">
                    <fieldset>
                <legend>{{ __('hotel.general-info') }}</legend>
                        <div class="form-row">
                            <div class="col col-md-4">
                                <div class="form-group">
                                    <label for="txtName">{{__('hotel.hotel-name')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" id="txtName" placeholder="Name" name="txtName" value="{{ old('txtName') }}" autofocus required>
                                </div>
                                <div class="form-group">
                                    <label for="selectStar">{{__('hotel.star')}}</label><label style="color:red">* </label>
                                    <select name="selectStar" class="form-control select2" >
                                    
                                        <option value ="1" @if(old('selectStar')==1) {{'selected'}} @endif> 1</option>
                                        <option value ="2" @if(old('selectStar')==2) {{'selected'}} @endif> 2</option>
                                        <option value ="3" @if(old('selectStar')==3) {{'selected'}} @endif> 3</option>
                                        <option value ="4" @if(old('selectStar')==4) {{'selected'}} @endif> 4</option>
                                        <option value ="5" @if(old('selectStar')==5) {{'selected'}} @endif> 5</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="txtDescription">{{__('hotel.description')}}</label><label style="color:red">* </label>
                                    <textarea class="form-control" id="txtDescription" name="txtDescription" rows="5" placeholder="Enter Description" required>{{ old('txtDescription') }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="selectStatus">{{__('hotel.status')}}</label><label style="color:red">* </label>
                                    <select name="selectStatus" class="form-control select2" >
                                    @if(old('selectStatus')!=null)
                                        <option value="{{ old('selectStatus') }}"> 
                                        @if(old('selectStatus')== 1)
                                            {{__('hotel.active')}}
                                        @else
                                            {{__('hotel.not-active')}}
                                        @endif
                                        </option>
                                        @endif
                                        <option value ="1"> {{__('hotel.active')}}</option>
                                        <option value ="0">{{__('hotel.not-active')}}</option>
                                    </select>
                                </div>
                    
                                <div class="form-group">
                                    <label for="txtCity">{{__('hotel.city')}}</label><label style="color:red">* </label>
                                    <input type="hidden" class="form-control" name="old_city" id="old_city" placeholder="" value="{{ old('txtCity') }}">
                                    <select class="form-control md-6 selectpicker" name="txtCity" id="txtCity" data-size="5" data-live-search="true">
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="txtAddress">{{__('hotel.address')}}</label><label style="color:red">* </label>
                                    <textarea class="form-control" id="txtAddress" name="txtAddress" rows="5" placeholder="Enter Address" required>{{ old('txtAddress') }}</textarea>
                                </div>
                                
                            </div>
                            <div class="col col-md-4">
							@if (strtolower(session()->get('role'))=='admin')
                                <div class="form-group">
                                    <label for="txtBeHotelId">{{__('hotel.be-hotel-id')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtBeHotelId" id="txtBeHotelId" placeholder="BE Hotel Id" value="{{ old('txtBeHotelId') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="txtBeSecreetKey">{{__('hotel.be-secreet-key')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtBeSecreetKey" id="txtBeSecreetKey" placeholder="BE Secreet Key" value="{{ old('txtBeSecreetKey') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="txtBeApiKey">{{__('hotel.be-api-key')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtBeApiKey" id="txtBeApiKey" placeholder="BE API Key" value="{{ old('txtBeApiKey') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgMerchantId">{{__('hotel.mpg-merchant-id')}}</label>
                                    <input type="text" class="form-control" name="txtMpgMerchantId" id="txtMpgMerchantId" placeholder="MPG Merchant Id" value="{{ old('txtMpgMerchantId') }}" >
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgSecreetKey">{{__('hotel.mpg-secreet-key')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtMpgSecreetKey" id="txtMpgSecreetKey" placeholder="MPG Secreet Key" value="{{ old('txtMpgSecreetKey') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgApikey">{{__('hotel.mpg-api-key')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtMpgApiKey" id="txtMpgApiKey" placeholder="MPG API Key" value="{{ old('txtMpgApiKey') }}" required>
                                </div>
							@else
								<div class="form-group">
                                    <label for="txtBeHotelId">{{__('hotel.be-hotel-id')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtBeHotelId" id="txtBeHotelId" placeholder="BE Hotel Id" value="{{ old('txtBeHotelId') }}" required readonly>
                                </div>
                                <div class="form-group">
                                    <label for="txtBeSecreetKey">{{__('hotel.be-secreet-key')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtBeSecreetKey" id="txtBeSecreetKey" placeholder="BE Secreet Key" value="{{ old('txtBeSecreetKey') }}" required readonly>
                                </div>
                                <div class="form-group">
                                    <label for="txtBeApiKey">{{__('hotel.be-api-key')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtBeApiKey" id="txtBeApiKey" placeholder="BE API Key" value="{{ old('txtBeApiKey') }}" required readonly>
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgMerchantId">{{__('hotel.mpg-merchant-id')}}</label>
                                    <input type="text" class="form-control" name="txtMpgMerchantId" id="txtMpgMerchantId" placeholder="MPG Merchant Id" value="{{ old('txtMpgMerchantId') }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgSecreetKey">{{__('hotel.mpg-secreet-key')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtMpgSecreetKey" id="txtMpgSecreetKey" placeholder="MPG Secreet Key" value="{{ old('txtMpgSecreetKey') }}" required readonly>
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgApikey">{{__('hotel.mpg-api-key')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtMpgApiKey" id="txtMpgApiKey" placeholder="MPG API Key" value="{{ old('txtMpgApiKey') }}" required readonly>
                                </div>
							@endif
							
                                <div class="form-group">
                                    <label for="txtEmail">{{__('hotel.email_notice')}}</label><label style="color:red">* </label>
                                    <input type="email" class="form-control" name="txtEmail" id="txtEmail" placeholder="Email Notification" value="{{ old('txtEmail') }}" required>
                                </div>
								<div class="form-group">
                                    <label for="txtEmail">{{__('hotel.email_mice')}}</label><label style="color:red">* </label>
                                    <input type="email" class="form-control" name="txtEmailMice" id="txtEmailMice" placeholder="Email Mice" value="{{ old('txtEmailMice') }}" required>
                                </div>
								<div class="form-group">
                                    <label for="txtEmail">{{__('hotel.mice_wa')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtMiceWA" id="txtMiceWA" placeholder="Mice WA" value="{{ old('txtMiceWA') }}" data-validation="number" 
                                    data-validation-allowing="number" datavalidation-ignore="$" minlength="9" maxlength="15" required> <!--pattern="^(\+\d{0,8}\s)?\(?\d{4}\)?[\s.-]?\d{4}[\s.-]?\d{7}$" required>-->
                                </div>
                            </div>
                            <div class="col col-md-4">
                                <div class="form-group">
                                        <label for="Maps">{{__('hotel.maps')}}</label><label style="color:red">* </label>
                                        <input
                                        id="pac-input"
                                        class="controls"
                                        type="text"
                                        placeholder="Search Box"
                                    />
                                        <div id="googleMap" style="width:100%;height:380px;"></div>
                                        <input type="hidden" class="form-control" id="txtLongitude" name="txtLongitude" placeholder="Longitude" value="{{ old('txtLongitude') }}">
                                        <input type="hidden" class="form-control" id="txtLatitude" name="txtLatitude" placeholder="Latitude" value="{{ old('txtLatitude') }}">
                                        <input type="hidden" class="form-control" name="created_by" id="created_by" value="{{session()->get('full_name')}}">
          
                                </div>
                            </div>
                        </div>
                        @csrf
                        <div class="d-flex flex-row-reverse">
                            <div class="p-2">
                                <input type="button" class="btn btn-warning float-right" value="{{ __('button.cancel') }}" onclick="window.history.back()" >
                            </div>
                           <div class="p-2">
                                <button type="submit" class="btn btn-primary float-right" onkeypress="return event.keyCode != 13;" >{{ __('button.save') }}</button>
                           </div>
                        </div>
                    </fieldset>
                    </form>
                </div>
            </div>
        </div>
    
</div>
</x-app-layout>
<script>
    $(document).ready(function() {
        getCity();
    });
    // define variable untuk menampung markers
    var markers = [];
    // fungsi initialize untuk mempersiapkan peta
    function initMap() {
        const myLatlng = { lat: -6.927108, lng: 107.636275 };
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

    function getCity (){
        $.get('system/get_city',function (data)
            {
                //var oldCity = $('#old_city').val();
                var oldCity = "{{ old('txtCity') }}";
                console.log(oldCity);
                $('#txtCity').empty();
                $('#txtCity').append('<option value="">-- Select City --</option>');
                $.each(data, function(index, data)
                {
                    if(oldCity == data.system_cd){
                        $('#txtCity').append('<option value="'+data.system_cd+'" selected>'+data.system_value+'</option>' );
                    }else{
                        $('#txtCity').append('<option value="'+data.system_cd+'">'+data.system_value+'</option>');
                    }
                    $('#txtCity').selectpicker('refresh');
                });
            });
    }
</script>