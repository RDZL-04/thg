<!DOCTYPE html>
<title>Edit Halls</title>

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
                    <form id="editHall" name="editHall" action="{{ route('hall.edit') }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" id="id" name="id" value="{{ $data['id'] }}">
                    <fieldset>
                        <legend></legend>
                        <div class="form-row">
                            <div class="col col-md-6 mx-auto">
                                <div class="form-group">
                                    <label for="selectHotel">{{ __('outlet.hotel-name') }} </label></label><label style="color:red">* </label>
                                    <input class="form-control" type="text" id="nameHotel" name="nameHotel" value="{{ $data['hotel_name'] }}" disabled>
                                    <input type="hidden" id="selectIdHotel" name="selectIdHotel" value="{{ $data['hotel_id'] }}">
                                </div>
                                <div class="form-group">
                                    <input type="hidden" id="id" name="id" value="{{ $data['id'] }}">
                                    <label for="textName">{{ __('hall.hall-name') }} </label></label><label style="color:red">* </label>
                                    @if(empty($data))
                                    <input type="text" class="form-control" id="txtName" placeholder="Name" name="txtName" value="{{ old('txtName') }}" autofocus required>
                                    @else
                                    <input type="text" class="form-control" id="txtName" placeholder="Name" name="txtName" value="{{ $data['name'] }}" autofocus required>
                                    @endif
                                </div><br>
                                <div class="form-group">
                                    <label for="txtSize">{{ __('hall.hall-size') }} </label></label><label style="color:red">* </label>
                                    @if(empty($data))
                                    <input type="text" class="form-control" id="txtSize" placeholder="{{ __('hall.hall-size') }}" name="txtSize" value="{{ old('txtSize') }}" autofocus required>
                                    @else
                                    <input type="text" class="form-control" id="txtSize" placeholder="{{ __('hall.hall-size') }}" name="txtSize" value="{{ $data['size'] }}" autofocus required>
                                    @endif
                                    <label style="font-size: 12px;">{{__('hall.hall-size-desc')}}
                                </div>
                            </div>
                            <div class="col col-md-6 mx-auto">
                                <div class="form-group">
                                    <label for="selectCategory">{{ __('outlet.category') }} </label></label><label style="color:red">* </label>
                                    <div id="checkCat" style="height: 37px">
                                        <div class="form-check form-check-inline">
                                            @if(count($data['hall_category']) == count($dataMice))
                                                @foreach($data['hall_category'] as $datax) 
                                                <input class="form-check-input" type="checkbox" name="miceCategoryId[]" value="{{ $datax['mice_category_id'] }}" checked>
                                                <label class="form-check-label" for="miceCategoryId">
                                                    {{ $datax['category_name'] }}
                                                </label>&nbsp;&nbsp;
                                                @endforeach
                                            @elseif(count($data['hall_category']) < count($dataMice))
                                            <?php
                                                foreach ($data['hall_category'] as $object) {
                                                    $indexed[$object['mice_category_id']] = $object;
                                                }
                                            ?>
                                                @foreach($dataMice as $datax)
                                                    @if(isset($indexed[$datax['mice_category_id']]))
                                                        <input class="form-check-input" type="checkbox" name="miceCategoryId[]" value="{{ $datax['mice_category_id'] }}" checked>
                                                        <label class="form-check-label" for="miceCategoryId">
                                                            {{ $datax['category_name'] }}
                                                        </label>&nbsp;&nbsp;
                                                    @else 
                                                        <input class="form-check-input" type="checkbox" name="miceCategoryId[]" value="{{ $datax['mice_category_id'] }}">
                                                        <label class="form-check-label" for="miceCategoryId">
                                                            {{ $datax['category_name'] }}
                                                        </label>&nbsp;&nbsp;
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="selectSequence">{{ __('hall.hall-capacity') }} </label><label style="color:red">* </label>
                                    @if(empty($data))
                                    <input type="number" class="form-control" id="txtCapacity" name="txtCapacity" value="{{ old('txtCapacity') }}" required> {{__('hall.hall-capacity-desc')}}
                                    @else
                                    <input type="number" class="form-control" id="txtCapacity" name="txtCapacity" value="{{ $data['capacity'] }}" required> {{__('hall.hall-capacity-desc')}}
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="selectSequence">{{ __('outlet.seq-no') }} </label><label style="color:red">* </label>
                                    @if(empty($data))
                                    <input type="number" class="form-control" id="txtSeqNo" name="txtSeqNo" value="{{ old('txtSeqNo') }}" required>
                                    @else
                                    <input type="number" class="form-control" id="txtSeqNo" name="txtSeqNo" value="{{ $data['seq'] }}" required>
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
                                    @elseif(!empty($data['descriptions']))
                                    <textarea class="text-editor" id="description" name="description">{{ $data['descriptions'] }}</textarea>
                                    @endif
                                </div>
                            </div>
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <?php
                                        $layout = explode('/',$data['layout']);
                                    ?>
                                    <label for="layout">Layout</label><label style="color:red">* </label><br>
                                    <a href="{{ url($data['layout']) }}" target="_blank"> {{ $layout[2] }}</a>
                                    @if(empty($data))
                                    <input class="form-control form-control-sm" type='file' name="layout" id="layout" value="{{ url(old('layout')) }}" required/>
                                    @else
                                    <input type='hidden' name="oldLayout" id="oldLayout" value="{{$data['layout']}}"/>
                                    <input class="form-control form-control-sm" type='file' name="layout" id="layout" value="{{ url($data['layout']) }}" />
                                    @endif
                                    <label style="font-size: 12px;">{{__('hall.hall-layout-desc')}}</label><br>
                                </div>
                            </div>
                            @if($data['mice_offers'] != null)
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <?php
                                        $mice_offers = explode('/',$data['mice_offers']);
                                    ?>
                                    <div class="row">
                                        <div class="col col-md-6">
                                        <label for="mice_Offers">Offers</label><br>
                                        <a id="linkMice" href="{{ url($data['mice_offers']) }}" target="_blank"> {{ $mice_offers[2] }}</a>
                                        </div>
                                        <div class="col col-md-6">
                                        <label for="mice_Offers">&nbsp;</label><br>
                                        <a href="#" class="button delete-offers"><i class="fas fa-minus-circle"></i>Remove Offers</a>
                                        </div>
                                    </div>
                                    <input type='hidden' name="oldMiceOffers" id="oldMiceOffers" value="{{$data['mice_offers']}}"/>
                                    <input class="form-control form-control-sm" type='file' name="mice_offers" id="mice_offers"/>
                                    <label style="font-size: 12px;">{{__('hall.hall-layout-desc')}}</label><br>
                                </div>
                            </div>
                            @else
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="mice_Offers">Offers</label><br><br>
                                    <input class="form-control form-control-sm" type='file' name="mice_offers" id="mice_offers"/>
                                    <label style="font-size: 12px;">{{__('hall.hall-layout-desc')}}</label><br>
                                </div>
                            </div>
                            @endif
                        </div>
                        @csrf
                        <br>
                        <div class="d-flex flex-row-reverse">
                            <div class="p-2">
                                <input type="button" class="btn btn-warning float-right" value="{{ __('button.cancel') }}" onclick="window.location.href = '{{ url('mice/hall') }}'" >
                            </div>
                            @if((session()->get('role')=='Admin') || Auth::user()->permission('mice-hall-edit'))
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
<div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                    
                    <br>
                    <fieldset>
                    <legend>{{__('hall.images-hall')}}</legend>
                    @if ($data['hall_images'] == null)
                    {{__('hotel.no-images')}}
                    @else
                    @foreach (array_chunk($data['hall_images'],3) as $img)
                    <div class="row">
                        @foreach ($img as $data_img)        
                        <div class="col-sm-4">
                            <div class="card">
                            <div class="card-body">
                                <center>
                                <img  src=" {{url($data_img['filename'])}}" alt="Card image cap text-center h-100" style="width:250px;height:250px;align:center">
                                </center>
                                #{{ $data_img['seq']}}
                                @if((session()->get('role')=='Admin') || Auth::user()->permission('mice-hall-create') || Auth::user()->permission('mice-hall-edit'))
                                <p class="card-text"><small class="text-muted"> <a href="{{ url('mice/get_edit_hall_images?data=') }}{{json_encode($data_img)}}">{{ __('button.edit') }}</a>
                                &nbsp;
                                <a href="{{ url('mice/delete_hall_images') }}/{{$data_img['id']}}" class="button delete-confirm">{{ __('button.delete') }}</a>
                                </p></small></p>
                                @endif
                            </div>
                            </div>
                        </div>
                        @endforeach
                        </div>
                        <br>
                    @endforeach
                    @endif
                    @if((session()->get('role')=='Admin') || Auth::user()->permission('mice-hall-create') || Auth::user()->permission('mice-hall-edit'))
                    <form id="addImage" name="addimage" action="{{ route('images_hall') }}" method="get">
                    <input type="hidden" class="form-control" id="txtId" placeholder="Id" name="txtId" value="{{ $data['id'] }}">
                    <button type="submit" class="btn btn-primary float-right" >{{ __('button.add-image') }}</button>    
                    </form>
                    @endif
                </div>
                <br>
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

    $('.delete-offers').on('click', function (event) {
    event.preventDefault();
        swal({
            title: 'Are you sure?',
            text: 'Do you want remove Offers ?',
            icon: 'warning',
            buttons: ["Cancel", "Yes!"],
        }).then(function(value) {
            if (value) {
                $('#oldMiceOffers').attr('value', '');
                $('#mice_offers').attr('value', '');
                $('#linkMice').removeAttr('href');
                $('#linkMice').hide();
            }
        });
    });
    
</script>
