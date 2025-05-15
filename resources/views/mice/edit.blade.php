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
        .note-editable ul{
            list-style: disc !important;
            list-style-position: inside !important;
        }

        .note-editable ol {
            list-style: decimal !important;
            list-style-position: inside !important;
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
                    <form id="editMiceCategory" name="editMiceCategory" action="{{ route('mice_category.edit') }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" id="id" name="id" value="{{ $dataCategory['id'] }}">
                    <fieldset>
                        <legend></legend>
                        <div class="form-row">
                            <div class="col col-md-6 mx-auto">
                                <div class="form-group">
                                    <label for="selectHotel">{{ __('outlet.hotel-name') }} </label></label><label style="color:red">* </label>
                                    <input class="form-control" type="text" id="nameHotel" name="nameHotel" value="{{ $dataCategory['hotel_name'] }}" disabled>
                                    <input type="hidden" id="selectIdHotel" name="selectIdHotel" value="{{ $dataCategory['hotel_id'] }}">
                                </div>
                            </div>
                            <div class="col col-md-6 mx-auto">
                                <div class="form-group">
                                    <label for="selectCategory">{{ __('outlet.category') }} </label></label><label style="color:red">* </label>
                                    <input class="form-control" type="text" id="nameCat" name="nameCat" value="{{ $dataCategory['category_name'] }}" disabled>
                                    <input type="hidden" id="selectIdMiceCat" name="selectIdMiceCat" value="{{ $dataCategory['category_id'] }}">
                                </div>
                            </div>
                            
                        </div>
                        <div class="form-row">
                            <div class="col col-md-12 mx-auto">
                                <div class="form-group">
                                    <label for="description">{{ __('outlet.description') }} </label></label><label style="color:red">* </label>
                                    @if(old('description')!=null)
                                    <textarea class="text-editor" id="description" name="description">{{ old('description') }}</textarea>
                                    @elseif(!empty($dataCategory['descriptions']))
                                    <textarea class="text-editor" id="description" name="description">{{ $dataCategory['descriptions'] }}</textarea>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="Maps">Images</label><label style="color:red">* </label>
                                    <input type='hidden' name="oldImages" id="Oldimages" value="{{ $dataCategory['images'] }}"/>
                                    <input class="form-control form-control-sm" type='file' name="images" id="images"/>
                                    <label style="font-size: 12px;">{{__('mice.mice-images')}}</label>
                                </div>
                                <img id="icon" src="{{url('/')}}/{{ $dataCategory['images'] }}" alt="your image" style="width:100%;height:400px;" />
                            </div>
                        </div>
                        @csrf
                        <br>
                        <div class="d-flex flex-row-reverse">
                            <div class="p-2">
                                <input type="button" class="btn btn-warning float-right" value="{{ __('button.cancel') }}" onclick="window.location.href = '{{ url('mice/mice_category') }}'" >
                            </div>
                            @if((session()->get('role')=='Admin') || Auth::user()->permission('mice-category-edit'))
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
