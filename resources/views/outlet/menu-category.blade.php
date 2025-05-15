<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$judul}}</title>
    <style>
    .dropdown-toggle {
        height: 40;
    }
    #idOutlet {
        min-width: 0;
    }

    #loader {
        display:none;
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 120px;
        height: 120px;
        -webkit-animation: spin 2s linear infinite; /* Safari */
        animation: spin 2s linear infinite;
        }
    #loader.show {
        display : block;
        }
    #table.hide {
        display : none;
        }
    th, td {
        white-space: nowrap;
    }

    div.dataTables_wrapper {
        width: 100%;
        margin: 0 auto;
    }
    .hide {
        visibility: hidden;
        border: none;
    }
    /* #snackbar {
        visibility: hidden;
        min-width: 250px;
        margin-left: -125px;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 2px;
        padding: 16px;
        position: fixed;
        z-index: 1;
        left: 50%;
        bottom: 30px;
        font-size: 17px;
    }

    #snackbar.show {
        visibility: visible;
        -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
        animation: fadein 0.5s, fadeout 0.5s 2.5s;
    } */

    #snackbarModal.show {
    /* visibility: visible; */
        display : block;
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
        position: relative;
        padding: .75rem 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
        border-radius: .25rem;
        -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
        animation: fadein 0.5s, fadeout 0.5s 2.5s;
    }
    #snackbarModal.hide {
    /* visibility: hidden; */
        display : none;
        -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
        animation: fadein 0.5s, fadeout 0.5s 2.5s;
    }

    @-webkit-keyframes fadein {
        from {bottom: 0; opacity: 0;} 
        to {bottom: 30px; opacity: 1;}
    }

    @keyframes fadein {
        from {bottom: 0; opacity: 0;}
        to {bottom: 30px; opacity: 1;}
    }

    @-webkit-keyframes fadeout {
        from {bottom: 30px; opacity: 1;} 
        to {bottom: 0; opacity: 0;}
    }

    @keyframes fadeout {
        from {bottom: 30px; opacity: 1;}
        to {bottom: 0; opacity: 0;}
    }
    .dropdown-menu{
        width: 100%;
    
    }
