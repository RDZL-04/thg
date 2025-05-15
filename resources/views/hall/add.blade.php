<!DOCTYPE html>
<title>Add Halls for Hotel</title>

    <style>
        .text-editor {
            width: 100%;
            height: 200%;
        }
        .dropdown-toggle {
            height: 40;
        }
        .checkbox, .radio {
            display: block;
            margin-bottom: 20px;
            margin-top: 20px;
            position: relative;
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
		{{ $judul }} Halls
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
                    <form id="addHall" name="addHall" action="{{ route('hall.add') }}" method="post" enctype="multipart/form-data">
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
                                                    <option value="{{ $datax['id'] }}" selected>{{ $datax['hotel_name'] }}</option>
                                                @else
                                                <option value="{{ $datax['id'] }}">{{ $datax['hotel_name'] }}</option>
                                                @endif
                                            @else
                                                <option value="{{ $datax['id'] }}">{{ $datax['hotel_name'] }}</option>
                                            @endif
                                            
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="textName">{{ __('hall.hall-name') }} </label></label><label style="color:red">* </label>
                                    <input type="text" class="form-control" id="txtName" placeholder="Name" name="txtName" value="{{ old('txtName') }}" autofocus required>
                                    <label style="font-size: 12px;">&nbsp;</label>
                                </div>
                                <div class="form-group">
                                    <label for="txtSize">{{ __('hall.hall-size') }} </label></label><label style="color:red">* </label>
                                    <input type="text" class="form-control" id="txtSize" placeholder="{{ __('hall.hall-size') }}" name="txtSize" value="{{ old('txtSize') }}" autofocus required>
                                    <label style="font-size: 12px;">{{__('hall.hall-size-desc')}}</label>
                                </div>
                            </div>
                            <div class="col col-md-6 mx-auto">
                                <div class="form-group">
                                    <input type="hidden" name="theCat">
                                    <label for="selectCategory">{{ __('outlet.category') }} </label></label><label style="color:red">* </label>
                                    <div id="checkCat" style="height: 37px"> 
                                        <div class="form-check form-check-inline">
                                        {{-- Load CHECKBOX jika sudah pilih Hotel --}}
                                        @if(!empty(old('theCat')))
                                            @if(count(old('theCat')) == count(old('miceCategoryId')))
                                                @foreach(old('theCat') as $datax) 
                                                <input class="form-check-input" type="checkbox" name="miceCategoryId[]" value="{{ $datax['mice_category_id'] }}" checked>
                                                <label class="form-check-label" for="miceCategoryId">
                                                    {{ $datax['category_name'] }}
                                                </label>
                                                @endforeach
                                            @elseif(count(old('miceCategoryId')) == 1)    
                                                @foreach(old('theCat') as $datax)
                                                    @foreach(old('miceCategoryId') as $dataz) 
                                                        @if($datax['mice_category_id'] == $dataz)
                                                        <input class="form-check-input" type="checkbox" name="miceCategoryId[]" value="{{ $datax['mice_category_id'] }}" checked>
                                                        <label class="form-check-label" for="miceCategoryId">
                                                            {{ $datax['category_name'] }}
                                                        </label>&nbsp;&nbsp;
                                                        @else 
                                                        <input class="form-check-input" type="checkbox" name="miceCategoryId[]" value="{{ $datax['mice_category_id'] }}" >
                                                        <label class="form-check-label" for="miceCategoryId">
                                                            {{ $datax['category_name'] }}
                                                        </label>&nbsp;&nbsp;
                                                        @break
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                            @else
                                                @foreach(old('theCat') as $datax)
                                                    @foreach(old('miceCategoryId') as $dataz) 
                                                        @if($datax['mice_category_id'] == $dataz)
                                                        <input class="form-check-input" type="checkbox" name="miceCategoryId[]" value="{{ $datax['mice_category_id'] }}" checked>
                                                        <label class="form-check-label" for="miceCategoryId">
                                                            {{ $datax['category_name'] }}
                                                        </label>&nbsp;&nbsp;
                                                        @elseif($loop->parent->last)
                                                        <input class="form-check-input" type="checkbox" name="miceCategoryId[]" value="{{ $datax['mice_category_id'] }}" >
                                                        <label class="form-check-label" for="miceCategoryId">
                                                            {{ $datax['category_name'] }}
                                                        </label>&nbsp;&nbsp;
                                                        @break
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                            @endif
                                        @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="selectSequence">{{ __('hall.hall-capacity') }} </label><label style="color:red">* </label>
                                    <input type="number" class="form-control" id="txtCapacity" name="txtCapacity" value="{{ old('txtCapacity') }}" required>
                                    <label style="font-size: 12px;">{{__('hall.hall-capacity-desc')}}</label>
                                </div>
                                <div class="form-group">
                                    <label for="selectSequence">{{ __('outlet.seq-no') }} </label><label style="color:red">* </label>
                                    @if(!empty(old('txtSeqNo')))
                                    <input type="number" class="form-control" id="txtSeqNo" name="txtSeqNo" value="{{ old('txtSeqNo') }}" required>
                                    @elseif(!empty($seqNo))
                                    <input type="number" class="form-control" id="txtSeqNo" name="txtSeqNo" value="{{ $seqNo }}" required>
                                    @endif
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
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="Maps">Layout</label><label style="color:red">* </label>
                                    <input class="form-control form-control-sm" type='file' name="layout" id="layout" required/>
                                    <label style="font-size: 12px;">{{__('hall.hall-layout-desc')}}</label>
                                </div>
                            </div>
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="Offers">Offers</label>
                                    <input class="form-control form-control-sm" type='file' name="mice_offers" id="mice_offers"/>
                                    <label style="font-size: 12px;">{{__('hall.hall-layout-desc')}}</label>
                                </div>
                            </div>
                        </div>
                        @csrf
                        <br>
                        <div class="d-flex flex-row-reverse">
                            <div class="p-2">
                                <input type="button" class="btn btn-warning float-right" value="{{ __('button.cancel') }}" onclick="window.location.href = '{{ url('mice/hall') }}'" >
                            </div>
                            @if((session()->get('role')=='Admin') || Auth::user()->permission('mice-hall-create'))
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
        var url = 'get_hotel_mice_msystem/' + hotel_id;

        $.get(url, function (data)
        {
            console.log(data);
            $('#checkCat').empty();
            $.each(data, function(index, data)
            {
                $('#checkCat').append(
                '<div class="form-check form-check-inline">' +
                    '<input class="form-check-input" type="checkbox" name="miceCategoryId[]" value="'+data.mice_category_id+'" id="chk'+data.mice_category_id+'">' +
                    '<label class="form-check-label" for="defaultCheck1">' +
                        data.category_name +
                    '</label>' +
                '</div>' 
                );
                
            });
        });
    });

    $('#description').summernote({
        height: 400
    });
    
</script>
