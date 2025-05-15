<meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Master MICE Category</title>
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
		Master MICE Category
        </h2>
    </x-slot>
    <div class="py-12">
    
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div id="app">
        @include('/flash-message')
        @if(isset($message))
         <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>    
            {{ $message }}
        </div>
  @endif
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
                    @if(session()->get('role')=='Admin')
                    <option value="{{ $dataz['id'] }}">{{ $dataz['hotel_name'] }}</option>
                    @else
                    <option value="{{ $dataz['id'] }}">{{ $dataz['name'] }}</option>
                    @endif
                @endforeach
                </select>
                </div>
            </div>
          
        </div>
        @if((session()->get('role')=='Admin') || Auth::user()->permission('mice-category-create'))
        <a href="{{ route('mice_category.new') }}" class="btn  btn-primary float-right">{{ __('button.add-mice') }}</a>
        @endif
        <br>
        <br>
            <table class="table-auto table-bordered" id="miceTable" class="display" >
                <thead id="tableHeadMice">
                    <tr class="bg-gray-100" style="text-align:center" valign="middle" >
                        <th class="px-2">No</th>
                        <th class="px-4">{{ __('outlet.action') }}</th>
                        <th class="px-1 w-10">{{ __('outlet.hotel-name') }}</th>
                        <th class="px-4">{{ __('outlet.category') }}</th>
                        {{-- <th class="px-4">{{ __('outlet.description') }}</th> --}}
                        <th class="px-5 text-center">{{ __('outlet.created-by') }}</th>
                        <th class="px-5 text-center">{{ __('outlet.created-at') }}</th>
                        <th class="px-5">{{ __('outlet.updated-by') }}</th>
                        <th class="px-5">{{ __('outlet.updated-at') }}</th>
                    </tr>
                </thead>
                <tbody id="tableBodyMice">
                    @foreach ($data as $datax)
                        <tr id="{{ $datax['id'] }}">
                            <td class="text-center">{{$loop->iteration}}</td>
                            <td class="px-4 py-2 text-center">
                                @if((session()->get('role')=='Admin') || Auth::user()->permission('mice-category-edit'))
                                <a href="get_edit_mice_category/{{ $datax['id'] }}" ><i class="fas fa-pen"></i></a>
                                    &nbsp;&nbsp;&nbsp;
                                @endif
                                @if((session()->get('role')=='Admin') || Auth::user()->permission('mice-category-delete'))
                                    <a href="delete_mice_category/{{ $datax['id'] }}" class="button delete-confirm"><i class="fas fa-trash"></i></a>
                                @endif
                            </td>
                            <td>{{ $datax['hotel_name']}}</td>
                            <td>{{ $datax['category_name']}}</td>
                            {{-- <td>{{ $datax['descriptions'] }}</td> --}}
                            <td>{{ $datax['created_by'] }}</td>
                            <td class="text-center" style="text-align:center;">{{ $datax['created_at'] }}</td>
                            <td>{{ $datax['updated_by'] }}</td>
                            <td class="text-center" style="text-align:center;">{{ $datax['updated_at'] }}</td>
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

    function dataTableLoad() {
        $('#miceTable').DataTable({
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
            var updated_by = ""; var updated_at = "";
            var datax = []; var dataz = [];
            var user_id = '{{ Session::get("id") }}';
            var role = '{{ Session::get("role") }}';
            console.log(user_id);

            if(role.toLowerCase() == 'admin'){
                if(hotel_id == 0){
                    // url = 'get_all_mice_category'
                    location.reload();
                } else {
                    url = 'get_all_mice_category_with_hotel/' + hotel_id
                }
            } else {
                if(hotel_id == 0){
                    // url = 'get_all_mice_category?user_id=' + user_id
                    location.reload();
                } else {
                    url = 'get_all_mice_category_with_hotel/' + hotel_id + '/' + user_id
                }
            }
          
            $.get(url, function (data)
            {
                var table = $('#miceTable').DataTable();
                table.destroy();
                // console.log(data);
                $('#tableBodyMice').empty();

                table.clear();
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
                    $('#tableBodyMice').append(
                    '<tr id="'+data.id+'">'+
                        '<td>'+i+'</td>'+
                        '<td class="px-4 py-2 text-center">'+
                        '@if((session()->get("role")=="Admin") || Auth::user()->permission("mice-category-edit"))'+
                         '<a href="get_edit_mice_category/'+data.id+'" ><i class="fas fa-pen"></i></a>'+
                                    '&nbsp;&nbsp;&nbsp;'+
                        '@endif'+
                        '@if((session()->get("role")=="Admin") || Auth::user()->permission("mice-category-delete"))'+
                        '<a href="delete_mice_category/'+data.id+'" class="button delete-confirm"><i class="fas fa-trash"></i></a>'+
                        '@endif'+
                        '</td>'+
                        '<td>'+data.hotel_name+'</td>'+
                        '<td>'+data.category_name+'</td>'+
                        '<td>'+data.created_by+'</td>'+
                        '<td class="text-center">'+data.created_at+'</td>'+
                        '<td>'+updated_by+'</td>'+
                        '<td class="text-center">'+updated_at+'</td>'+
                    '</tr>');
                    i += 1
                    datax.push(data);
                });
                // dataz['dataz'] = datax;
                onLoadDelete();
                dataTableLoad();
            });

    });
   
</script>
</x-app-layout>