</style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{  $judul }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div id="app">
                @include('/flash-message')
                @yield('content')
            </div>
            <!-- allert pop up -->
            <div class="alert alert-success alert-block " id="snackbar" style ="display:none ">
                </div>
                <div class="alert alert-danger alert-block " id="snackbarErorr" style ="display:none ">
                </div>
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                    <div class="container">
                        <div class="mt-6">
                            <div class="row justify-content-md-left">
                                <div class="col-md-1">
                                   <label for="labelHotel">{{ __('outlet.hotel') }}</label>
                                </div>
                                <div class="col-md-4">
                                @if(strpos(strtolower(session()->get('role')),'outlet') != false)
                                    <select class="form-control md-6 selectpicker" id="idHotel" data-size="5" data-live-search="true" disabled>
                                @else
                                    <select class="form-control md-6 selectpicker" id="idHotel" data-size="5" data-live-search="true">
                                    <option value="">--{{ __('combobox.select-hotel') }}--</option>
                                @endif
                                @if($dataHotel != null)
                                    @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-list'))
                                        @if(count($dataHotel) > 1)
                                            @foreach ($dataHotel as $datax)
                                                <option value="{{ $datax['id'] }}">{{ $datax['name'] }}</option>
                                            @endforeach
                                        @else
                                            <option value="{{ $dataHotel[0]['id'] }}" selected>{{ $dataHotel[0]['name'] }}</option>
                                        @endif
                                    @else
                                        @foreach ($dataHotel as $datax)
                                            <option value="{{ $datax['id'] }}">{{ $datax['name'] }}</option>
                                        @endforeach
                                    @endif
                                @endif
                                </select>
                                </div>
                            </div>
                            <br>
                            <div class="row justify-content-md-left">
                                <div class="col-md-1">
                                   <label for="labelHotel">{{ __('outlet.title') }}</label>
                                </div>
                                <div class="col-md-4">
                                <select class="form-control md-6 selectpicker" id="idOutlet" data-size="5" data-live-search="true" data-container="body">
                                    @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-list'))
                                    <option value="">--{{ __('combobox.select-outlet') }}--</option>
                                        @if($dataOutlet != null)
                                            @foreach ($dataOutlet as $dataO)
                                                <option value="{{ $dataO['id'] }}">{{ $dataO['name'] }}</option>
                                            @endforeach
                                        @endif
                                    @endif
                                </select>
                                </div>
                            </div>
                        
                            <table   style="float:right">
                                @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-menu-category-create'))
                                <th>
                                <button type="button" class="btn  btn-primary float-right" data-toggle="modal" data-target="#exampleModal" data-backdrop="static" data-keyboard="false"  id="btn_add" >{{ __('button.add_category') }}</button>
                                </th>
                                @endif
                                <th>
                                &nbsp&nbsp&nbsp&nbsp
                                </th>
                            </table>
                            <br>
                            <br>
                        </div><center>
                        <div class="max-w-5xl mx-auto sm:px-6 lg:px-4" id="loader"></div>
                        </center>
                        <div class="max-w-5xl mx-auto sm:px-6 lg:px-4" id="table" >
                            <table class="table-bordered" id="categoryTable" class="display" style="width:100%">
                                <thead>
                                    <tr class="bg-gray-100" style="text-align:center" valign="middle" >
                                        <th class="px-2" style="width:10px">{{ __('table.no') }}</th>
                                        <th class="px-4 w-10">{{ __('table.action') }}</th>
                                        <th class="px-4 w-10">{{ __('outlet.name_category') }}</th>
                                        <th class="px-4 w-10">{{ __('hotel.seq-no') }}</th>
                                        <th class="px-4 w-10">{{ __('outlet.outlet-name') }}</th>
                                        <th style="display:none">Fb Outlet ID</th>
                                        <th style="display:none">show In Menu</th>
                                        <th class="px-5 w-10">{{__('msystem.created-by') }}</th>
                                        <th class="px-5 w-10">{{__('msystem.created-at') }}</th>
                                        <th class="px-5 w-10">{{__('msystem.changed-by') }}</th>
                                        <th class="px-5 w-10">{{__('msystem.updated-at') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyMenuCategory">
                                @if(!empty($data))
                                    @foreach( $data as $datax )
                                    <tr id="{{ $datax['id'] }}" style="text-align:left" valign="middle">
                                        <td class="text-center">{{$loop->iteration}}</td>
                                        <td class="px-4 py-2" style="text-align: center;">
                                            @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-menu-category-edit'))
                                            <button id="edit" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-key1="{{ $datax['id'] }}"  ><i class="fas fa-pen"></i></button>
                                            &nbsp;&nbsp;&nbsp;
                                            @endif
                                            @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-menu-category-delete'))
                                            <button class="button delete-confirm" onclick="deleteData('{{ $datax['id'] }}')"><i class="fas fa-trash"></i></button>
                                            @endif
                                        </td>
                                        <td>{{ $datax['name'] }}</td>
                                        <td style="text-align:center" valign="middle">{{ $datax['seq_no'] }}</td>
                                        <td style="display:none">{{ $datax['fboutlet_id'] }}</td>
                                        <td>{{ $datax['outlet_name'] }}</td>                                        
                                        <td style="display:none">{{ $datax['show_in_menu'] }}</td>
                                        <td>{{ $datax['created_by'] }}</td>
                                        <td style="text-align:center" valign="middle">{{ $datax['created_at'] }}</td>
                                        <td>{{ $datax['updated_by'] }}</td>
                                        <td style="text-align:center" valign="middle">{{ $datax['updated_at'] }}</td>
                                    </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    <br>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">New message</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <!-- modal toast-->
       <div id="snackbarModal"></div>
       <form id="myForm" method='post' onkeypress="handle(event)">
        <div class="form-group">
            <label for="full_name" class="col-form-label">{{ __('table.outlet') }}</label><label style="color:red">* </label>
            <select class="form-control md-6 selectpicker" id="idOutletModal" data-size="4" data-live-search="true" onchange="getSeqNo(this)">
            </select>
        </div>
          <div class="form-group">
            <input type="hidden" class="form-control" id="id_menu_category" name="id_menu_category">
            <label for="System_type" class="col-form-label">{{ __('outlet.category-name') }}</label><label style="color:red">* </label>
            <input type="text" class="form-control" name="txtName" id="txtName" placeholder="{{ __('outlet.category-name') }}" autofocus required>
          </div>
          <div class="form-group">
            <label for="txtSeq" class="col-form-label">{{ __('hotel.seq-no') }}</label><label style="color:red">* </label>
            {{-- @if(!empty($seqNo)) --}}
            {{-- <input type="number" class="form-control" name="txtSeq" id="txtSeq" placeholder="{{ __('hotel.seq-no') }}" value="{{ $seqNo }}" required> --}}
            {{-- @else --}}
            <input type="number" class="form-control" name="txtSeq" id="txtSeq" placeholder="{{ __('hotel.seq-no') }}" required>
            {{-- @endif --}}
          </div>
          
          <div class="form-group">
            <label for="txtSeq" class="col-form-label">Show in menu</label><label style="color:red">* </label>
            <input type="checkbox" id="show_in_menu" name="show_in_menu" value="1" checked>
            <input type="hidden" class="form-control" name="created_by" id="created_by" value="{{session()->get('full_name')}}">
            <input type="hidden" class="form-control" name="updated_by" id="updated_by" value="{{session()->get('full_name')}}">
            <input type="hidden" class="form-control" name="created_begin" id="created_begin" >
          </div>
        </form>
      </div>
      <div class="modal-footer">
      @csrf
      @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-menu-category-create'))
        <button type="button" id="btnSave" class="btn btn-primary" disabled>{{ __('button.save') }}</button>
        @endif
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('button.close') }}</button>
        </form>
      </div>
    </div>
  </div>
