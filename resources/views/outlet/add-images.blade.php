
<title>Edit Master {{ __('outlet.title') }}</title>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
		{{ $judul }}
        </h2>
    </x-slot>
    <div id="app">
        {{-- @include('/flash-message') --}}


        @yield('content')
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg"> @include('/flash-message')
            <br>
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                    <form id="addImages" name="addImages" action="{{ route('outlet_images.store') }}" method="post" enctype="multipart/form-data">
                    <fieldset>
                <legend>Images</legend>
                        <div class="form-row">
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="txtName">{{ __('outlet.name') }}:</label></label><label style="color:red">* </label>
                                    <input type="hidden" class="form-control" id="txtOutletId" placeholder="IdOutlet" name="txtOutletId" value="{{ $txtOutletId }}">
                                    @if (empty($data))
                                    <input type="hidden" class="form-control" id="txtId" placeholder="Id" name="txtId" value="">
                                    <input type="hidden" class="form-control" id="txtJenis" placeholder="Id" name="txtJenis" value="new">
                                    <input type="text" class="form-control" id="txtName" placeholder="Name" name="txtName" value="{{ old('txtName') }}" autofocus required>
                                </div>
                                <div class="form-group">
                                    <label for="txtName">{{ __('outlet.seq-no') }}:</label></label><label style="color:red">* </label>
                                    <input type="number" class="form-control" id="txtSeqNo" name="txtSeqNo" value="{{ old('txtSeqNo') }}" required>
                                    @else
                                    <input type="hidden" class="form-control" id="txtId" placeholder="Id" name="txtId" value="{{ $data['id']  }}">
                                    <input type="text" class="form-control" id="txtName" placeholder="Name" name="txtName" value="{{ $data['name']  }}" autofocus required>
                                    </div>
                                <div class="form-group">
                                    <label for="txtName">{{ __('outlet.seq-no') }}:</label></label><label style="color:red">* </label>
                                    <input type="number" class="form-control" id="txtSeqNo" name="txtSeqNo" value="{{ $data['seq_no'] }}" required>
                                    @endif
                                
                                </div>
                                
                            </div>
                            
                            <div class="col col-md-6">
                                <div class="form-group">
                                
                                        <label for="Images">{{ __('outlet.images') }}</label><label style="color:red">* </label>
                                        
                                        @if (empty($data))
                                        <input class="form-control form-control-sm" type='file' name="images" id="images" value="{{ old('images') }}" required/>
                                        <label style="font-size: 12px;">{{ __('outlet.desc-image') }}</label>
                                        <img id="icon" src="{{url('/')}}/hotel-images/default.jpg" alt="your image" style="width:100%;height:380px;" />
                                        @else
                                        <input type='hidden' name="oldImages" id="Oldimages" value="{{ $data['filename'] }}"/>
                                        
                                        <input class="form-control form-control-sm" type='file' name="images" id="images" value="{{url($data['filename'])}}"/>
                                        <label style="font-size: 12px;">{{ __('outlet.desc-image') }}</label>
                                        <img id="icon" src="{{url($data['filename'])}}" alt="your image" style="width:100%;height:380px;" />
                                        @endif
                                </div>
                            </div>
                        </div>
                        @csrf
                        <div class="d-flex flex-row-reverse">
                            <div class="p-2">
                                <input type="button" class="btn btn-warning float-right" value="{{ __('button.cancel') }}" onclick="window.location.href = '{{ url('master/fnboutlet/get_edit_outlet/').'/'. $txtOutletId}}'" >
                            </div>
                            @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-edit'))
                            <div class="p-2">
                                <button type="submit" class="btn btn-primary float-right" >{{ __('button.save') }}</button>
                            </div>
                            @endif
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

$("#images").change(function() {
  readURL(this);
});
</script>