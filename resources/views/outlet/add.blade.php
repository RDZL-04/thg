<title>Add Master {{ __('outlet.title') }}</title>
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

        .dropdown-toggle {
            height: 40;
        }
    </style>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
		{{ __('button.add-outlet') }}
        </h2>
    </x-slot>
    <div id="app">
        {{-- @include('/flash-message') --}}
        @yield('content')
    </div>

    <div class="py-12"> 
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">@include('/flash-message')
            <br>
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                    <form id="addOutlet" name="addOutlet" action="{{ route('outlet.store') }}" method="post">
                    <fieldset>
                        <legend>General Information</legend>
                        <div class="form-row">
                            <div class="col col-md-4">
                            <div class="form-group">
                                <label for="selectHotel">{{ __('outlet.hotel-name') }} </label></label><label style="color:red">* </label>
                                <select name="selectIdHotel" id="selectIdHotel" class="form-control selectpicker" data-size="5" data-live-search="true" autofocus required>
                                    <option value="">--{{ __('combobox.select-hotel') }}--</option>
                                    @if(!empty($data))
                                    @foreach ($data as $datax)
                                        @if(old('selectIdHotel')!=null)
                                            @if($datax['id'] == old('selectIdHotel'))
                                                <option value="{{ $datax['id'] }}" selected>{{ $datax['name'] }}</option>
                                            @else
                                            <option value="{{ $datax['id'] }}">{{ $datax['name'] }}</option>
                                            @endif
                                        @else
                                            <option value="{{ $datax['id'] }}">{{ $datax['name'] }}</option>
                                        @endif
                                        
                                    @endforeach
                                    @endif
                                </select>
                                </div>
                                <div class="form-group">
                                    <label for="txtName">{{ __('outlet.restaurant-name') }} </label></label><label style="color:red">* </label>
                                    <input type="hidden" class="form-control" id="urlGoogleMaps" name="urlGoogleMaps" value="{{ $url_google_maps }}" required>
                                    <input type="text" class="form-control" id="txtName" placeholder="Name" name="txtName" value="{{ old('txtName') }}" required>
                                </div>
                               
                                <div class="form-group">
                                    <label for="txtAddress">{{ __('outlet.address') }} </label></label><label style="color:red">* </label>
                                    <textarea class="form-control" id="txtAddress" name="txtAddress" rows="3" placeholder="Enter Address" required>{{ old('txtAddress') }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="selectStatus">{{ __('outlet.status') }}</label></label><label style="color:red">* </label>
                                    <select name="selectStatus" class="form-control select2" required>
                                    @if(old('selectStatus')!=null)
                                        @if(old('selectStatus')== 1)
                                            {{ __('outlet.active') }}
                                        @else
                                            {{ __('outlet.not-active') }}
                                        @endif
                                        </option>
                                        @endif
                                        <option value ="1">{{ __('outlet.active') }}</option>
                                        <option value ="0">{{ __('outlet.not-active') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                <label for="selectSequence">{{ __('outlet.seq-no') }} </label></label><label style="color:red">* </label>
                                @if(old('txtSeqNo')!=null)
                                    <input type="number" class="form-control" id="txtSeqNo" name="txtSeqNo" value="{{ old('txtSeqNo') }}" required>
                                @else
                                    <input type="number" class="form-control" id="txtSeqNo" name="txtSeqNo" required>
                                @endif
                                </div>
                                <div class="form-group">
                                    <label for="txtOpen">{{ __('outlet.open_at') }}</label></label><label style="color:red">* </label>
                                    <input type="time" class="form-control" name="txtOpen" id="txtOpen" placeholder="{{ __('outlet.open_at') }}" value="{{ old('txtOpen') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="txtClose">{{ __('outlet.close_at') }}</label></label><label style="color:red">* </label>
                                    <input type="time" class="form-control" name="txtClose" id="txtClose" placeholder="{{ __('outlet.close_at') }}" value="{{ old('txtClose') }}" required>
                                </div>
                            </div>
                            <div class="col col-md-4">
                                <div class="form-group">
                                    <label for="txtMpgMerchantId">{{ __('outlet.tax') }}</label></label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtTax" id="txtTax" placeholder="{{ __('outlet.tax') }}" value="{{ old('txtTax') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgMerchantId">{{ __('outlet.service') }}</label></label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtService" id="txtService" placeholder="{{ __('outlet.service') }}" value="{{ old('txtService') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="txtDescription">{{ __('outlet.description') }}</label></label><label style="color:red">* </label>
                                    <textarea class="form-control" id="txtDescription" name="txtDescription" rows="3" placeholder="Enter Description" required>{{ old('txtDescription') }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgMerchantId">{{ __('outlet.mpg-merchant-id') }}</label></label>
                                    <input type="text" class="form-control" name="txtMpgMerchantId" id="txtMpgMerchantId" placeholder="MPG Merchant Id" value="{{ old('txtMpgMerchantId') }}" >
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgSecretKey">{{ __('outlet.mpg-secret-key') }}</label></label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtMpgSecretKey" id="txtMpgSecretKey" placeholder="MPG Secret Key" value="{{ old('txtMpgSecretKey') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="txtMpgApikey">{{ __('outlet.mpg-api-key') }}</label></label><label style="color:red">* </label>
                                    <input type="text" class="form-control" name="txtMpgApiKey" id="txtMpgApiKey" placeholder="MPG API Key" value="{{ old('txtMpgApiKey') }}" required>
                                </div>
                            </div>
                            <div class="col col-md-4">
                                <div class="form-group">
                                    <label for="Maps">{{ __('outlet.maps') }}</label></label><label style="color:red">* </label>
                                    <input
                                        id="pac-input"
                                        class="controls"
                                        type="text"
                                        placeholder="Search Box"
                                    />
                                    <div id="googleMap" style="width:100%;height:380px;">
                                    </div>
                                    <input type="hidden" class="form-control" id="txtLongitude" name="txtLongitude" placeholder="Longitude" value="{{ old('txtLongitude') }}" required>
                                    <input type="hidden" class="form-control" id="txtLatitude" name="txtLatitude" placeholder="Latitude" value="{{ old('txtLatitude') }}" required>
                                </div>
                            </div>
                        </div>
                        @csrf
                        <div class="d-flex flex-row-reverse">
                            <div class="p-2">
                                <input type="button" class="btn btn-warning float-right" value="{{ __('button.cancel') }}" onclick="window.location.href = '{{ url('master/fnboutlet') }}'" >
                            </div>
                           <div class="p-2">
                                <button type="submit" class="btn btn-primary float-right" >{{ __('button.save') }}</button>
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
    // define variable map
    var map
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

    $('#selectIdHotel').on('change',function(e){
        console.log(e.target.value);
        var hotel_id =  e.target.value;
        $.get('get_hotel_id/' + hotel_id, function (data)
            {
                $.each(data, function(index, data)
                {
                    console.log(data);
                    var txtAddress = document.getElementById('txtAddress');
                    var txtLongitude = document.getElementById('txtLongitude');
                    var txtLatitude = document.getElementById('txtLatitude');

                    txtAddress.value = data.address;
                    txtLongitude.value = data.longitude;
                    txtLatitude.value = data.latitude;

                    var myLatlng = { lat: parseFloat(data.latitude), lng: parseFloat(data.longitude) };
                    deleteMarkers();
                    addMarker(myLatlng);
                    //var map = new google.maps.Map(document.getElementById("googleMap"));
                    // var map = document.getElementById("googleMap");
                    // map.setCenter(myLatlng)
                    map.panTo(myLatlng)
                       
                });
        });
    });
    
</script>
