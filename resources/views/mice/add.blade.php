<!DOCTYPE html>
<title>Add Mice Category for Hotel</title>
    <style>
        .text-editor {
            width: 100%;
            height: 200%;
        }
        .dropdown-toggle {
            height: 40;
        }
    </style>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
		{{ $judul }} Mice Category
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
                    <form id="addMiceCategory" name="addMiceCategory" action="{{ route('mice_category.store') }}" method="post" enctype="multipart/form-data">
                    <fieldset>
                        <legend></legend>
                        <div class="form-row">
                            <div class="col col-md-6 mx-auto">
                                <div class="form-group">
                                    <label for="selectHotel">{{ __('outlet.hotel-name') }} </label></label><label style="color:red">* </label>
                                    <select name="selectIdHotel" id="selectIdHotel" class="form-control selectpicker" data-size="5" data-live-search="true" autofocus required>
                                        <option value="">--{{ __('combobox.select-hotel') }}--</option>
                                        @foreach ($dataHotel as $datax)
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
                                    </select>
                                </div>
                            </div>
                            <div class="col col-md-6 mx-auto">
                                <div class="form-group">
                                    <label for="selectCategory">{{ __('outlet.category') }} </label></label><label style="color:red">* </label>
                                    <select name="selectIdMiceCat" id="selectIdMiceCat" class="form-control selectpicker" data-size="5" data-live-search="true" autofocus required>
                                        <option value="">--{{ __('combobox.select-category') }}--</option>
                                        @foreach ($dataCategory as $datax)
                                            @if(old('selectIdMiceCat')!=null)
                                                @if($datax['system_cd'] == old('selectIdMiceCat'))
                                                    <option value="{{ $datax['system_cd'] }}" selected>{{ $datax['system_value'] }}</option>
                                                @else
                                                <option value="{{ $datax['system_cd'] }}">{{ $datax['system_value'] }}</option>
                                                @endif
                                            @else
                                                <option value="{{ $datax['system_cd'] }}">{{ $datax['system_value'] }}</option>
                                            @endif
                                            
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                        <div class="form-row">
                            <div class="col col-md-12 mx-auto">
                                <div class="form-group">
                                    <label for="description">{{ __('outlet.description') }} </label></label><label style="color:red">* </label>
                                    @if(old('description')!=null)
                                    <textarea class="text-editor" id="description" name="description">{{ old('description') }}</textarea>
                                    @else
                                    <textarea class="text-editor" id="description" name="description"></textarea>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="Maps">Images</label><label style="color:red">* </label>
                                    <input class="form-control form-control-sm" type='file' name="images" id="images" required/>
                                    <label style="font-size: 12px;">{{__('mice.mice-images')}}</label>
                                </div>
                                <img id="icon" src="{{url('/')}}/hotel-images/default.jpg" alt="your image" style="width:100%;height:400px;" />
                            </div>
                        </div>
                        @csrf
                        <br>
                        <div class="d-flex flex-row-reverse">
                            <div class="p-2">
                                <input type="button" class="btn btn-warning float-right" value="{{ __('button.cancel') }}" onclick="window.location.href = '{{ url('mice/mice_category') }}'" >
                            </div>
                            @if((session()->get('role')=='Admin') || Auth::user()->permission('mice-category-create'))
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
 
    $('#selectIdHotel').on('change',function(e){
        console.log(e.target.value);
        var hotel_id =  e.target.value;
       
    });

    $('#description').summernote({
        height: 400
    });

    $("#images").change(function() {
        readURL(this);
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
    
</script>