</div>

    <script>

    $(document).ready(function() {
        // dataTable();    
        cssDataTable();
        // onLoadDelete();
        loadEditor();
        $('#btn_edit').attr('disabled','disabled');
        $('#btn_delete').attr('disabled', 'disabled');  

        // Prepare Modal
        var modal = $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var modal = $(this)
            modal.find('.modal-title').text("{{ __('outlet.add_category') }}")
            getDataOutlet();
        });

        var modalClose = $('#exampleModal').on('hide.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var modal = $(this)
            $('#id_menu_category').val("")
            $('#txtName').val("");
            $('#txtSeq').val("");
        });

        // $('#btn_add').click(function(){
            // $('#txtSeq').val('{!! $seqNo !!}');
        // });
    });

    function loaderTable() {
        var x = document.getElementById("loader");
        var y = document.getElementById("table");
        y.className = "hide";
        x.className = "show";
    }

    function cssDataTable() {
        $('#categoryTable').DataTable({
                processing: true,
                scrollX: true,
                scrollY:"480px",
                scrollCollapse: true,
                pageLength: 10,
                info: false,
                ordering: false,
                autoWidth: true,
            });

            $('#categoryTable').on( 'column-sizing.th', function ( e, settings ) {
                console.log( 'Column width recalculated in table' );
            });
    }

    function loadEditor() {
        $('#categoryTable tbody').on( 'click', '#edit', function () {
                $('#exampleModal').modal();
                var modal = $('#exampleModal');
                 modal.find('.modal-title').text("{{ __('outlet.edit_category') }}");
                var table = $('#categoryTable').DataTable();
                var data = table.row( $(this).parents('tr') ).data()
                var id = data['DT_RowId']; 
                console.log(data);
                
                $('#id_menu_category').val(data['DT_RowId']);
                $('#txtName').val(data[2]);
                $('#txtSeq').val(data[3]);
                $('#created_begin').val(data[7]);
                if(data[6] == 1){
                    document.getElementById("show_in_menu").checked = true;
                }else{
                    document.getElementById("show_in_menu").checked = false;
                }
                getDataOutlet(data[4]);
            });
    }
    
    // panggil Web Route get_menu_category_all utk mendapatkan data semua category
    // yang ditampilkan ke DataTabel
    function dataTable (theData){
        // console.log($("#idHotel").val());
        var theUrl = "";
        if($("#idHotel").val() === null || $("#idHotel").val() === "") {
            // console.log('MASUK');
            if('{!! strtolower(session()->get('role')) !!}' == 'admin') {
                url = 'get_menu_category_all?';
            } else {
                url = 'get_menu_category_all_user?user_id='+ {!! session()->get('id') !!} + '&';
            }
            if(theData == null || theData == undefined || theData == "") {
                theUrl = url;
            }else {
                theUrl = url+'fboutlet_id='+theData;
            }
        } else {
            if('{!! strtolower(session()->get('role')) !!}' == 'admin') {
                url = 'get_menu_category_all?'+ '&hotel_id=' + $("#idHotel").val() + '&';
            } else {
                url = url = 'get_menu_category_all_user?user_id='+ {!! session()->get('id') !!} + '&hotel_id=' + $("#idHotel").val() + '&';
            }
            if(theData == null || theData == undefined || theData == "") {
                theUrl = url;
            }else {
                theUrl = url+'fboutlet_id='+theData;
            }
        }
        // console.log(theUrl);
        $.get(theUrl,function (data)
            {
                var i =1;
                var table = $('#categoryTable').DataTable();
                table.destroy();
                // console.log(data);
                $('#bodyMenuCategory').empty();
                $.each(data, function(index, data)
                {
                    if(data.updated_by == null){
                        var updated_by = '';
                    }else{
                        var updated_by = data.updated_by;
                    }
                    if(data.created_by == null){
                        var created_by = '';
                    }else{
                        var created_by = data.created_by;
                    }
                    
                    $('#bodyMenuCategory').append(
                        
                        '<tr id="'+data.id+'">'+
                        '<td style="text-align:center" valign="middle">'+i+'</td>'+
                        '<td class="px-4 py-2" style="text-align: center;">'+
                        '@if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-menu-category-edit"))'+
                        '<button id="edit" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-key1="'+data.id+'"  ><i class="fas fa-pen"></i></button>'+
                                        '&nbsp;&nbsp;&nbsp;'+
                        '@endif'+
                        '@if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-menu-category-delete"))'+
                        '<a onclick="deleteData('+data.id+') " class="button delete-confirm" style="cursor:pointer"><i class="fas fa-trash"></i></a>'+
                        '@endif'+
                        '</td>'+
                        '<td>'+data.name+'</td>'+
                        '<td style="text-align:center" valign="middle">'+data.seq_no+'</td>'+
                        '<td style="display:none">'+data.fboutlet_id+'</td>'+
                        '<td>'+data.outlet_name+'</td>'+
                        '<td style="display:none">'+data.show_in_menu+'</td>'+
                        '<td>'+created_by+'</td>'+
                        '<td style="text-align:center" valign="middle">'+data.created_at+'</td>'+
                        '<td>'+updated_by+'</td>'+
                        '<td style="text-align:center" valign="middle">'+data.updated_at+'</td>'+
                        '</tr>');
                        // console.log(updated_by);
                        i +=1;
                });

            $('#categoryTable').DataTable({
                processing: true,
                scrollX: true,
                scrollY:"480px",
                scrollCollapse: true,
                pageLength: 10,
                info: false,
                ordering: false,
                autoWidth: true,
                destroy:true,
            });

            $('#categoryTable').on( 'column-sizing.th', function ( e, settings ) {
                console.log( 'Column width recalculated in table' );
            });

            // onLoadDelete();

            $('#categoryTable tbody').on( 'click', '#edit', function () {
                $('#exampleModal').modal();
                var table = $('#categoryTable').DataTable();
                var data = table.row( $(this).parents('tr') ).data()
                var id = data['DT_RowId']; 
                // console.log(data);
                $('#id_menu_category').val(data['DT_RowId']);
                $('#txtName').val(data[2]);
                $('#txtSeq').val(data[3]);
                $('#created_begin').val(data[7]);
                console.log(data[7])
                if(data[6] == 1){
                    document.getElementById("show_in_menu").checked = true;
                }else{
                    document.getElementById("show_in_menu").checked = false;
                }
                getDataOutlet(data[4]);
            });

        } );
        
    }

    function deleteData(id){
        console.log(id);
        swal({
                title: '{{__('message.are_you_sure')}}',
                text: '{{__('message.delete_not_permanent')}}',
                icon: 'warning',
                buttons: ["{{__('message.btn_delete_cancel')}}", "{{__('message.btn_delete_yes')}}"],
            }).then(function(value) {
                if (value) {
                    // console.log('ada');
                    url = "menu/category/delete_menu_category/"+id;
                    // console.log(url);
                    window.location.href = url;
                }
            });
    }

    // Fungsi Save Button di Modal
    $('#btnSave').on('click',function(){
        // console.log('ada');
        var showmenu = document.getElementById("show_in_menu").checked
        if (showmenu){
            showmenu = 1;
        }else{
            showmenu = 0;
        }
            var id =  $('#id_menu_category').val();
            if (id == ''){
                var data = {
                            name : $('#txtName').val(),
                            seq_no : $('#txtSeq').val(),
                            fboutlet_id : $('#idOutletModal').val(),
                            show_in_menu: showmenu,
                            updated_by: $('#updated_by').val(),
                            created_by: $('#created_by').val()
                            }  

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: 'POST',
                        url: 'menu/category/save_menu_category',
                        data: data,
                        success:function(request)
                        {
                            // request = (JSON.parse(request));
                            console.log(request);
                            if(request.status == true){
                                // console.log(request.message);
                                alert(request.message);
                                $('#exampleModal').modal('hide');
                                // loaderTable();
                                // setTimeout(dataTable(), 2000);
                                dataTable();
                                location.reload();
                            }else{
                                alertModal(request.message);
                            }
                        }
                    });
            }else{
                var data = {
                            id : $('#id_menu_category').val(),
                            name : $('#txtName').val(),
                            seq_no : $('#txtSeq').val(),
                            fboutlet_id : $('#idOutletModal').val(),
                            show_in_menu: showmenu,
                            updated_by: $('#updated_by').val(),
                            created_by: $('#created_begin').val()
                            }           
                // console.log(data);
                $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                });

                $.ajax({
                    type: 'POST',
                    url: 'menu/category/save_menu_category',
                    data: data,
                    success:function(request)
                    {
                        if(request.status == true){
                            alert(request.message);
                            $('#exampleModal').modal('hide');
                            // loaderTable();
                            dataTable();
                            location.reload();
                            // setTimeout(dataTable(), 2000);
                        }else{
                            alertModal(request.message);
                        }
                    }
                });
            }
    });

    function alert(message) {
        console.log(message)
    var x = document.getElementById("snackbar");
            x.style.display = "block";
    x.innerHTML = '<button type="button" class="close" onclick="dismissAlert()">×</button>'+message;
    }

    function alertErorr(message) {
        console.log(message)
    var x = document.getElementById("snackbarErorr");
            x.style.display = "block";
    x.innerHTML = '<button type="button" class="close" onclick="dismissAlertError()">×</button>'+message;
    }

    function dismissAlert(){
        // console.log('ada');
        var x = document.getElementById("snackbar");
            x.style.display = "none";
    }
    function dismissAlertError(){
        // console.log('ada');
        var x = document.getElementById("snackbarErorr");
            x.style.display = "none";
    }
    function alertModal(message) {
    var x = document.getElementById("snackbarModal");
        // x.innerHTML += ;
        x.className = "show";
        x.innerHTML = message;
        setTimeout(function(){ x.className = x.className.replace("show", "hide"); }, 2000);
        // location.reload();
    }

    // function untuk meng-handle Keypress Enter saat User mengisi text di inputan Modal
    function handle(e){
        if(e.keyCode === 13){
            e.preventDefault(); // Ensure it is only this code that runs
            $("#btnSave").click()
        }
    }

    $('#idOutlet').on('change',function(e){
         dataTable(e.target.value);
    });

    function getDataOutlet(request){
        // console.log('data outlet' )
        $('#btnSave').prop('disabled', true);
        if('{!! strtolower(session()->get('role')) !!}' == 'admin') {
            url = 'get_outlet_all';
        } else {
            url = 'get_outlet_all?user_id=' + {!! session()->get('id') !!}
        }
        var outlet = request;
        console.log(request);
        $('#idOutletModal').empty();
        $('#idOutletModal').selectpicker('destroy');
        $('#idOutletModal').append('<option value="">-- Select Outlet --</option>');

        $.get(url, function (data){
            // console.log(data);
            $.each(data, function(index, data)
            {
                if(outlet == data.id){  
                    $('#idOutletModal').append('<option value="'+data.id+'" selected>'+data.name+'-'+data.hotel_name+'</option>');
                    $('#btnSave').prop('disabled', false);
                }else{
                    $('#idOutletModal').append('<option value="'+data.id+'">'+data.name+'-'+data.hotel_name+'</option>');
                    $('#btnSave').prop('disabled', false);
                }
                $('#idOutletModal').selectpicker('refresh');
                
            });
        });
    }

    $('#idHotel').on('change',function(e){
            console.log('ada');
            var i = 1;
            var hotel_id =  e.target.value;
            // console.log('hotel_id'+hotel_id);
            var active = ""; var url = "";
            var updated_by = ""; var updated_at = "";
            var datax = []; var dataz = [];
            var user_id = '{{ Session::get("id") }}';
            var role = '{{ Session::get("role") }}';

            if(role.toLowerCase() == 'admin'){
                if(hotel_id == 0){
                    url = 'get_menu_category_all_hotel';
                } else {
                    url = 'get_menu_category_all_hotel/' + hotel_id;
                }
            } else {
                if(hotel_id == 0){
                    url = 'get_menu_category_all_user_hotel/' + user_id;
                } else {
                    url = 'get_menu_category_all_user_hotel/' + hotel_id + '/' + user_id;
                }
            }
        
            $.get(url, function (data)
            {
                // console.log(data);
                var table = $('#categoryTable').DataTable();
                table.destroy();
                $('#idOutlet').empty();
                $('#idOutlet').selectpicker('destroy');
                $('#bodyMenuCategory').empty();
                $('#idOutlet').append('<option value="">-- Select Outlet --</option>');
                
                // console.log(data);
                $.each(data.data, function(index, data)
                {
                    // console.log(data);
                    if(data.created_by == null){
                        created_by = '';
                    } else {
                        created_by = data.created_by;
                    }
                    if(data.created_at == null){
                        created_at = '';
                    } else {
                        created_at = data.created_at;
                    }
                    if(data.updated_by == null){
                        updated_by = '';
                    } else {
                        updated_by = data.updated_by;
                    }
                    if(data.updated_at == null){
                        updated_at = '';
                    } else {
                        updated_at = data.updated_at;
                    }
                    // datax[index] = data.outlet_id;
                    // dataz[index] = data.outlet_name;
                    // $('#idOutlet').append('<option value="'+data.outlet_id+'">'+data.outlet_name+'</option>');
                    // $('#idOutlet').selectpicker('refresh');
                    $('#bodyMenuCategory').append(
                        '<tr id="'+data.id+'">'+
                        '<td style="text-align:center" valign="middle">'+i+'</td>'+
                        '<td class="px-4 py-2" style="text-align: center;">'+
                        '@if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-menu-category-edit"))'+
                        '<button id="edit" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-key1="'+data.id+'"  ><i class="fas fa-pen"></i></button>'+
                                    '&nbsp;&nbsp;&nbsp;'+
                        '@endif'+
                        '@if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-menu-category-delete"))'+
                        '<a onclick="deleteData('+data.id+') " class="button delete-confirm" style="cursor:pointer"><i class="fas fa-trash"></i></a>'+
                        '@endif'+
                        '</td>'+
                        '<td>'+data.name+'</td>'+
                        '<td style="text-align:center" valign="middle">'+data.seq_no+'</td>'+
                        '<td style="display:none">'+data.fboutlet_id+'</td>'+
                        '<td>'+data.outlet_name+'</td>'+
                        '<td style="display:none">'+data.show_in_menu+'</td>'+
                        '<td>'+created_by+'</td>'+
                        '<td style="text-align:center" valign="middle">'+data.created_at+'</td>'+
                        '<td>'+updated_by+'</td>'+
                        '<td style="text-align:center" valign="middle">'+data.updated_at+'</td>'+
                        '</tr>');
                    i += 1
                    // datax.push(data);
                });

                $.each(data.dataOutlet, function(index, data)
                {
                    $('#idOutlet').append('<option value="'+data.id+'">'+data.name+'</option>');
                    $('#idOutlet').selectpicker('refresh');
                });
                                
                $('#categoryTable').DataTable({
                    processing: true,
                    scrollX: true,
                    scrollY:"480px",
                    scrollCollapse: true,
                    pageLength: 10,
                    info: false,
                    ordering: false,
                    autoWidth: true,
                    destroy:true,
                });

                $('#categoryTable').on( 'column-sizing.th', function ( e, settings ) {
                    console.log( 'Column width recalculated in table' );
                });
               
                $('#categoryTable tbody').on( 'click', '#edit', function () {
                    // console.log('ada');
                    $('#exampleModal').modal();
                    var table = $('#categoryTable').DataTable();
                    var data = table.row( $(this).parents('tr') ).data()
                    var id = data['DT_RowId']; 
                    console.log(data);
                    $('#id_menu_category').val(data['DT_RowId']);
                    $('#txtName').val(data[2]);
                    $('#txtSeq').val(data[3]);
                    $('#created_begin').val(data[7]);
                    if(data[6] == 1){
                    document.getElementById("show_in_menu").checked = true;
                    }else{
                        document.getElementById("show_in_menu").checked = false;
                    }
                    getDataOutlet(data[4]);
                });
            });
            
    }); 

    function getSeqNo(idOutlet)
    {
        var outlet_id = idOutlet.value;
        var url = "/master/fnboutlet/menu/category/get_seq_no_menu_category/"+outlet_id;
        console.log(url);
        if(outlet_id !== "" || outlet_id !== null)
        {
            $.get(url, function (data){
                if(data.status) {
                    if(data.data !== null || data.data !== "")
                    {
                        $('#txtSeq').val(data.data);
                    } 
                } else {
                    $('#txtSeq').val(1);
                }
            });
        }
    }

</script>
</x-app-layout>
