<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $judul }}</title>
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"> -->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.bundle.min.js"></script> -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">
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
                    <form id="addAttraction" name="addAttraction" action="{{ route('attraction.save') }}" method="post" enctype="multipart/form-data">
                    <fieldset>
                    <legend>{{ $judul }} {{$name_hotel}}:</legend>
                        <div class="form-row">
                        @if(!empty($data))
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="txtName">{{__('attraction.name')}}</label><label style="color:red">* </label>
                                    <input type="hidden" class="form-control" id="txtIdHotel" placeholder="IdHotel" name="txtIdHotel" value="{{$data['hotel_id']}}">
                                    <input type="hidden" class="form-control" id="txtId" placeholder="Id" name="txtId" value="{{$data['id']}}">
                                    <input type="text" class="form-control" id="txtName" placeholder="{{__('attraction.name')}}" name="txtName" value="{{$data['attraction_nm']}}" autofocus required>
                                </div>
                                <div class="form-group">
                                    <label for="txtName">{{__('attraction.category')}}</label><label style="color:red">* </label>
                                    <div class="row">
                                        <div class="col-8">
                                        <select name="selectCategory" id="selectCategory" onchange="changeFunc();" class="form-control select2" >
                                            @if($data['category_id']== null)
                                            <option value="">--{{ __('attraction.select_category') }}--</option>
                                            @endif
                                            @if($data_category != null)
                                                @foreach($data_category as $category)
                                                    <?php
                                                        $spliter = explode(';',$category['system_value']);
                                                        $categoryName = $spliter[0];
                                                        $icon_type = $spliter[1];
                                                        $icon_src = $spliter[2];
                                                    ?>
                                                    @if($data['category_id'] != null)
                                                        @if($data['category_id'] == $category['system_cd'])
                                                        <option value ="{{$category['system_cd']}}" data-icon="{{$icon_type}}" data-src="{{$icon_src}}" selected="selected">
                                                                {{ $categoryName}}
                                                        </option>
                                                        @else
                                                        <option value ="{{$category['system_cd']}}" data-icon="{{$icon_type}}" data-src="{{$icon_src}}">
                                                            {{ $categoryName}}
                                                        </option>
                                                        @endif                                        
                                                    @else
                                                        <option value ="{{$category['system_cd']}}" data-icon="{{$icon_type}}" data-src="{{$icon_src}}">
                                                                {{ $categoryName}}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                                
                                            </select>
                                        </div>
                                        @if($data['category_id'] != null)
                                            @if($data_category != null)
                                                    @foreach($data_category as $category)
                                                        @if((int)$data['category_id'] == (int)$category['system_cd'])
                                                        <?php
                                                            $spliter = explode(';',$category['system_value']);
                                                            $categoryName = $spliter[0];
                                                            $icon_type = $spliter[1];
                                                            $icon_src = $spliter[2];
                                                        ?>
                                                        <div class="col-4" id="iconCategory" style="font-size:30px;">
                                                            @if($icon_type == 'Ionicons')
                                                            <ion-icon name="{{ $icon_src }}" style="font-size:30px;"></ion-icon>
                                                            @elseif($icon_type == 'MaterialIcons')
                                                            <span class="material-icons" style="font-size:30px;">{{ $icon_src }}</span>
                                                            @endif
                                                        </div>
                                                        @endif
                                                    @endforeach
                                            @endif
                                        @else
                                        <div class="col-4" id="iconCategory" style="font-size:30px;">
                                        
                                        </div>
                                        @endif
                                    </div>
                                    
                                </div>
                                <div class="form-group">
                                    <label for="txtDistance">{{__('attraction.distance')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" id="txtDistance" placeholder="{{__('attraction.example_distance')}}" name="txtDistance" value="{{ $data['distance'] }}" autofocus required>
                                </div>
                            </div>
                        @else
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="txtName">{{__('attraction.name')}}</label><label style="color:red">* </label>
                                    <input type="hidden" class="form-control" id="txtIdHotel" placeholder="IdHotel" name="txtIdHotel" value="{{$id_hotel}}">
                                    <input type="text" class="form-control" id="txtName" placeholder="{{__('attraction.name')}}" name="txtName" value="{{ old('txtName') }}" autofocus required>
                                </div>
                                <div class="form-group">
                                    <label for="txtName">{{__('attraction.category')}}</label><label style="color:red">* </label>
                                    <div class="row">
                                        <div class="col-8">
                                            <select name="selectCategory" id="selectCategory" onchange="changeFunc();" class="form-control select2" >
                                            <option value="" data-icon="" data-src="">--{{ __('attraction.select_category') }}--</option>
                                            @if($data_category != null)
                                                @foreach($data_category as $category)
                                                <?php
                                                    $spliter = explode(';',$category['system_value']);
                                                    $categoryName = $spliter[0];
                                                    $icon_type = $spliter[1];
                                                    $icon_src = $spliter[2];
                                                ?>
                                                <option value ="{{$category['system_cd']}}" data-icon="{{$icon_type}}" data-src="{{$icon_src}}" onclick="selectIcon()">
                                                        {{ $categoryName}}
                                                </option>
                                                @endforeach
                                            @endif
                                                
                                            </select>
                                        </div>
                                        <div class="col-4" id="iconCategory" style="font-size:30px;">
                                        </div>
                                    </div>
                                    
                                    
                                    
                                </div>
                                <div class="form-group">
                                    <label for="txtDistance">{{__('attraction.distance')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" id="txtDistance" placeholder="{{__('attraction.example_distance')}}" name="txtDistance" value="{{ old('txtDistance') }}" autofocus required>
                                </div>
                            </div>
                        @endif
                        <div class="col col-md-6">
                                <div class="form-group">
                                
                                </div>
                                <div class="form-group">
                                
                                </div>
                                <div class="form-group">
                                    
                                </div>
                            </div>
                            
                            
                        </div>
                        @csrf
                        <div class="d-flex flex-row-reverse">
                            <div class="p-2">
                            <input type="hidden" class="form-control" name="created_by" id="created_by" value="{{session()->get('full_name')}}">
                            @if(!empty($data))
                            <input type="button" class="btn btn-warning float-right" value="{{ __('button.cancel') }}" onclick="window.location.href = '{{ url('get_edit_hotel').'/'. $data['hotel_id']}}'" >
                            @else
                            <input type="button" class="btn btn-warning float-right" value="{{ __('button.cancel') }}" onclick="window.location.href = '{{ url('get_edit_hotel').'/'. $id_hotel}}'" >
                            @endif
                            
                            </div>
                           <div class="p-2" style='font-size: 30px;'>
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
<script type="module" src="https://unpkg.com/ionicons@5.4.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule="" src="https://unpkg.com/ionicons@5.4.0/dist/ionicons/ionicons.js"></script>
<script>
$(document).ready(function() {
	console.log('ada');
});
function changeFunc() {
    var selectBox = document.getElementById("selectCategory");
    var selectedValue = selectBox.options[selectBox.selectedIndex];
    var iconType = selectedValue.dataset.icon;
    var iconSrc = selectedValue.dataset.src;
    console.log('icon type'+iconType);
    console.log(iconSrc)
    if(iconType === ""){
        document.getElementById("iconCategory").innerHTML = "";
    }
    else if( iconType === 'Ionicons'){
        document.getElementById("iconCategory").innerHTML = "<ion-icon name='"+iconSrc+"' style='font-size: 30px;'></ion-icon>";
    }else if( iconType === 'MaterialIcons'){
        document.getElementById("iconCategory").innerHTML = "<span class='material-icons' style='font-size: 30px;'>"+iconSrc+"</span>";
    }
   }

function selectIcon(){
    console.log('ada');
}
function validate_input(e, oInput, _validFileExtensions) 
{
	//var self = this;
	
}
</script>