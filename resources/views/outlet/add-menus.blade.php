
<title>{{ $judul }}</title>

    <style>
        th, td {
            white-space: nowrap;
        }
    
        div.dataTables_wrapper {
            width: 100%;
            margin: 0 auto;
        }
        .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 40rem; }
        .toggle.ios .toggle-handle { border-radius: 40rem; }
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
        {{-- @include('/flash-message') --}}

        @yield('content')
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg"> @include('/flash-message')
            <br>
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                    <form id="addMenu" name="addMenu" action="{{ route('outlet_menu.edit') }}" method="post" enctype="multipart/form-data">
                    <fieldset>
                <legend>{{ __('outlet.menu') }} {{ $rest_name }}</legend>
                
                        <div class="form-row">
                            <div class="col col-md-4">
                                
                                <div class="form-group">
                                    <label for="txtName">{{ __('outlet.menu') }}</label><label style="color:red">* </label>
                                    <input type="hidden" class="form-control" id="txtOutletId" placeholder="IdOutlet" name="txtOutletId" value="{{ $txtOutletId }}">
                                    <input type="hidden" class="form-control" id="txtRestName" name="txtRestName" value="{{ $rest_name }}">
                                    @if (empty($data))
                                    <input type="hidden" class="form-control" id="txtJenis" name="txtJenis" value="new">
                                    @if (!empty($data_old))
                                        <input type="text" class="form-control" id="txtName" placeholder="Name" name="txtName" value="{{ $data_old['txtName'] }}" autofocus required>
                                    @elseif(old('txtName') != null)
                                        <input type="text" class="form-control" id="txtName" placeholder="Name" name="txtName" value="{{ old('txtName') }}"  autofocus required>
                                    @else
                                    <input type="text" class="form-control" id="txtName" placeholder="Name" name="txtName" autofocus required>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="txtDescription">{{ __('outlet.description') }}</label><label style="color:red">* </label>
                                    @if (!empty($data_old))
                                        <textarea class="form-control" id="txtDescription" name="txtDescription" rows="3" placeholder="Enter Description" required>{{ $data_old['txtDescription'] }}</textarea>
                                    @elseif(old('txtDescription') != null)
                                        <textarea class="form-control" id="txtDescription" name="txtDescription" rows="3" placeholder="Enter Description" required>{{ old('txtDescription') }}</textarea>
                                    @else
                                        <textarea class="form-control" id="txtDescription" name="txtDescription" rows="3" placeholder="Enter Description" required></textarea>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="txtPrice">{{ __('outlet.price') }}</label><label style="color:red">* </label>
                                    @if (!empty($data_old))
                                        <input type="number" class="form-control" id="txtPrice" name="txtPrice"placeholder="Price" onkeypress='validate(event)' value="{{ $data_old['txtPrice'] }}" required>
                                    @elseif(old('txtPrice') != null)
                                        <input type="number" class="form-control" id="txtPrice" name="txtPrice"placeholder="Price" onkeypress='validate(event)' value="{{ old('txtPrice') }}" required>
                                    @else
                                    <input type="number" class="form-control" id="txtPrice" name="txtPrice"placeholder="Price" onkeypress='validate(event)' required>
                                    @endif
                                </div>
                                
                                <div class="form-group">
                                    <label for="selectStatus">{{ __('outlet.status') }}</label><label style="color:red">* </label>
                                    <select name="selectStatus" class="form-control select2" required>
                                    
                                        @if (!empty($data_old))
                                            @if($data_old['selectStatus'] == '1')
                                                <option value ="1" selected>{{ __('outlet.active') }}</option>
                                            @elseif($data_old['selectStatus'] == '0')
                                                <option value ="0" selected>{{ __('outlet.not-active') }}</option>
                                            @endif
                                        @elseif(old('selectStatus') != null)
                                            @if(old('selectStatus') == '1')
                                                <option value ="1" selected>{{ __('outlet.active') }}</option>
                                            @elseif(old('selectStatus') == '0')
                                                <option value ="0" selected>{{ __('outlet.not-active') }}</option>
                                            @endif
                                        @else
                                            <option value ="1"> {{ __('outlet.active') }}</option>
                                            <option value ="0">{{ __('outlet.not-active') }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="selectCategory">{{ __('outlet.category') }}</label><label style="color:red">* </label>
                                        <select name="selectCategory" class="form-control select2" required>
                                            <option value="">--{{ __('combobox.select-category') }}--</option>
                                            @if(!empty($data_category))
                                            @foreach ($data_category as $datax)
                                                @if (!empty($data_old))
                                                    @if($data_old['selectCategory'] == $datax['id'])
                                                        <option value="{{ $datax['id'] }}" selected>{{ $datax['name'] }}</option>
                                                    @else
                                                        <option value="{{ $datax['id'] }}">{{ $datax['name'] }}</option>
                                                    @endif
                                                @elseif(old('selectCategory') != null)
                                                    @if(old('selectCategory') == $datax['id'])
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
                                    <label for="txtName">{{ __('outlet.seq-no') }}</label><label style="color:red">* </label>
                                    @if (!empty($data_old))
                                        <input type="number" class="form-control" id="txtSeqNo" name="txtSeqNo" value="{{ $data_old['txtSeqNo'] }}" required>
                                    @elseif(old('txtSeqNo'))
                                        <input type="number" class="form-control" id="txtSeqNo" name="txtSeqNo" value="{{ old('txtSeqNo') }}" required>
                                    @elseif(!empty($new_seq_no))
                                        <input type="number" class="form-control" id="txtSeqNo" name="txtSeqNo" value="{{ $new_seq_no }}" required>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="txtIsPromo">Is Promo</label><br>
                                    @if (!empty($data_old))
                                        @if(!empty($data_old['chkIsPromo']) && $data_old['chkIsPromo'] == 'on')
                                        <input type="checkbox" id="chkIsPromo" class="form-control" name="chkIsPromo" data-toggle="toggle" data-style="ios" data-offstyle="danger"  checked>
                                        @else
                                        <input type="checkbox" id="chkIsPromo" class="form-control" name="chkIsPromo" data-toggle="toggle" data-style="ios" data-offstyle="danger" >
                                        @endif
                                    @elseif(old('chkIsPromo'))
                                        @if(old('chkIsPromo') == 'on')
                                        <input type="checkbox" id="chkIsPromo" name="chkIsPromo" class="form-control" type="checkbox" data-toggle="toggle" data-style="ios" data-offstyle="danger" checked>
                                        @else
                                        <input type="checkbox" id="chkIsPromo" name="chkIsPromo" class="form-control" type="checkbox" data-toggle="toggle" data-style="ios" data-offstyle="danger" >
                                        @endif
                                    @else
                                        <input type="checkbox" id="chkIsPromo" class="form-control" name="chkIsPromo" data-toggle="toggle" data-style="ios" data-offstyle="danger">
                                    @endif
                                    {{-- else if empty $data --}}
                                    @else 
                                    <input type="hidden" class="form-control" name="created_by" id="created_by" value="{{ session()->get('full_name') }}">
                                    <input type="hidden" class="form-control" id="txtId" placeholder="Id" name="txtId" value="{{ $data['id']  }}">
                                    <input type="text" class="form-control" id="txtName" placeholder="Name" name="txtName" value="{{ $data['name']  }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="txtDescription">{{ __('outlet.description') }}</label><label style="color:red">* </label>
                                    <textarea class="form-control" id="txtDescription" name="txtDescription" rows="3" placeholder="Enter Description" required>{{ $data['description'] }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="txtPrice">{{ __('outlet.price') }}</label><label style="color:red">* </label>
                                    <input type="number" class="form-control" id="txtPrice" name="txtPrice"placeholder="Price" onkeypress='validate(event)' value="{{ (int)$data['price'] }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="selectStatus">{{ __('outlet.status') }}</label><label style="color:red">* </label>
                                        <select name="selectStatus" class="form-control select2" required>
                                        @if(is_null($data['menu_sts']))
                                        <option value ="1"> {{ __('outlet.active') }}</option>
                                        <option value ="0">{{ __('outlet.not-active') }}</option>
                                        @else
                                            @if($data['menu_sts'] == 1)
                                            <option value="{{ $data['menu_sts']  }}">{{ __('outlet.active') }}</option>
                                            <option value ="0">{{ __('outlet.not-active') }}</option>
                                            @else
                                            <option value="{{ $data['menu_sts']  }}">{{ __('outlet.not-active') }}</option>
                                            <option value ="1"> {{ __('outlet.active') }}</option>
                                            @endif                                        
                                        @endif
                                        </select>
                                </div>
                                <div class="form-group">
                                    <label for="selectCategory">{{ __('outlet.category') }}</label><label style="color:red">* </label>
                                        <select name="selectCategory" class="form-control select2" required>
                                            <option value="">--{{ __('combobox.select-category') }}--</option>
                                            @foreach ($data_category as $datax)
                                                @if($data['menu_cat_id']!=null)
                                                    @if($data['menu_cat_id'] == $datax['id'])
                                                        <option value="{{ $datax['id'] }}" selected> {{ $datax['name'] }} </option>
                                                    @else
                                                        <option value="{{ $datax['id'] }}">{{ $datax['name'] }}</option>
                                                    @endif
                                                @else
                                                    <option value="{{ $datax['id'] }}">{{ $datax['name'] }}</option>
                                                @endif
                                            @endforeach       
                                        </select>
                                </div>
                                <div class="form-group">
                                    <label for="txtName">{{ __('outlet.seq-no') }}</label><label style="color:red">* </label>
                                    <input type="number" class="form-control" id="txtSeqNo" name="txtSeqNo" value="{{ $data['seq_no'] }}" required>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" name="txtIsPromo" id="txtIsPromo" type="hidden" value="{{ $data['is_promo'] }}">
                                    <label for="txtIsPromo">Promo</label><br>
                                    @if($data['is_promo'] == 1)
                                    <input class="form-control" name="chkIsPromo" id="chkIsPromo" type="checkbox" data-toggle="toggle" data-style="ios" data-offstyle="danger" checked>
                                    @elseif($data['is_promo'] == 0)
                                    <input class="form-control" name="chkIsPromo" id="chkIsPromo" type="checkbox" data-toggle="toggle" data-style="ios" data-offstyle="danger">
                                    @endif
                                    @endif  {{-- END IF @data emtpy --}}
                                </div>
                            </div>
                            <div class="col col-sm-1">
                                &nbsp;
                            </div>
                            <div class="col col-md-6">
                                <div class="form-group">
                                <label for="images">{{ __('outlet.images') }}</label><label style="color:red"></label>
                                @if (empty($data))
                                    <input class="form-control form-control-sm" type='file' name="images" id="images"/>
                                    <label style="font-size: 12px;">{{ __('outlet.desc-image') }}</label>
                                    <img id="icon" src="{{url('/')}}/hotel-images/default.jpg" alt="your image" style="width:300px;height:300px;" />
                                @else
                                    @if($data['images'] != null)
                                        <input type='hidden' name="oldImages" id="Oldimages" value="{{$data['images']}}"/>
                                        <input class="form-control form-control-sm" type='file' name="images" id="images" value="{{url($data['images'])}}" />
                                        <label style="font-size: 12px;">{{ __('outlet.desc-image') }}</label>
                                        <img id="icon" src="{{url($data['images'])}}" alt="your image" style="width:300px;height:300px;" />
                                    @else
                                    <input class="form-control form-control-sm" type='file' name="images" id="images"/>
                                        <label style="font-size: 12px;">{{ __('outlet.desc-image') }}</label>
                                        <img id="icon" src="{{url('/')}}/hotel-images/default.jpg" alt="your image" style="width:300px;height:300px;" />
                                    @endif
                                    
                                @endif
                                </div>
                            </div>

                        </div>
                        
                        @csrf
                        <div class="d-flex flex-row-reverse">
                            <div class="p-2">
                                <input type="button" class="btn btn-warning float-right" value="{{ __('button.cancel') }}" onclick="window.location.href = '{{ url('master/fnboutlet/get_edit_outlet/').'/'. $txtOutletId}}'" >
                            </div>
                            @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-menu-create') || Auth::user()->permission('outlet-menu-edit'))
                            <div class="p-2">
                                <button type="submit" id="btnSubmit" class="btn btn-primary float-right" >{{ __('button.save') }}</button>
                            </div>
                            @endif
                        </div>

                    </fieldset>
                    </form>
                </div>
            </div>
        </div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                        <br>
                        <div class="d-flex ">
                            <div class="p-2">
                                <fieldset>
                                    <legend>{{ __('outlet.side-dish') }}</legend>
                                </fieldset>
                            </div>
                          
                            @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-menu-create') || Auth::user()->permission('outlet-menu-edit'))

                                @if( (!empty($data) && $data['menu_sts'] == '1' && $data_issidedish != null))
                                <div class="p-2" style=" margin-left: auto;">
                                    <input type="button" class="btn btn-success float-right" value="{{ __('button.add-side-dish') }}" data-toggle="modal" data-target="#sidedishModal">
                                </div>
                                @endif
                           
                            @endif
                        </div>
                        <table class="table table-bordered" id="sidedishTable">
                            <thead>
                                <tr class="bg-gray-100" style="text-align:center" valign="middle" >
                                    <th class="px-2">{{ __('outlet.no') }}</th>
                                    <th class="px-4">{{ __('outlet.action') }}</th>
                                    <th class="px-4 w-10">{{ __('user.name') }}</th>
                                    <th class="px-4 w-10">{{ __('outlet.sidedish-condiment') }}</th>
                                    <th class="px-4">{{ __('outlet.created-by') }}</th>
                                    <th class="px-4">{{ __('outlet.created-at') }}</th>
                                    {{-- <th class="px-4">{{ __('outlet.updated-by') }}</th> --}}
                                    {{-- <th class="px-4">{{ __('outlet.updated-at') }}</th> --}}
                                </tr>
                            </thead>
                            <tbody id="tableBodySidedish">
                                @if($data_sidedish != null)
                                    @foreach ($data_sidedish as $datas)
                                        <tr id="{{ $datas['id'] }}">
                                            <td class="text-center">{{$loop->iteration}}</td>
                                            <td class="px-4 py-2 text-center">
                                                {{-- <a href="{{ url('master/fnboutlet/menu/view_sidedish').'/'.$data['sdish_id'] }}"><i class="fas fa-pen"></i></a> --}}
                                                 {{-- &nbsp; --}}
                                                 @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-menu-create') || Auth::user()->permission('outlet-menu-edit'))
                                                 <a href="{{ url('master/fnboutlet/menu/delete_sidedish').'/'.$txtOutletId.'/'.$data['id'].'/'.$datas['sdish_id'] }}" class="delete-confirm"><i class="fas fa-trash"></i></a>
                                                 @endif
                                            </td>
                                            <td>{{$datas['name']}}</td>
                                            <td>
                                                @if($datas['is_sidedish'] == 1)
                                                    {{  __('outlet.sidedish') }}
                                                @else
                                                    {{  __('outlet.condiment') }}
                                                @endif
                                            
                                            </td>
                                            <td>{{ $datas['created_by'] }}</td>
                                            <td>{{ $datas['created_at'] }}</td>
                                            {{-- <td>{{ $datas['updated_by'] }}</td> --}}
                                            {{-- <td >{{ $datas['updated_at'] }}</td> --}}
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <br>
                        <br>
                    </div>
                </div>
            </div>
        </div>

        

    <!-- Modal -->
    <div class="modal fade" id="sidedishModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">{{ __('button.add') }} {{ __('outlet.side-dish') }}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form>
                <div class="form-group">
                <input type="hidden" class="form-control" id="id_system">
                <label for="menuCategoryName" class="col-form-label">Menu {{ __('outlet.category') }}</label><label style="color:red">* </label>
                <select name="selectMenuCategory" id="selectMenuCategory" class="form-control selectpicker" data-size="5" data-live-search="true" data-container="body">
                    @if($data_issidedish != null)
                        <option value =""> --{{ __('combobox.select-category') }}--</option>
                        @foreach ($data_category as $dataz)
                            <option value ="{{ $dataz['id'] }}"> {{ $dataz['name'] }}</option>
                        @endforeach
                    @else
                        <option value =""> --{{ __('combobox.select-category') }}--</option>
                    @endif
                </select>
                </div>
                <div class="form-group">
                  <input type="hidden" class="form-control" id="id_system">
                  <label for="menuName" class="col-form-label">{{ __('outlet.menu') }} {{ __('outlet.name') }}</label><label style="color:red">* </label>
                  <select name="selectSidedish" id="selectSidedish" class="form-control md-6 selectpicker" data-size="5" data-live-search="true" data-container="body">
                      {{-- @if($data_issidedish != null) --}}
                        <option value =""> --{{ __('combobox.select-menu') }}--</option>
                        {{-- @foreach ($data_issidedish as $dataz) --}}
                            {{-- <option value ="{{ $dataz['id'] }}"> {{ $dataz['name'] }}</option> --}}
                        {{-- @endforeach --}}
                      {{-- @else --}}
                        {{-- <option value =""> --{{ __('combobox.select-menu') }}--</option> --}}
                      {{-- @endif --}}
                  </select>
                </div>
                <div class="form-group">
                  <label for="isSidedish" class="col-form-label">{{ __('outlet.side-dish') }}</label><label style="color:red">* </label>
                  <select name="selectIsSidedish" id="selectIsSidedish" class="form-control md-6 select2" >
                        <option value ="1"> {{ __('outlet.sidedish') }}</option>
                        <option value ="0"> {{ __('outlet.condiment') }}</option>
                  </select>
                </div>
                <div class="form-group">
                  <input type="hidden" class="form-control" name="changed_by" id="changed_by" value="{{ session()->get('full_name')}}">
                </div>
              </form>
            </div>
            <div class="modal-footer">
            @csrf
            @if($data_issidedish == null)
              <button type="button" id="btnSaveDis" class="btn btn-primary" disabled>Submit</button>
            @else
              <button type="button" id="btnSave" class="btn btn-primary">{{ __('button.save') }}</button>
            @endif
              <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('button.cancel') }}</button>
            </div>
          </div>
        </div>
      </div>
