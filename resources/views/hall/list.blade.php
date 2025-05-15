<meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Master Hall</title>
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
		Master Hall
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
                <select class="form-control md-6 selectpicker" id="idHotel" data-size="5" data-live-search="true">
                    <option value="">--{{ __('combobox.select-hotel') }}--</option>
                @foreach ($dataHotel as $dataz)
                    <option value="{{ $dataz['id'] }}">{{ $dataz['hotel_name'] }}</option>
                @endforeach
                </select>
                </div>
            </div>
          
        </div>
        @if((session()->get('role')=='Admin') || Auth::user()->permission('mice-hall-create'))
        <a href="{{ route('hall.new') }}" class="btn  btn-primary float-right">{{ __('button.add-hall') }}</a>
        @endif
        <br>
        <br>
            <table class="table-auto table-bordered" id="miceTable" class="display" >
                <thead id="tableHeadMice">
                    <tr class="bg-gray-100" style="text-align:center" valign="middle" >
                        <th class="px-2">No</th>
                        <th class="px-4">{{ __('outlet.action') }}</th>
                        <th class="px-4 w-10">{{ __('outlet.hotel-name') }}</th>
                        <th class="px-4 w-10">{{ __('hall.hall-name') }}</th>
                        <th class="px-4">{{ __('hall.hall-category') }}</th>
                        {{-- <th class="px-4">{{ __('outlet.description') }}</th> --}}
                        <th class="px-4">{{ __('hall.hall-capacity') }}</th>
                        <th class="px-4">{{ __('hall.hall-size') }}</th>
                        <th class="px-4">Layout</th>
                        <th class="px-4">Offers</th>
                        <th class="px-5">{{ __('outlet.seq-no') }}</th>
                        <th class="px-5">{{ __('outlet.created-by') }}</th>
                        <th class="px-5 text-center">{{ __('outlet.created-at') }}</th>
                        <th class="px-5">{{ __('outlet.updated-by') }}</th>
                        <th class="px-5 text-center" >{{ __('outlet.updated-at') }}</th>
                    </tr>
                </thead>
                <tbody id="tableBodyMHall">
                    @foreach ($data as $datax)
                    <?php $dataMice = ""; ?>
                        <tr id="{{ $datax['id'] }}">
                            <td class="text-center">{{$loop->iteration}}</td>
                            <td class="px-4 py-2 text-center">
                                @if((session()->get('role')=='Admin') || Auth::user()->permission('mice-hall-edit'))
                                <a href="get_edit_hall/{{ $datax['id'] }}" ><i class="fas fa-pen"></i></a>
                                    &nbsp;
                                @endif
                                @if((session()->get('role')=='Admin') || Auth::user()->permission('mice-hall-delete'))
                                    <a href="delete_hall/{{ $datax['id'] }}" class="button delete-confirm"><i class="fas fa-trash"></i></a>
                                @endif
                            </td>
                            <td>{{ $datax['hotel_name']}}</td>
                            <td>{{ $datax['name']}}</td>
                            {{-- Category diambil dari Looping --}}
                            <td>
                            <?php 
                                foreach ($dataCategory as $dataz) {
                                    if($dataz['id'] == $datax['id']){
                                        if($dataMice == ""){
                                            $dataMice = $dataz['category_name'];
                                        } else {
                                            $dataMice = $dataMice .', '. $dataz['category_name'];
                                        }
                                        
                                    }
                                }
                                print $dataMice;
                            ?>
                            </td>
                            {{-- Category diambil dari Looping --}}
                            {{-- <td>{!! $datax['descriptions'] !!}</td> --}}
                            <td>{{ $datax['capacity'] }}</td>
                            <td>{{ $datax['size'] }}</td>
                            <td class="text-center">{{ $datax['layout'] }}</td>
                            <td class="text-center">{{ $datax['mice_offers'] }}</td>
                            <td class="text-center">{{ $datax['seq'] }}</td>
                            <td>{{ $datax['created_by'] }}</td>
                            <td class="text-center">{{ $datax['created_at'] }}</td>
                            <td>{{ $datax['updated_by'] }}</td>
                            <td class="text-center">{{ $datax['updated_at'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        <br>
    </div>

    <script>
    onLoadDelete();
    function onLoadDelete() {
        $('.delete-confirm').on('click', function (event) {
            console.log('Delete');
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
    }

    function dataTableLoad(data) {
        $('#miceTable').DataTable({
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

            $('#miceTable').on( 'column-sizing.th', function ( e, settings ) {
            console.log( 'Column width recalculated in table' );
        } );
    }

    $(document).ready(function() {
        
        dataTableLoad();
        
    } );

    $('#idHotel').on('change',function(e){
            // console.log(e.target.value);
            var i = 1;
            var hotel_id =  e.target.value;
            var active = ""; var url = "";
            var dataCat = []; var dataMice = ""; var dataCat = "";
            var updated_by = ""; var updated_at = "";
            var user_id = '{{ Session::get("id") }}';
            var role = '{{ Session::get("role") }}';

            if(role.toLowerCase() == 'admin'){
                if(hotel_id == 0){
                    // url = 'get_hotel_mice'
                    location.reload();
                } else {
                    url = 'get_hotel_hall/' + hotel_id
                }
            } else {
                if(hotel_id == 0){
                    // url = 'get_hotel_mice_with_hotel_user?user_id=' + user_id
                    location.reload();
                } else {
                    url = 'get_hotel_hall/' + hotel_id 
                }
            }
          
            $.get(url, function (datax)
            {
                var table = $('#miceTable').DataTable();
                var index = 0;
                // console.log(datax);
                $('#tableBodyMHall').empty();

                table.clear();
                if(datax['data'].length > 0){
                    $.each(datax['data'], function(index, data)
                    {
                        //console.log(data);
                        var dataMice = "";
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
                        if(data.mice_offers == null){
                            mice_offers = '';
                        } else {
                            mice_offers = data.mice_offers;
                        }
                        // Check Mice Category has same id's
                        for (index = 0; index < datax['dataCategory'].length; ++index) {
                            // console.log(data['dataCategory'][index].category_name);
                            if(datax['dataCategory'][index].id == data.id)
                            {
                                dataCat = datax['dataCategory'][index].category_name;
                                if(dataMice == "")
                                dataMice = dataCat;
                                else
                                dataMice = dataMice + ', ' + dataCat;
                            }
                        }

                        $('#miceTable').append(
                        '<tr id="'+data.id+'">'+
                            '<td class="text-center">'+i+'</td>'+
                            '<td class="px-4 py-2 text-center">'+
                            '@if((session()->get("role")=="Admin") || Auth::user()->permission("mice-hall-edit"))'+
                            '<a href="get_edit_hall/'+data.id+'" ><i class="fas fa-pen"></i></a>'+
                            '&nbsp;&nbsp;'+
                            '@endif'+
                            '@if((session()->get("role")=="Admin") || Auth::user()->permission("mice-hall-delete"))'+
                            '<a href="delete_hall/'+data.id+'" class="button delete-confirm"><i class="fas fa-trash"></i></a>'+
                            '@endif'+
                            '</td>'+
                            '<td class="text-center">'+data.hotel_name+'</td>'+
                            '<td class="text-left">'+data.name+'</td>'+
                            '<td class="text-left">'+dataMice+'</td>'+
                            // '<td class="text-left">'+data.descriptions+'</td>'+
                            '<td>'+data.capacity+'</td>'+
                            '<td>'+data.size+'</td>'+
                            '<td>'+data.layout+'</td>'+
                            '<td>'+mice_offers+'</td>'+
                            '<td class="text-center">'+data.seq+'</td>'+
                            '<td>'+data.created_by+'</td>'+
                            '<td class="text-center">'+data.created_at+'</td>'+
                            '<td>'+updated_by+'</td>'+
                            '<td class="text-center">'+updated_at+'</td>'+
                        '</tr>');
                        i += 1
                        // datax.push(data);
                    });
                } else {
                    $('#miceTable').append(
                            '<tr class="text-center">'+
                            '<td class="text-center" colspan="13">Data Not Found</td>'+
                            '</tr>');
                }
                // dataz['dataz'] = datax;
                onLoadDelete();
                dataTableLoad(datax);
            });

    });
   
</script>
</x-app-layout>
