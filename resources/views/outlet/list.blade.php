<meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Master {{ __('outlet.title') }}</title>
    <style>
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
    </style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
		Master {{ __('outlet.title') }}
        </h2>
    </x-slot>
    <div class="py-12">
    
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div id="app">
        @include('/flash-message')

        @yield('content')
    </div>
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                    <br>
                    <div class="container">
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
                            @if($data != null)
                                @if(Auth::user()->permission('outlet-list'))
                                    @if(count($data) > 1)
                                        @foreach ($data as $datax)
                                            <option value="{{ $datax['id'] }}">{{ $datax['name'] }}</option>
                                        @endforeach
                                    @else
                                        <option value="{{ $data[0]['id'] }}" selected>{{ $data[0]['name'] }}</option>
                                    @endif
                                @else
                                    @foreach ($data as $datax)
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
                            <select class="form-control md-6 selectpicker" id="idOutlet" data-size="4" data-live-search="true" data-container="body">
                                @if(Auth::user()->permission('outlet-list'))
                                <option value="">--{{ __('combobox.select-outlet') }}--</option>
                                    @if($data_outlet != null)
                                        @foreach ($data_outlet as $datax)
                                            <option value="{{ $datax['id'] }}">{{ $datax['name'] }}</option>
                                        @endforeach
                                    @endif
                                @endif
                            </select>
                            </div>
                        </div>
                    </div>
                    @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-create'))
                    <a href="{{ route('outlet.add') }}" class="btn  btn-primary float-right">{{ __('button.add-outlet') }}</a>
                    @endif
                    <br>
                    <br>
                        <table class="table-auto table-bordered" id="outletTable" class="display" >
                            <thead id="tableHeadOutlet">
                                <tr class="bg-gray-100" style="text-align:center" valign="middle" >
                                    <th class="px-2">No</th>
                                    <th class="px-4">{{ __('outlet.action') }}</th>
                                    <th class="px-1 w-10">{{ __('outlet.hotel-name') }}</th>
                                    <th class="px-5 w-10">{{ __('outlet.outlet-name') }}</th>
                                    <th class="px-4">{{ __('outlet.address') }}</th>
                                    <th class="px-5">PG Api Key</th>
                                    <th class="px-5">PG Api Secret</th>
                                    <th class="px-4">Status</th>
                                    <th class="px-5 w-20 text-center">{{ __('outlet.seq-no') }}</th>
                                    <th class="px-4">{{ __('outlet.open_at') }}</th>
                                    <th class="px-4">{{ __('outlet.close_at') }}</th>
                                    <th class="px-5">{{ __('outlet.created-by') }}</th>
                                    <th class="px-5">{{ __('outlet.created-at') }}</th>
                                    <th class="px-5">{{ __('outlet.updated-by') }}</th>
                                    <th class="px-5">{{ __('outlet.updated-at') }}</th>
                                </tr>
                            </thead>
                            <tbody id="tableBodyOutlet">
                            @if($data_outlet != null)
                                @foreach ($data_outlet as $data)
                                    <tr id="{{ $data['id'] }}">
                                        <td>{{$loop->iteration}}</td>
                                        <td class="px-4 py-2 text-center">
                                        @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-edit'))
                                        <a href="fnboutlet/get_edit_outlet/{{ $data['id'] }}" ><i class="fas fa-pen"></i></a>
                                        @endif
                                        &nbsp;
                                        @if((session()->get('role')=='Admin') || Auth::user()->permission('outlet-delete'))
                                        <a href="fnboutlet/delete_outlet/{{ $data['id'] }}" class="button delete-confirm"><i class="fas fa-trash"></i></a>
                                        @endif
                                        </td>
                                        <td>{{$data['hotel_name']}}</td>
                                        <td>{{$data['name']}}</td>
                                        <td>{{ $data['address'] }}</td>
                                        <td>{{ $data['mpg_api_key'] }}</td>
                                        <td>{{ $data['mpg_secret_key'] }}</td>
                                        <td>@if($data['status'] =='1')
                                                Active
                                            @else
                                                Not Active
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $data['seq_no'] }}</td>
                                        <td class="text-center">{{ $data['open_at'] }}</td>
                                        <td class="text-center">{{ $data['close_at'] }}</td>
                                        <td>{{ $data['created_by'] }}</td>
                                        <td class="text-center">{{ $data['created_at'] }}</td>
                                        <td>{{ $data['updated_by'] }}</td>
                                        <td class="text-center">{{ $data['updated_at'] }}</td>
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

    <script>
    onLoadDelete();
    function onLoadDelete() {
        $('.delete-confirm').on('click', function (event) {
            console.log('Delete');
            event.preventDefault();
            const url = $(this).attr('href');
            swal({
                title: '{{__('message.are_you_sure')}}',
                text: '{{__('message.delete_not_permanent')}}',
                icon: 'warning',
                buttons: ["{{__('message.btn_delete_cancel')}}", "{{__('message.btn_delete_yes')}}"],
            }).then(function(value) {
                if (value) {
                    window.location.href = url;
                }
            });
        });
    }

    function dataTableLoad(data) {
        $('#outletTable').DataTable({
            data: data,
            scrollX: true,
            scrollY:"480px",
            scrollCollapse: true,
            pageLength: 10,
            info: false,
            ordering: false,
            autoWidth: false,
            retrieve: true,
            destroy: true,
            searching: false,
            columnDefs: [
                { width: "50%", targets: 2 }
            ]
        }).columns.adjust()
        //  .responsive.recalc()

            $('#outletTable').on( 'column-sizing.th', function ( e, settings ) {
            console.log( 'Column width recalculated in table' );
        } );
    }
    $(document).ready(function() {
        
        dataTableLoad();
        
    });

    $('#idHotel').on('change',function(e){
            // console.log(e.target.value);
            var i = 1;
            var hotel_id =  e.target.value;
            console.log('hotel_id'+hotel_id);
            var active = ""; var url = "";
            var updated_by = ""; var updated_at = "";
            var datax = []; var dataz = [];
            var user_id = '{{ Session::get("id") }}';
            var role = '{{ Session::get("role") }}';

            if(role.toLowerCase() == 'admin'){
                if(hotel_id == 0){
                    url = 'fnboutlet/get_outlet_all'
                } else {
                    url = 'fnboutlet/get_hotel_outlet/' + hotel_id
                }
            } else {
                if(hotel_id == 0){
                    url = 'fnboutlet/get_outlet_all_with_user_outlet'
                } else {
                    url = 'fnboutlet/get_hotel_outlet_with_user/' + hotel_id + '/' + user_id
                }
            }
            // console.log(url);
            $.get(url, function (data)
            {
                var table = $('#outletTable').DataTable();
                table.destroy();
                $('#idOutlet').empty();
                $('#idOutlet').selectpicker('destroy');
                $('#tableBodyOutlet').empty();
                $('#idOutlet').append('<option value="">-- Select Outlet --</option>');

                
                $.each(data, function(index, data)
                {
                    //console.log(data);
                    if(data.status == 1){
                        active = "Active";
                    } else {
                        active = "Not Active";
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
                    $('#idOutlet').append('<option value="'+data.id+'">'+data.name+'</option>');
                    $('#idOutlet').selectpicker('refresh');
                    $('#tableBodyOutlet').append(
                    '<tr id="'+data.id+'">'+
                            '<td>'+i+'</td>'+
                            '<td class="px-4 py-2 text-center">'+
                            '@if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-edit"))'+
                            '<a href="fnboutlet/get_edit_outlet/'+data.id+'" ><i class="fas fa-pen"></i></a>&nbsp;&nbsp;'+
                            '&nbsp;'+
                            '@endif'+
                            '@if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-delete"))'+
                            '<a href="fnboutlet/delete_outlet/'+data.id+'" class="button delete-confirm"><i class="fas fa-trash"></i></a>'+
                            '@endif'+
                            '</td>'+
                            '<td>'+data.hotel_name+'</td>'+
                            '<td>'+data.name+'</td>'+
                            '<td>'+substring(data.address)+'</td>'+
                            '<td>'+data.mpg_api_key+'</td>'+
                            '<td>'+data.mpg_secret_key+'</td>'+
                            '<td>'+active+'</td>'+
                            '<td class="text-center">'+data.seq_no+'</td>'+
                            '<td>'+data.open_at+'</td>'+
                            '<td>'+data.close_at+'</td>'+
                            '<td>'+data.created_by+'</td>'+
                            '<td>'+data.created_at+'</td>'+
                            '<td>'+updated_by+'</td>'+
                            '<td>'+updated_at+'</td>'+
                    '</tr>');
                    i += 1
                    // datax.push(data);
                });
                $('#outletTable').DataTable({
                        // data: data,
                        scrollX: true,
                        scrollY:"480px",
                        scrollCollapse: true,
                        pageLength: 10,
                        info: false,
                        ordering: false,
                        autoWidth: false,
                        retrieve: true,
                        destroy: true,
                        searching: false,
                        columnDefs: [
                            { width: "50%", targets: 2 }
                        ]
                    }).columns.adjust()
                    //  .responsive.recalc()

                        $('#outletTable').on( 'column-sizing.th', function ( e, settings ) {
                        console.log( 'Column width recalculated in table' );
                    } );
                // dataz['dataz'] = datax;
                onLoadDelete();
                // dataTableLoad(data);
            });

    });

    $('#idOutlet').on('change',function(e){
            // console.log(e.target.value);
            var id_hotel = document.getElementById("idHotel").value;
            // var id_hotel = e.value;
            // console.log(a);
            var i = 1;
            var outlet_id =  e.target.value;
            var active = "";
            if(outlet_id == 0){
                reloadTableHotel(id_hotel)
            }else{
                    $.get('fnboutlet/get_outlet_detail/' + outlet_id, function (data)
                {
                    // console.log(data);
                    $('#tableBodyOutlet').empty();
                    
                    // console.log(data);
                    if(data.status == '1'){
                        active = "Active";
                    } else {
                        active = "Not Active";
                    }
            
                    $('#tableBodyOutlet').append(
                    '<tr id="'+data.id+'">'+
                        '<td>'+i+'</td>'+
                            '<td class="px-4 py-2 text-center">'+
                            '@if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-edit"))'+
                            '<a href="fnboutlet/get_edit_outlet/'+data.id+'" ><i class="fas fa-pen"></i></a>&nbsp;&nbsp;'+
                            '&nbsp;'+
                            '@endif'+
                            '@if((session()->get("role")=="Admin") || Auth::user()->permission("outlet-delete"))'+
                            '<a href="fnboutlet/delete_outlet/'+data.id+'" class="button delete-confirm"><i class="fas fa-trash"></i></a>'+
                            '@endif'+
                            '</td>'+
                            '<td>'+data.hotel_name+'</td>'+
                            '<td>'+data.name+'</td>'+
                            '<td>'+substring(data.address)+'</td>'+
                            '<td>'+data.mpg_api_key+'</td>'+
                            '<td>'+data.mpg_secret_key+'</td>'+
                            '<td>'+active+'</td>'+
                            '<td>'+data.seq_no+'</td>'+
                            '<td>'+data.open_at+'</td>'+
                            '<td>'+data.close_at+'</td>'+
                            '<td>'+data.created_by+'</td>'+
                            '<td>'+data.created_at+'</td>'+
                            '<td>'+data.updated_by+'</td>'+
                            '<td>'+data.updated_at+'</td>'+
                    '</tr>');
                    i += 1
                    onLoadDelete(); 
                    dataTableLoad(data);
                });
            }
    });

    function reloadTableHotel(id){

        // console.log(e.target.value);
        var i = 1;
            var hotel_id =  id;
            console.log('hotel_id'+hotel_id);
            var active = ""; var url = "";
            var updated_by = ""; var updated_at = "";
            var datax = []; var dataz = [];
            var user_id = '{{ Session::get("id") }}';
            var role = '{{ Session::get("role") }}';

            if(role.toLowerCase() == 'admin'){
                if(hotel_id == 0){
                    url = 'fnboutlet/get_outlet_all'
                } else {
                    url = 'fnboutlet/get_hotel_outlet/' + hotel_id
                }
            } else {
                if(hotel_id == 0){
                    url = 'fnboutlet/get_outlet_all_with_user_outlet'
                } else {
                    url = 'fnboutlet/get_hotel_outlet_with_user/' + hotel_id + '/' + user_id
                }
            }
            // console.log(url);
            //$.get('/outlet/get_hotel_outlet/' + hotel_id, function (data)
            $.get(url, function (data)
            {
                var table = $('#outletTable').DataTable();
                table.destroy();
                // $('#idOutlet').empty();
                // $('#idOutlet').selectpicker('destroy');
                $('#tableBodyOutlet').empty();
                // $('#idOutlet').append('<option value="">-- Select Outlet --</option>');

                
                $.each(data, function(index, data)
                {
                    //console.log(data);
                    if(data.status == 1){
                        active = "Active";
                    } else {
                        active = "Not Active";
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
                    // $('#idOutlet').append('<option value="'+data.id+'">'+data.name+'</option>');
                    // $('#idOutlet').selectpicker('refresh');
                    $('#tableBodyOutlet').append(
                    '<tr id="'+data.id+'">'+
                        '<td>'+i+'</td>'+
                         '<td class="px-4 py-2 text-center">'+
                         '<a href="fnboutlet/get_edit_outlet/'+data.id+'" ><i class="fas fa-pen"></i></a>'+
                                    '&nbsp;'+
                                    '@if(strtolower(session()->get("role")) == "admin")'+
                                    '<a href="fnboutlet/delete_outlet/'+data.id+'" class="button delete-confirm"><i class="fas fa-trash"></i></a>'+
                                    '@endif'+
                                    '</td>'+
                                    '<td>'+data.hotel_name+'</td>'+
                                    '<td>'+data.name+'</td>'+
                                    '<td>'+substring(data.address)+'</td>'+
                                    '<td>'+data.mpg_api_key+'</td>'+
                                    '<td>'+data.mpg_secret_key+'</td>'+
                                    '<td>'+active+'</td>'+
                                    '<td class="text-center">'+data.seq_no+'</td>'+
                                    '<td>'+data.open_at+'</td>'+
                                    '<td>'+data.close_at+'</td>'+
                                    '<td>'+data.created_by+'</td>'+
                                    '<td>'+data.created_at+'</td>'+
                                    '<td>'+updated_by+'</td>'+
                                    '<td>'+updated_at+'</td>'+
                                '</tr>');
                    i += 1
                    // datax.push(data);
                });
                $('#outletTable').DataTable({
                        // data: data,
                        scrollX: true,
                        scrollY:"480px",
                        scrollCollapse: true,
                        pageLength: 10,
                        info: false,
                        ordering: false,
                        autoWidth: false,
                        retrieve: true,
                        destroy: true,
                        searching: false,
                        columnDefs: [
                            { width: "50%", targets: 2 }
                        ]
                    }).columns.adjust()
                    //  .responsive.recalc()

                        $('#outletTable').on( 'column-sizing.th', function ( e, settings ) {
                        console.log( 'Column width recalculated in table' );
                    } );
                // dataz['dataz'] = datax;
                onLoadDelete();
                // dataTableLoad(data);
    });
}
    
    function substring(text){
        var output;
        if (text.length >= 20){
            output = text.substr(0,20) + '...'
            return(output);
        }else{
            output = text
            return(output);
        }
    }
</script>
</x-app-layout>
