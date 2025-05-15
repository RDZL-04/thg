<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $judul }}</title>
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"> -->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.bundle.min.js"></script> -->
<style>
.form-control {
	padding: .375rem;
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
                    <form id="addFacility" name="addFacility" action="{{ route('facility.add') }}" method="post" enctype="multipart/form-data">
                    <fieldset>
                <legend>{{__('hotel.facilities')}} :</legend>
                        <div class="form-row">
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="txtName">{{__('hotel.name-facilities')}}</label><label style="color:red">* </label>
                                    <input type="hidden" class="form-control" id="txtIdHotel" placeholder="IdHotel" name="txtIdHotel" value="{{ $id }}">
                                    @if (empty($data))
                                    <input type="text" class="form-control" id="txtName" placeholder="Name" name="txtName" value="{{ old('txtName') }}" autofocus required>
                                </div>
                                <div class="form-group">
                                    <label for="txtName">{{__('hotel.seq-no')}}</label><label style="color:red">* </label>
                                    <input type="number" class="form-control" id="txtSeqNo" name="txtSeqNo" value="{{ old('txtSeqNo', $txtSeqNo) }}" min="1" required>
                                    @else
                                    <input type="hidden" class="form-control" id="txtId" placeholder="Id" name="txtId" value="{{$data['id']}}">
                                    <input type="text" class="form-control" id="txtName" placeholder="Name" name="txtName" value="{{$data['name']}}" autofocus required>
                                </div>
                                <div class="form-group">
                                    <label for="txtName">{{__('hotel.seq-no')}}</label><label style="color:red">* </label>
                                    <input type="number" class="form-control" id="txtSeqNo" name="txtSeqNo" value="{{ $data['seq_no'] ? $data['seq_no'] : $txtSeqNo }}" min="1" required>
                                    @endif
                                    
                                </div>
                                
                            </div>
                            
                            <div class="col col-md-6">
                                <div class="form-group">
                                        <label for="Maps">{{__('hotel.icon')}}</label><label style="color:red">* </label>
                                        @if (empty($data))
                                        <input type='file' class="form-control form-control-sm"  name="file" id="file" value="{{ old('file') }}" required accept=".png,.svg" onchange="validate_input(event, this, ['.png','.svg'])" />
                                        <label style="font-size: 12px;">{{__('hotel.desc-icon')}}</label>
										
										<div class="alert alert-danger alert-block invalid-extension">
											<button type="button" class="close" data-dismiss="alert">×</button>    
											<div id="msg-invalid-ext" style="font-size: 12px;">
											{!! __('hotel.invalid-extension') !!}
											</div>
										</div>
										
                                        <img id="icon" src="{{url('/')}}/icon/default.jpg"  alt="your image" style="width:100%;height:380px;" value="{{ old('file') }}"/>
                                        @else
                                        <input type='hidden' name="oldFile" id="oldFile" value="{{$data['icon']}}"/>
                                        <input type='file' class="form-control form-control-sm"  name="file" id="file" value="{{$data['icon']}}" accept=".png,.svg" onchange="validate_input(event, this, ['.png','.svg'])" />
                                        <label style="font-size: 12px;">{{__('hotel.desc-icon')}}</label>
										
										<div class="alert alert-danger alert-block invalid-extension">
											<button type="button" class="close" data-dismiss="alert">×</button>    
											<div id="msg-invalid-ext" style="font-size: 12px;">
											{!! __('hotel.invalid-extension') !!}
											</div>
										</div>
										
                                        <img id="icon" src="{{url($data['icon'])}}" alt="your image" style="width:100%;height:380px;" />
                                        @endif
                                        <!-- <input type="text" class="form-control" id="txtLongitude" name="txtLongitude" placeholder="Longitude" value="{{ old('txtLongitude') }}">
                                        <input type="text" class="form-control" id="txtLatitude" name="txtLatitude" placeholder="Latitude" value="{{ old('txtLatitude') }}"> -->
                                </div>
                            </div>
                        </div>
                        @csrf
                        <div class="d-flex flex-row-reverse">
                            <div class="p-2">
                            <input type="button" class="btn btn-warning float-right" value="{{ __('button.cancel') }}" onclick="window.location.href = '{{ url('get_edit_hotel').'/'. $id}}'" >
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
	$('.invalid-extension').hide();
});

function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    
    reader.onload = function(e) {
      $('#icon').attr('src', e.target.result);
    }
    
    reader.readAsDataURL(input.files[0]); // convert to base64 string
  }
}

$("#file").change(function() {
  readURL(this);
});

function validate_input(e, oInput, _validFileExtensions) 
{
	//var self = this;
	
	if (oInput.type == "file") {
		//var sFileName = oInput.value;
		var sFileName = $(oInput)[0].files[0].name;
		 if (sFileName.length > 0) {
			var blnValid = false;
			for (var j = 0; j < _validFileExtensions.length; j++) {
				var sCurExtension = _validFileExtensions[j];
				if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
					blnValid = true;
					break;
				}
			}
			 
			if (!blnValid) {
				//"Sorry, {1} is invalid, allowed extensions are: {2}"
				var message = $('#msg-invalid-ext').html();
				message = message.replace('[filename]',sFileName);
				message = message.replace('[extensions]',_validFileExtensions.toString());
				$('#msg-invalid-ext').html(message);
				
				//this.alert(TEXT_INVALID_FILE, message);
				//alert("Invalid file type");
				oInput.value = "";
				$('.invalid-extension').show();
				return false;
			}
			else
			{
				$('.invalid-extension').hide();
				return true;
			}
		}
	}
	return false;
}
</script>