<script>

$(document).ready(function() {
    
    $('#addMenu').one('submit', function() {
    // $(this).find('input[type="submit"]').attr('disabled','disabled');
    // console.log('asda')
    $("#btnSubmit").attr("disabled", true);
    });

    $('#sidedishTable').DataTable({
            scrollX: true,
            scrollY:"480px",
            scrollCollapse: true,
            info: false,
            ordering: false,
            paging: false,
            autoWidth: false,
            retrieve: true,
            columnDefs: [
                { width: "50%", targets: 2 }
            ]
        }).columns.adjust()
        // .responsive.recalc();

        $('#sideDishTable').on( 'column-sizing.th', function ( e, settings ) {
            console.log( 'Column width recalculated in table' );
        } );

    $('#exampleModal').on('show.bs.modal', function (event) {
        // console.log('HIT');
        var button = $(event.relatedTarget) // Button that triggered the modal
        var modal = $(this)
        modal.find('.modal-title').text('Master System')
                
    });

    $('#exampleModal').on('hide.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var modal = $(this)
                
    });

    if(window.location.href.indexOf('success') > -1){
        $("#msg").css("display", "block");
    }
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
   
function validate(evt) {
  var theEvent = evt || window.event;

  // Handle paste
  if (theEvent.type === 'paste') {
      key = event.clipboardData.getData('text/plain');
  } else {
  // Handle key press
      var key = theEvent.keyCode || theEvent.which;
      key = String.fromCharCode(key);
  }
  var regex = /[0-9]|\./;
  if (isNaN(key) || key < 0 || key > 10) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
}
$('#btnSave').on('click',function(){
    
    var menuId =  $('#txtId').val();
    var txtOutletId =  $('#txtOutletId').val();
    var menuAsSidedishId =  $('#selectSidedish').val();
    var isSidedish = $('#selectIsSidedish').val();
    var created_by = $('#created_by').val();

    var data = {
                outlet_id : txtOutletId,
                fboutlet_mn_id : menuId,
                fboutlet_mn_sdish_id: menuAsSidedishId,
                is_sidedish : isSidedish,
                created_by: created_by
               }
    console.log(data);
    if(menuAsSidedishId != ""){
        $('#btnSave').prop('disabled', true);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
                type: 'POST',
                url: '/master/fnboutlet/menu/add_sidedish',
                data: data,
            success:function(request)
            {
                if(request.status == true){
                    $('#sidedishModal').modal('hide');
                    var url = window.location.href;    
                    if (url.indexOf('?') > -1 ){
                        if(url.indexOf('success') > -1 ){
                            url = window.location.origin + window.location.pathname + '?success'
                        }
                    }else {
                        url += '?success'
                    }
                    window.location.href = url;
                }else{
                    // alert(request.message);
                }
            }
        });
    } 
}); // End Button Submit Modal

$('#selectMenuCategory').on('change',function(e){
    var menu_cat_id = e.target.value;
    if(menu_cat_id != "" || menu_cat_id != 0){
        var menuId =  $('#txtId').val();
        var url = 'get_menu_from_category/'+menuId+'/'+menu_cat_id;
        // console.log(menu_cat_id);
        $.get(url, function (data)
        {
            $('#selectSidedish').empty();
            $('#selectSidedish').selectpicker('destroy');
            $('#selectSidedish').append('<option value="">--{{ __("combobox.select-menu") }}--</option>');
            
            // console.log(data);
            $.each(data.data, function(index, data)
            {
                // console.log(data);
                $('#selectSidedish').append('<option value="'+data.id+'">'+data.name+'</option>');
                $('#selectSidedish').selectpicker('refresh');
            });

        });
    } else {
        $('#selectSidedish').empty();
        $('#selectSidedish').selectpicker('destroy');
        $('#selectSidedish').append('<option value="">--{{ __("combobox.select-menu") }}--</option>');
    }
});
    </script>
</x-app-layout>
