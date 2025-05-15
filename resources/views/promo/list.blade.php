<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('promo.title') }}</title>
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
    .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 40rem; }
    .toggle.ios .toggle-handle { border-radius: 40rem; }
    .dropdown-menu{
        width: 100%;
    
    }
</style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
		{{ __('promo.title') }}
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
                    <br><br>
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
                        @else
                            <select class="form-control md-6 selectpicker" id="idHotel" data-size="5" data-live-search="true">
                            <option value="">--{{ __('combobox.select-hotel') }}--</option>
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
                    <br>
                    @if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-promo-create"))
                    <button type="button" class="btn  btn-primary float-right" onclick="addData()" data-backdrop="static" data-keyboard="false" id="btn_add" >{{ __('button.add-promo') }}</button>
                    @endif
                    <br>
                    <br>
                        <table class="table-bordered" id="promoTable" class="display" style="width:100%">
                            <thead>
                                <tr class="bg-gray-100" style="text-align:center" valign="middle" >
                              
                                    <th class="px-2" style="width:10px">{{ __('promo.no') }}</th>
                                    <th class="px-4 w-10">{{ __('promo.action') }}</th>
                                    <th class="px-5 w-10">{{ __('promo.name') }}</th>
                                    <th class="px-4 w-10">{{ __('promo.description') }}</th>
                                    <th class="px-1 w-10">{{ __('promo.value') }}</th>
                                    <th class="px-1 w-10">{{ __('promo.max-discount') }}</th>
                                    <th class="px-1 w-10">{{ __('table.outlet') }}</th>
                                    <th class="px-1 w-10">{{__('promo.valid-from')}}</th>
                                    <th class="px-1 w-10">{{__('promo.valid-to')}}</th>
                                    <th class="px-1 w-10">{{__('promo.created-by')}}</th>
                                    <th class="px-1 w-10">{{__('promo.created-at')}}</th>
                                    <th class="px-1 w-10">{{__('promo.updated-by')}}</th>
                                    <th class="px-1 w-10">{{__('promo.updated-at')}}</th>
                                 </tr>
                            </thead>
                            <tbody id="TbodyPromo">
                            @if($data != null)
                                @foreach ($data as $data)
                                <tr id="{{ $data['id'] }}">
                                <td>{{$loop->iteration}}</td>
                                <td class="px-4 py-2" style="text-align: center;">
                                @if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-promo-edit"))
                                <button id="edit" onclick="getEdit('{{ $data['id']}}')" data-backdrop="static" data-keyboard="false"><i class="fas fa-pen"></i></button>
                                    &nbsp;&nbsp;&nbsp;
                                @endif
                                @if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-promo-delete"))
                                <button id="delete" onclick="deleteData('{{ $data['id'] }}')"><i class="fas fa-trash"></i></button>
                                @endif                                
                                </td>
                                <td>{{$data['name']}}</td>
                                <td>{{$data['description']}}</td>
                                <td>{{$data['value']}}</td>
                                <td>{{$data['max_discount_price']}}</td>
                                <td>{{$data['name_outlet']}}</td>
                                <td>{{$data['valid_from']}}</td>
                                <td>{{$data['valid_to']}}</td>
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
    <div class="modal fade bd-example-modal-lg" id="promoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New message</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <!-- modal toast-->
                <div id="snackbarModal" ></div>
                
                    <form  id="myForm">
                        <div class="row">
                            <div class="col-sm">
                                <div class="form-group">
                                    <input type="hidden" class="form-control" id="id_promo">
                                    <label for="full_name" class="col-form-label">{{ __('promo.name') }}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" id="name" placeholder="{{ __('promo.name') }}" >
                                </div>
                                <div class="form-group">
                                    <label for="phone" class="col-form-label">{{ __('promo.value') }}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" id="value" placeholder="{{ __('promo.value') }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" max="999" >
                                </div>
                                <div class="form-group">                                    
                                    <label for="role" class="col-form-label">{{ __('promo.max-discount') }}</label><label style="color:red"> </label>
                                    
                                    <input type="text" class="form-control" id="max_discount" placeholder="{{ __('promo.max-discount') }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" >
                                </div>
                                <div class="form-group">
                                    <label for="full_name" class="col-form-label">{{ __('table.outlet') }}</label><label style="color:red">* </label>
                                    <!-- <input type="text" class="form-control" name="nameOutlet" id="nameOutlet"> -->
                                        <select class="form-control md-6 selectpicker" id="idOutletModal" data-size="4" data-live-search="true">
                                        </select>
                                </div>
                                </div>
                            <div class="col-sm">
                               
                                <div class="form-group" id="divPassword" style="display:block">
                                    <label for="password" class="col-form-label" >{{ __('promo.valid-from') }}</label><label style="color:red">* </label>
                                        <input type="date" class="flex form-control"  id="valid_from">
                                </div>
                                <div class="form-group" id="divPassword" style="display:block">
                                    <label for="password" class="col-form-label" >{{ __('promo.valid-to') }}</label><label style="color:red">* </label>
                                        <input type="date" class="flex form-control" id="valid_to">
                                </div>
                                <div class="form-group">
                                    <label for="email" class="col-form-label">{{ __('promo.description') }}</label><label style="color:red">* </label>
                                    <textarea class="form-control" id="description"  rows="3" placeholder="Enter Description"></textarea>
                                </div>
                                <div class="form-group">
                                <input type="hidden" class="form-control" name="created_by" id="created_by" value="{{session()->get('full_name')}}">
                                    <input type="hidden" class="form-control" name="changed_by" id="changed_by" value="{{session()->get('full_name')}}">
                                </div>
                                <div class="form-group">
                                    <label for="txtIsPromo">Enable this Promo for all Menu</label><br>
                                    <input type="checkbox" id="chkPromoAll" class="form-control" name="chkPromoAll" data-toggle="toggle" data-style="ios" data-offstyle="danger">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                @csrf
                    @if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-promo-edit") || Auth::user()->permission("outlet-promo-create"))
                    <button type="button" id="btnSave" class="btn btn-primary">{{ __('button.save') }}</button>
                    @endif
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('button.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script>

    $(document).ready(function() {
        dataTable();            
         var modalClose = $('#promoModal').on('hide.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var modal = $(this)
                $('#id_promo').val("")
                $('#name').val("");
                $('#value').val("");
                $('#max_discount').val("");
                $('#valid_form').val("");
                $('#valid_to').val("");
                $('#description').val(""); 
                $('#nameOutlet').val("");
                var validTo = document.querySelector('input[id="valid_to"]');
            validTo.value = "";
            var validFrom = document.querySelector('input[id="valid_from"]');
            validFrom.value = "";
        });
    });       

    function dataTable(data){
        $('#promoTable').DataTable({
            // data: data,
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
        }).columns.adjust()
            $('#promoTable').on( 'column-sizing.th', function ( e, settings ) {
            console.log( 'Column width recalculated in table' );
            });        
    } 

    function refreshTable()
    {
        var role = '{{ Session::get("role") }}';
        if(role.toLowerCase() == 'admin'){
            var url = 'promo/get_data';
        } else {
            var url = 'promo/get_data_with_user';
        }
        $.get(url,function (data)
        {
            var table = $('#promoTable').DataTable();
            table.destroy();
                // console.log(data);
                var i = 1;
                var role = "";
                $('#TbodyPromo').empty();
                $.each(data, function(index, data)
                {
                    if(data.updated_by == null){
                        var updated_by = '';
                    }else{
                        var updated_by = data.updated_by;
                    }
                    $('#TbodyPromo').append(
                    '<tr id="'+data.id+'">'+
                        '<td>'+i+'</td>'+
                        '<td class="px-4 py-2" style="text-align: center;">'+
                        '@if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-promo-edit"))'+
                        '<button id="edit" onclick="getEdit('+data.id+')" data-backdrop="static" data-keyboard="false"><i class="fas fa-pen"></i></button>'+
                        '&nbsp;&nbsp;&nbsp;'+
                        '@endif'+
                        '@if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-promo-delete"))'+
                        '<button id="delete" onclick="deleteData('+data.id+')"><i class="fas fa-trash"></i></button>'+
                        '@endif'+
                        '</td>'+
                        '<td>'+data.name+'</td>'+
                        '<td>'+data.description+'</td>'+
                        '<td>'+data.value+'</td>'+
                        '<td>'+data.max_discount_price+'</td>'+
                        '<td>'+data.name_outlet+'</td>'+
                        '<td>'+data.valid_from+'</td>'+
                        '<td>'+data.valid_to+'</td>'+
                        '<td>'+data.created_by+'</td>'+
                        '<td>'+data.created_at+'</td>'+
                        '<td>'+updated_by+'</td>'+
                        '<td>'+data.updated_at+'</td>'+
                    '</tr>');
                    i += 1
                });
                // dataTable();
                    $('#promoTable').DataTable({
                        // data: data,
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
                    });
                    $('#promoTable').on( 'column-sizing.th', function ( e, settings ) {
                        console.log( 'Column width recalculated in table' );
                        });
        });
    }

    function getEdit(id){
        // console.log(id);
        $('#promoModal').modal('show');
        var modal = $('#promoModal');
            modal.find('.modal-title').text('Edit Master Promo');
        $.get('promo/get_promo_id/'+id,function (data){
            // console.log(data);
            $('#id_promo').val(data.id);
            $('#name').val(data.name);
            $('#value').val(data.value);
            $('#max_discount').val(data.max_discount_price);
            $('#description').val(data.description);
            // $('#nameOutlet').val(data.name_outlet);
            var validTo = document.querySelector('input[id="valid_to"]');
            validTo.value = data.valid_to;
            var validFrom = document.querySelector('input[id="valid_from"]');
            validFrom.value = data.valid_from;
            getDataOutlet(data.name_outlet);
        });
    }

    function addData(){
        $('#promoModal').modal('show');
        var modal = $('#promoModal');
            modal.find('.modal-title').text('Add Master Promo');
        getDataOutlet();
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
                        var data = {
                        id : id,
                        }
            // console.log(data);
            $.ajaxSetup({
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                        });
            $.ajax({
                    type: 'POST',
                    url: 'promo/delete_promo',
                    data: data,
                    success:function(request)
                    {
                    if(request.status == true){
                        alert(request.message);
                        refreshTable();
                        // setTimeout(location.reload.bind(location), 2800);
                        }else{
                        alertErorr(request.message);
                        }
                    }
                    });
        }
        });
    }

    function getDataOutlet(request){
            url = 'master/fnboutlet/get_outlet_all';
            // var outlet = $('#nameOutlet').val();
            var outlet = request;
                // var table = $('#outletTable').DataTable();
                // console.log(request);
                // console.log(data);
                $('#idOutletModal').empty();
                $('#idOutletModal').selectpicker('destroy');
                $('#idOutletModal').append('<option value="">-- Select Outlet --</option>');
        $.get(url, function (data){
                $.each(data, function(index, data)
                {
                    
                    if(outlet ==data.name){  
                    $('#idOutletModal').append('<option value="'+data.id+'" selected>'+data.name+' - '+data.hotel_name+'</option>');
                    }else{
                    $('#idOutletModal').append('<option value="'+data.id+'">'+data.name+' - '+data.hotel_name+'</option>');
                    }
                    $('#idOutletModal').selectpicker('refresh');
                    
                });
            });
    } 

    $('#value').on('input',function()
    {
        if (this.value.length > 3) {
            this.value = this.value.slice(0,3);     
            alertModal("Value must be less than or equal to 999");
        }
    });

    $('#max_discount').on('input',function()
    {
        if (this.value.length > 8) {
            this.value = this.value.slice(0,8);     
            alertModal("Value must be less than or equal to 99999999");
        }
    });

    // add and edit
    $('#btnSave').on('click',function()
    {
        document.getElementById("btnSave").disabled = true;
        var from = $("#valid_from").val();
        var to = $("#valid_to").val();
        if ($('#chkPromoAll').is(":checked"))
        {
            var checkPromoAll = 1;
        } else {
            var checkPromoAll = 0;
        }

        if(Date.parse(from) > Date.parse(to)){
            alertModal("Invalid Date Range");
            document.getElementById("btnSave").disabled = false;
        // alertModal(request.message);
        }else{
            var id =  $('#id_promo').val();
            if (id == '')
            {
                var data = {
                name : $('#name').val(),
                description : $('#description').val(),
                value : $('#value').val(),
                max_discount_price : $('#max_discount').val(),
                valid_from : $('#valid_from').val(),
                valid_to : $('#valid_to').val(),
                created_by : $('#created_by').val(),
                fboutlet_id : $('#idOutletModal').val(),
                chkPromoAll : checkPromoAll
                }
                // console.log(data);
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: 'POST',
                        url: 'promo/save_promo',
                        data: data,
                    success:function(request)
                {
                    if(request.status == true){
                        console.log(request.message);
                        alert(request.message);
                        $('#promoModal').modal('hide');
                        refreshTable();
                        document.getElementById("btnSave").disabled = false;
                        // setTimeout(location.reload.bind(location), 2000);
                    }else{
                        alertModal(request.message);
                        document.getElementById("btnSave").disabled = false;
                    }
                },
                  error: function(xhr, status, error){
                    // console.log(request.message);
                    alertModal('Opps Something Wrong');
                    document.getElementById("btnSave").disabled = false;
                  }
                });
            }
            else
            {
                var data = {
                id : $('#id_promo').val(),
                name : $('#name').val(),
                description : $('#description').val(),
                value : $('#value').val(),
                max_discount_price : $('#max_discount').val(),
                valid_from : $('#valid_from').val(),
                valid_to : $('#valid_to').val(),
                updated_by : $('#created_by').val(),
                fboutlet_id : $('#idOutletModal').val(),
                chkPromoAll : checkPromoAll
                }
                $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: 'POST',
                        url: 'promo/edit_promo',
                        data: data,
                    success:function(request)
                {
                    if(request.status == true){
                        // console.log(request.message);
                        alert(request.message);
                        $('#promoModal').modal('hide');
                        refreshTable()
                        document.getElementById("btnSave").disabled = false;
                        // setTimeout(location.reload.bind(location), 2000);
                    }else{
                        alertModal(request.message);
                        document.getElementById("btnSave").disabled = false;
                    }
                },
                  error: function(xhr, status, error){
                    alertModal('Opps Something Wrong');
                    document.getElementById("btnSave").disabled = false;
                  }
                });
            }
         }
    });

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

    $('#idHotel').on('change',function(e) {
            // console.log(e.target.value);
            var i = 1;
            var hotel_id =  e.target.value;
            // console.log('hotel_id'+hotel_id);
            var active = ""; var url = "";
            var updated_by = ""; var updated_at = "";
            var user_id = '{{ Session::get("id") }}';
            var role = '{{ Session::get("role") }}';

            if(role.toLowerCase() == 'admin'){
                if(hotel_id == 0){
                    location.reload();
                    // url = 'promo/get_data';
                } else {
                    url = 'promo/get_promo_with_hotel_user/' + hotel_id;
                }
            } else {
                if(hotel_id == 0){
                    location.reload();
                    // url = 'promo/get_data_with_user';
                } else {
                    url = 'promo/get_promo_with_hotel_user/' + hotel_id + '/' + user_id;
                }
            }

            $.get(url, function (data)
            {
                var table = $('#promoTable').DataTable();
                table.destroy();
                $('#idOutlet').empty();
                $('#idOutlet').selectpicker('destroy');
                $('#TbodyPromo').empty();
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

                    $('#TbodyPromo').append(
                    '<tr id="'+data.id+'">'+
                        '<td>'+i+'</td>'+
                        '<td class="px-4 py-2" style="text-align: center;">'+
                        '@if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-promo-edit"))'+
                        '<button id="edit" onclick="getEdit('+data.id+')" data-backdrop="static" data-keyboard="false"><i class="fas fa-pen"></i></button>'+
                        '&nbsp;&nbsp;&nbsp;'+
                        '@endif'+
                        '@if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-promo-delete"))'+
                        '<button id="delete" onclick="deleteData('+data.id+')"><i class="fas fa-trash"></i></button>'+
                        '@endif'+
                        '</td>'+
                        '<td>'+data.name+'</td>'+
                        '<td>'+data.description+'</td>'+
                        '<td>'+data.value+'</td>'+
                        '<td>'+data.max_discount_price+'</td>'+
                        '<td>'+data.name_outlet+'</td>'+
                        '<td>'+data.valid_from+'</td>'+
                        '<td>'+data.valid_to+'</td>'+
                        '<td>'+data.created_by+'</td>'+
                        '<td>'+data.created_at+'</td>'+
                        '<td>'+updated_by+'</td>'+
                        '<td>'+data.updated_at+'</td>'+
                    '</tr>');
                    i += 1
                });

                $.each(data.dataOutlet, function(index, data)
                {
                    $('#idOutlet').append('<option value="'+data.id+'">'+data.name+'</option>');
                    $('#idOutlet').selectpicker('refresh');
                });
                                
                $('#promoTable').DataTable({
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

                $('#promoTable').on( 'column-sizing.th', function ( e, settings ) {
                    console.log( 'Column width recalculated in table' );
                });
               
            });
           
    }); 

    $('#idOutlet').on('change',function(e) {
            // console.log(e.target.value);
            var i = 1;
            var outlet_id =  e.target.value;
            var active = ""; var url = "";
            var updated_by = ""; var updated_at = "";
            var datax = []; var dataz = [];
            var user_id = '{{ Session::get("id") }}';
            var role = '{{ Session::get("role") }}';

            if(role.toLowerCase() == 'admin'){
                if(outlet_id == 0){
                    location.reload();
                    // url = 'promo/get_data';
                } else {
                    url = 'promo/get_promo_with_outlet_user/' + outlet_id;
                }
            } else {
                if(outlet_id == 0){
                    location.reload();
                    // url = 'promo/get_data_with_user';
                } else {
                    url = 'promo/get_promo_with_outlet_user/' + outlet_id + '/' + user_id;
                }
            }

            $.get(url, function (data)
            {
                var table = $('#promoTable').DataTable();
                table.destroy();
                $('#TbodyPromo').empty();
                
                // console.log(data);
                $.each(data, function(index, data)
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

                    datax[index] = data.id_outlet;
                    dataz[index] = data.name_outlet;

                    $('#TbodyPromo').append(
                    '<tr id="'+data.id+'">'+
                        '<td>'+i+'</td>'+
                        '<td class="px-4 py-2" style="text-align: center;">'+
                        '@if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-promo-edit"))'+
                        '<button id="edit" onclick="getEdit('+data.id+')" data-backdrop="static" data-keyboard="false"><i class="fas fa-pen"></i></button>'+
                        '&nbsp;&nbsp;&nbsp;'+
                        '@endif'+
                        '@if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-promo-delete"))'+
                        '<button id="delete" onclick="deleteData('+data.id+')"><i class="fas fa-trash"></i></button>'+
                        '@endif'+
                        '</td>'+
                        '<td>'+data.name+'</td>'+
                        '<td>'+data.description+'</td>'+
                        '<td>'+data.value+'</td>'+
                        '<td>'+data.max_discount_price+'</td>'+
                        '<td>'+data.name_outlet+'</td>'+
                        '<td>'+data.valid_from+'</td>'+
                        '<td>'+data.valid_to+'</td>'+
                        '<td>'+data.created_by+'</td>'+
                        '<td>'+data.created_at+'</td>'+
                        '<td>'+updated_by+'</td>'+
                        '<td>'+data.updated_at+'</td>'+
                    '</tr>');
                    i += 1
                });
                
                $('#promoTable').DataTable({
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

                $('#promoTable').on( 'column-sizing.th', function ( e, settings ) {
                    console.log( 'Column width recalculated in table' );
                });
               
            });
           
    }); 

</script>
</x-app-layout>
