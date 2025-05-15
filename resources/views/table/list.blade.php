<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('table.title') }}</title>

    <style>
    a, a:hover{
    color:#333
    }
    th, td {
        white-space: nowrap;
    }

    div.dataTables_wrapper {
        width: 100%;
        margin: 0 auto;
    }
    .dropdown-toggle {
        height: 40;
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
    .dropdown-menu{
        width: 100%;
    
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
</style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
		{{ __('table.title') }}
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
                    <br>
                    <div class="container">
                        <div class="row justify-content-md-left">
                            <div class="col-md-1">
                               <label for="labelHotel">{{ __('table.hotel') }}</label>
                            </div>
                            <div class="col-md-6">
                            {{-- <select class="form-control md-6 selectpicker" name="txtHotel" id="txtHotel" data-size="5" data-live-search="true">
                                    </select> --}}
                            @if(strpos(strtolower(session()->get('role')),'outlet') != false)
                                <select class="form-control md-6 selectpicker" id="txtHotel" name="txtHotel" data-size="5" data-live-search="true" data-container="body" disabled>
                            @else
                                <select class="form-control md-6 selectpicker" id="txtHotel" name="txtHotel" data-size="5" data-live-search="true" data-container="body">
                                <option value="">--{{ __('combobox.select-hotel') }}--</option>
                            @endif
                            @if($dataHotel != null)
                                @if((session()->get("role")=="Admin") || (Auth::user()->permission('outlet-promo-list')))
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
                            <div class="col-md-6">
                            <select class="form-control md-6 selectpicker" id="idOutlet" data-size="5" data-live-search="true" data-container="body">
                                @if((session()->get("role")=="Admin") || (Auth::user()->permission('outlet-promo-list')))
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
                    </div>
                    <table   style="float:right">
                       <th>
                        @if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-table-create"))
                        <!-- <button type="button" class="btn  btn-primary float-right" data-toggle="modal"  data-target="#tableModal" data-backdrop="static" data-keyboard="false" id="btn_add" ><i class="fas fa-plus"></i> {{ __('button.add-table') }}</button> -->
                        <button type="button" class="btn  btn-primary float-right" data-backdrop="static" data-keyboard="false" id="btn_add" onclick="showModalAdd()"> {{ __('button.add-table') }}</button>
                        @endif
                        </th>
                        <th>
                        @if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-table-list"))
                        <button type="button"  class="btn  btn-warning float-right" id="btn_bacode" data-backdrop="static" data-keyboard="false" onclick="GetBarcode()" style="color: white;" disabled>{{ __('button.barcode') }}</button> 
                        @endif
                        </th>
                    </table>                    
                    <br>
                    <br>
                        <table class="table-bordered" id="tableTable" class="display" style="width:100%">
                            <thead>
                                <tr class="bg-gray-100" style="text-align:center" valign="middle" >
                              
                                    <th class="px-2 w-10"><input type="checkbox" name="check" id="checkAll" ></th>
                                    <th class="px-4 w-10">{{ __('table.action') }}</th>
                                    <th class="px-5 w-10">{{ __('table.hotel') }}</th>
                                    <th class="px-4 w-10">{{ __('table.outlet') }}</th>
                                    <th class="px-1 w-10">{{ __('table.table-no') }}</th>
                                    <!-- <th class="px-1 w-10">barcode</th> -->
                                    <th class="px-1 w-10">{{__('table.created-by')}}</th>
                                    <th class="px-1 w-10">{{__('table.created-at')}}</th>
                                    <th class="px-1 w-10">{{__('table.updated-by')}}</th>
                                    <th class="px-1 w-10">{{__('table.updated-at')}}</th>
                                    

                                </tr>
                            </thead>
                            <tbody id="Tbodytable">
                            @if($data != null)
                                @foreach ($data as $data)
                                <tr id="{{ $data['id'] }}">
                                <td>
                                    @if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-table-list"))
                                    <input type="checkbox" name="check" id="check[]" data-key1= "{{$data['fboutlet_id']}}-{{$data['table_no']}}*{{$data['name_outlet']}}-{{$data['table_no']}}" data-key2= "{{$data['fboutlet_id']}}" >
                                    @endif
                                </td>
                                <td class="px-4 py-2" style="text-align: center;">
                                @if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-table-edit"))
                                <button id="edit" data-keyboard="false" onclick="showModalEdit('{{$data['id']}}')"  ><i class="fas fa-pen"></i></button>
                                &nbsp;&nbsp;&nbsp;
                                @endif
                                @if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-table-delete"))
                                <a href="table/delete_table/{{ $data['id'] }}" class="button delete-confirm"><i class="fas fa-trash"></i></a>
                                @endif
                                </td>
                                <td>{{$data['name_hotel']}}</td>
                                <td>{{$data['name_outlet']}}</td>
                                <td style="text-align: center;">{{$data['table_no']}}</td>
                                <td>{{ $data['created_by'] }}</td>
                                <td>{{ $data['created_at'] }}</td>
                                <td>{{ $data['updated_by'] }}</td>
                                <td>{{ $data['updated_at'] }}</td>
                                </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        
                    <br>
                </div>
            </div>
        </div>
    </div>

        <!-- Modal -->
    @if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-table-create") || Auth::user()->permission("outlet-table-edit") || Auth::user()->permission("outlet-table-delete"))
    <div class="modal fade" id="tableModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
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
                
                    <form  id="myForm">
                            <div class="col-sm">
                                <div class="form-group">
                                    <input type="hidden" class="form-control" id="id_table">
                                    <label for="full_name" class="col-form-label">{{ __('table.outlet') }}</label><label style="color:red">* </label>
                                        
                                        <select class="form-control md-6 selectpicker" id="idOutletModal" data-size="5" data-live-search="true" required>
                                        </select>
                                </div>
                                <div class="form-group">
                                    <label for="phone" class="col-form-label">{{ __('table.table-no') }}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" id="textTableNo" placeholder="{{ __('table.table-no') }}" required>
                                </div>
                                <div class="form-group">
                                <input type="hidden" class="form-control" name="created_by" id="created_by" value="{{session()->get('full_name')}}">
                                    <input type="hidden" class="form-control" name="changed_by" id="changed_by" value="{{session()->get('full_name')}}">
                                </div>
                            </div>
                    </form>
                </div>
                <div class="modal-footer">
                @csrf
                    <button type="button" id="btnSave" class="btn btn-primary">{{ __('button.save') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('button.close') }}</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>

    $(document).ready(function() {
        dataTable();  
        // getDataHotel();
        onLoadDelete();  
        // GetBarcode();
        $('#tableTable').change(function () {
                    var key1 = $('input:checkbox[name=check]:checked').map(function (_, el) { return $(el).data('key1'); }).get();
                if (key1.length >= 1) {
                    // console.log('ada');
                    $('#btn_bacode').removeAttr('disabled');
                }
                else {
                    $('#btn_bacode').attr('disabled', 'disabled');
                    // console.log('ga ada');
                    }
                });

        // var modal = $('#tableModal').on('show.bs.modal', function (event) {
        //     var button = $(event.relatedTarget); // Button that triggered the modal
        //     var modal = $(this);
        //     modal.find('.modal-title').text('Add Master Table');
        //     getDataOutlet();
        //     // document.getElementById("name").focus();
        //     });
         var modalClose = $('#tableModal').on('hide.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var modal = $(this)
                $('#id_table').val("")
                $('#textOutlet').val("");
                $('#textTableNo').val("");
            });

            // setTimeout(function() {
            //     getDataHotel();
            // }, 1000);

            
    });
    function GetBarcode() {
        var id_table = $('input:checkbox[name=check]:checked').map(function () {
            return $(this).data('key1');
        }).get();
        var id_outlet = $('input:checkbox[name=check]:checked').map(function () {
            return $(this).data('key2');
        }).get();
        console.log(id_table);
        // id_table.push(id_outlet);
        // data = {'id_table' : id_table };
        // console.log(id_table);
        url = 'table/barcode/'+id_table;
        // console.log(url);
            window.open(url);
    }


    function dataTable(data)
	{	
		$('#tableTable').DataTable({
			drawCallback: function(){
			  $('.paginate_button:not(.disabled)', this.api().table().container())          
				 .on('click', function(){
					onLoadDelete();
				 });       
			  $('.paginate_button.next:not(.disabled)', this.api().table().container())          
				 .on('click', function(){
					onLoadDelete();
				 });       
			},
			data: data,
			processing: true,
			scrollX: true,
			scrollY:"480px",
			scrollCollapse: true,
			pageLength: 10,
			info: false,
			ordering: false,
			autoWidth: true,
			retrieve: true,
			destroy:true,
		}).columns.adjust();

		$('#tableTable').on( 'column-sizing.th', function ( e, settings ) {
			console.log( 'Column width recalculated in table' );
		});
		onLoadDelete();
        
    }
	
    function onLoadDelete() {
        $('.delete-confirm').on('click', function (event) {
            // console.log('Delete');
            event.preventDefault();
            const url = $(this).attr('href');
            swal({
                title: '{{__('message.are_you_sure')}}',
                text: '{{__('message.delete_not_permanent')}}',
                icon: 'warning',
                buttons: ["{{__('message.btn_delete_cancel')}}", "{{__('message.btn_delete_yes')}}"],
            }).then(function(value) {
                if (value) {
                    // console.log(url);
                    window.location.href = url;
                }
            });
        });
    }

    //check all
        document.getElementById('checkAll').onclick = function() {
            // if(this.checked){
            //     $('#btn_bacode').removeAttr('disabled');
            // }else{
            //     $("#btn_bacode").attr("disabled");
            //     console.log('ada');
            // }        
            var key1 = $('input:checkbox[name=check]:checked').map(function (_, el) { return $(el).data('key1'); }).get();
                if (key1.length <= 1) {
                    // console.log('ada');
                    $('#btn_bacode').removeAttr('disabled');
                }
                else {
                    $('#btn_bacode').attr('disabled', 'disabled');
                    // console.log('ga ada');
                    }        
                    var checkboxes = document.getElementsByName('check');
                    for (var checkbox of checkboxes) {
                        checkbox.checked = this.checked;
                    }
        }    

    function getDataHotel(){
        // id = {{session()->get('id')}};
        // // var value = @Request.RequestContext.HttpContext.Session["id"];
        // console.log(id);
        $.get('/hotel/get_hotel_all',function (data)
        {
            // console.log(data);
            $('#txtHotel').empty();
                $('#txtHotel').append('<option value="">-- Select Hotel --</option>');
                $.each(data, function(index, data)
                {
                    $('#txtHotel').append('<option value="'+data.id+'">'+data.name+'</option>');
                    $('#txtHotel').selectpicker('refresh');
                });
        });
    }       
    function getDataOutlet(request){
            var outlet = request;
            url = 'master/fnboutlet/get_outlet_all';
                var table = $('#outletTable').DataTable();
                // console.log(outlet);
                $('#idOutletModal').empty();
                $('#idOutletModal').selectpicker('destroy');
                $('#idOutletModal').append('<option value="">-- Select Outlet --</option>');
        $.get(url, function (data){
            // console.log(data);
                $.each(data, function(index, data)
                {
                    if(outlet == data.id){  
                    $('#idOutletModal').append('<option value="'+data.id+'" selected>'+data.name+' - '+data.hotel_name+'</option>');
                    }else{
                    $('#idOutletModal').append('<option value="'+data.id+'">'+data.name+' - '+data.hotel_name+'</option>');
                    }
                    $('#idOutletModal').selectpicker('refresh');
                    
                });
            });
    } 

    $('#txtHotel').on('change',function(e){
            // console.log(e.target.value);
            var i = 1;
            var hotel_id =  e.target.value;
            var active = ""; var url = ""; var url_table = "";
            var updated_by = ""; var updated_at = "";
            var datax = []; var dataz = [];
            var user_id = '{{ Session::get("id") }}';
            var role = '{{ Session::get("role") }}';

            if(role.toLowerCase() == 'admin'){
                if(hotel_id == 0){
                    url = 'master/fnboutlet/get_outlet_all';
                    url_table = 'table/get_data';
                } else {
                    url = 'master/fnboutlet/get_hotel_outlet/' + hotel_id;
                    url_table ='table/get_data_by_hotel/'+hotel_id;
                }
            } else {
                if(hotel_id == 0){
                    location.reload();
                } else {
                    url = 'master/fnboutlet/get_hotel_outlet_with_user/' + hotel_id + '/' + user_id
                    url_table ='table/get_data_by_hotel_with_user/'+hotel_id+'/' + user_id;
                }
            }
            // console.log(url);
            refreshTable(url_table);
            $.get(url, function (data)
            {
                
                // var table = $('#outletTable').DataTable();
                // console.log(data);
                $('#idOutlet').empty();
                $('#idOutlet').selectpicker('destroy');
                $('#tableBodyOutlet').empty();
                $('#idOutlet').append('<option value="">-- Select Outlet --</option>');

                // table.clear();
                $.each(data, function(index, data)
                {
                    //console.log(data);
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
                    $('#idOutlet').append('<option value="'+data.id+'">'+data.name+'</option>');
                    $('#idOutlet').selectpicker('refresh');
                    
                });
            });
    });

    $('#idOutlet').on('change',function(e){
            // console.log(e.target.value);
            var i = 1;
            var outlet_id =  e.target.value;
            var hotel_id =  $('#txtHotel').val();
            // var url_table ='table/get_data_by_hotel/'+hotel_id;
            // console.log(hotel_id);
            var updated_by = ""; var updated_at = "";
            var datax = []; var dataz = [];
            if(e.target.value != 0){
                url = 'table/get_data_by_outlet/' + outlet_id;
                refreshTable(url);
            }else{
                location.reload();
                // url = 'table/get_data_by_outlet/0';
            }
    });

    function refreshTable(url){
        // console.log(url);
        var user_id = '{{ Session::get("id") }}';
        var role = '{{ Session::get("role") }}';
        if(url == "" || url == null){
            if(role.toLowerCase() == 'admin'){
                url = 'get_data';
            } else {
                url = 'get_data_by_hotel_with_user/0/'.user_id;
            }
        }
        
        $.get(url,function (data)
            {
                var table = $('#tableTable').DataTable();
                table.destroy();
                $('#Tbodytable').empty();
                // table.clear();
                // console.log(data);			
                $.each(data, function(index, data)
                {
                    if(data.updated_by == null){
                        var updated_by = '';
                    }else{
                        var updated_by = data.updated_by;
                    }
                    $('#Tbodytable').append(
                    '<tr id="'+data.id+'">'+
                        '@if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-table-list"))'+
                        '<td><input type="checkbox" name="check" id="check[]" data-key1= "'+data.fboutlet_id+'-'+data.table_no+'*'+data.name_outlet+'-'+data.table_no+'" data-key2= "'+data.fboutlet_id+'" ></td>'+ 
                        '@endif'+
                        '<td class="px-4 py-2" style="text-align: center;">'+
                        '@if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-table-edit"))'+
                        '<button id="edit" data-keyboard="false" onclick="showModalEdit('+data.id+')"  ><i class="fas fa-pen"></i></button>'+
                        '&nbsp;&nbsp;&nbsp;'+
                        '@endif'+
                        '@if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-table-delete"))'+
                        '<a href="table/delete_table/'+data.id+'" class="button delete-confirm"><i class="fas fa-trash"></i></a>'+
                        '@endif'+
                        '</td>'+
                        '<td>'+data.name_hotel+'</td>'+
                        '<td>'+data.name_outlet+'</td>'+
                        '<td style="text-align: center;">'+data.table_no+'</td>'+
                        '<td>'+data.created_by+'</td>'+
                        '<td>'+data.created_at+'</td>'+
                        '<td>'+updated_by+'</td>'+
                        '<td>'+data.updated_at+'</td>'+
                    '</tr>');
                });
                $('#tableTable').DataTable({
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

                $('#tableTable').on( 'column-sizing.th', function ( e, settings ) {
                    console.log( 'Column width recalculated in table' );
                });
                onLoadDelete();
                // dataTable(data);  
            });
    }

    
    // add and edit
    $('#btnSave').on('click',function()
    {
        $('#btnSave').prop('disabled', true);
        var id =  $('#id_table').val();
       
        if (id == '')
        {
            var data = {
                fboutlet_id : $('#idOutletModal').val(),
                table_no : $('#textTableNo').val(),
                created_by : $('#created_by').val()
            }
            // console.log(data);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: 'table/save_table',
                    data: data,
                success:function(request)
            {
                if(request.status == true){
                    alert(request.message);
                    $('#tableModal').modal('hide');
                    location.reload();
                    // refreshTable();
                    // setTimeout(location.reload.bind(location), 2000);
                    $('#btnSave').prop('disabled', false);
                }else{
                    alertModal(request.message);
                }
            }
            });
        }
        else
        {
            var data = {
                id : $('#id_table').val(),
                fboutlet_id : $('#idOutletModal').val(),
                table_no : $('#textTableNo').val(),
                updated_by : $('#created_by').val()
            }
            // console.log('data');
            $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: 'table/edit_table',
                    data: data,
                success:function(request)
            {
                if(request.status == true){
                    alert(request.message);
                    $('#tableModal').modal('hide');
                    location.reload();
                    // refreshTable();
                    // setTimeout(location.reload.bind(location), 2000);
                    $('#btnSave').prop('disabled', false);
                }else{
                    alertModal(request.message);
                }
            }
            });
        }
    });

    function showModalAdd(){
        // console.log(id);
        $('#tableModal').modal('show');
        var modal =  $('#tableModal');
            modal.find('.modal-title').text('Add Master Table');
        getDataOutlet();
    }

    function showModalEdit(id){
        // console.log(id);
        $('#tableModal').modal('show');
        var modal =  $('#tableModal');
            modal.find('.modal-title').text('Edit Master Table');
        $.get('table/get_data_by_id/'+id,function (data){
            // console.log(data[0].table_no);
            $('#id_table').val(data[0].id);
            $('#textTableNo').val(data[0].table_no);
            // $('#id_outlet').val(data[0].fboutlet_id);
            getDataOutlet(data[0].fboutlet_id)
        });
    }

    function alertModal(message) {
    var x = document.getElementById("snackbarModal");
    // x.innerHTML += ;
    x.className = "show";
    x.innerHTML = message;
    setTimeout(function(){ x.className = x.className.replace("show", "hide"); }, 2000);
    // location.reload();
    }

    function alert(message) {
        console.log(message)
    var x = document.getElementById("snackbar");
            x.style.display = "block";
    x.innerHTML = '<button type="button" class="close" onclick="dismissAlert()">×</button>'+message;
    // x.className = "show";
    // x.innerHTML =+ message;
    // setTimeout(function(){ x.className = x.className.replace("show", ""); }, 2000);
    // location.reload();
    }

    function alertErorr(message) {
        console.log(message)
    var x = document.getElementById("snackbarErorr");
            x.style.display = "block";
    x.innerHTML = '<button type="button" class="close" onclick="dismissAlertError()">×</button>'+message;
    // x.className = "show";
    // x.innerHTML =+ message;
    // setTimeout(function(){ x.className = x.className.replace("show", ""); }, 2000);
    // location.reload();
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
</script>
</x-app-layout>
