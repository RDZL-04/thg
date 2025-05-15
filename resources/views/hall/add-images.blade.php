<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $judul }}</title>
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"> --}}
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> --}}
    {{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script> --}}

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
                    <form id="addImages" name="addImages" action="{{ route('hall_images.store') }}" method="post" enctype="multipart/form-data">
                    <fieldset>
                <legend>{{__('hall.images-hall')}}</legend>
                        <div class="form-row">
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="txtName">{{__('hall.hall-image-name')}}</label><label style="color:red">* </label>
                                    
                                    @if (empty($data))
                                    <input type="hidden" class="form-control" id="txtHallId" name="txtHallId"  value="{{ $id }}">
                                    <input type="text" class="form-control" id="txtName" placeholder="Name" name="txtName" value="{{ old('txtName') }}" autofocus required>
                                </div>
                                <div class="form-group">
                                    <label for="txtName">{{__('hotel.seq-no')}}</label><label style="color:red">* </label>
                                    @if(!empty(old('txtSeqNo')))
                                    <input type="number" class="form-control" id="txtSeqNo" name="txtSeqNo" value="{{ old('txtSeqNo') }}" min="1" required>
                                    @elseif(!empty($seqNo))
                                    <input type="number" class="form-control" id="txtSeqNo" name="txtSeqNo" value="{{ $seqNo }}" min="1" required>
                                    @endif
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
                                        @else
                                            <option value ="1"> {{__('hotel.active')}}</option>
                                            <option value ="0">{{__('hotel.not-active')}}</option>
                                        @endif
                                    </select>
                                    @else
                                    <input type="hidden" class="form-control" id="txtHallId" name="txtHallId" value="{{ $data['hall_id'] }}">
                                    <input type="hidden" class="form-control" id="txtId" placeholder="Id" name="txtId" value="{{ $data['id']  }}" autofocus required>
                                    <input type="text" class="form-control" id="txtName" placeholder="Name" name="txtName" value="{{ $data['name']  }}" required>
                                </div>
                               
                                <div class="form-group">
                                    <label for="txtName">{{__('hotel.seq-no')}}</label><label style="color:red">* </label>
                                    <input type="number" class="form-control" id="txtSeqNo" name="txtSeqNo" value="{{ $data['seq'] }}" min="1" required>
                                </div>
                                <div class="form-group">
                                    <label for="selectStatus">{{__('hotel.status')}}</label><label style="color:red">* </label>
                                    <select name="selectStatus" class="form-control select2" >
                                    <option value="{{ $data['status'] }}"> 
                                        @if($data['status']== 1)
                                            {{__('hotel.active')}}
                                    </option>
                                    <option value ="0">{{__('hotel.not-active')}}
                                        @else
                                            {{__('hotel.not-active')}}
                                    </option>
                                    <option value ="1">{{__('hotel.active')}}
                                        @endif
                                    </option>
                                    </select>
                                    @endif
                                </div>
                                
                            </div>
                            
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="Images">{{__('hotel.image')}}</label><label style="color:red">* </label>
                                    @if (empty($data))
                                    <input class="form-control form-control-sm" name="filename" id="filename" type="file" required/>
                                    <label style="font-size: 12px;">{{__('hotel.image-desc')}}</label>
                                    <img id="icon" src="{{url('/')}}/hotel-images/default.jpg" alt="your image" style="width:100%;height:380px;" />
                                    @else
                                    <input type='hidden' name="oldImages" id="Oldimages" value="{{$data['filename']}}"/>
                                    <input class="form-control form-control-sm" type='file' name="filename" id="filename" value="{{url($data['filename'])}}" />
                                    <label style="font-size: 12px;">{{__('hotel.image-desc')}}</label>
                                    <img id="icon" src="{{url($data['filename'])}}" alt="your image" style="width:100%;height:380px;" />
                                    @endif
                                </div>
                            </div>
                        </div>
                        @csrf
                        <div class="d-flex flex-row-reverse">
                            <div class="p-2">
                            @if(empty($data))
                            <input type="button" class="btn btn-warning float-right" value="{{ __('button.cancel') }}" onclick="window.location.href = '{{ url('mice/get_edit_hall').'/'. $id}}'" >
                            @else
                            <input type="button" class="btn btn-warning float-right" value="{{ __('button.cancel') }}" onclick="window.location.href = '{{ url('mice/get_edit_hall').'/'. $data['hall_id']}}'" >
                            @endif
                            </div>
                           <div class="p-2">
                                <button type="submit" class="btn btn-primary float-right" onkeypress="return event.keyCode != 13;" >{{ __('button.save') }}</button>
                           </div>
                    </fieldset>
                    </form>
                </div>
            </div>
        </div>
    
</div>
</x-app-layout>
<script>
function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    
    reader.onload = function(e) {
      $('#icon').attr('src', e.target.result);
    }
    
    reader.readAsDataURL(input.files[0]); // convert to base64 string
  }
}

$("#filename").change(function() {
  readURL(this);
});
</script>