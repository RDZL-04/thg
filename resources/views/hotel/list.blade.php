<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Master Hotel</title>
<!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous"> -->
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"> -->
<!-- <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}"> -->
<!-- <script src="{{ asset('js/bootstrap.min.js') }}"></script> -->
<!-- <script src="{{ asset('js/bootstrap-select.min.js') }}"></script> -->
    <!-- <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.css"/> -->
    <!-- <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> -->
    <style>
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
     /* Safari */
     @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
        }
    th, td {
        white-space: nowrap;
    }

    div.dataTables_wrapper {
        width: 100%;
        margin: 0 auto;
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
		{{ __('hotel.title') }}
        </h2>
    </x-slot>


    <div class="py-12">
    
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div id="app">
        @include('/flash-message')


        @yield('content')
        
    </div>
    <div class="alert alert-success alert-block " id="snackbar" style ="display:none ">
                </div>
                <div class="alert alert-danger alert-block " id="snackbarErorr" style ="display:none ">
                </div>
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                    <br>
                    <center>
                    <div class="max-w-5xl mx-auto sm:px-6 lg:px-4" id="loader"></div>
                    </center>
                    
                    <div class="max-w-5xl mx-auto sm:px-6 lg:px-4" id="table" >
                    @if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-create'))
                    <a href="{{ route('hotel.add') }}" class="btn  btn-primary float-right">{{ __('button.add-hotel') }}</a>
                    @endif
                    <br><br>
                        <table class="table-bordered" id="hotelTable" class="display" style="width:100%">
                            <thead>
                                <tr class="bg-gray-100" style="text-align:center" valign="middle" >
                                    <th class="px-2">{{ __('hotel.no') }}</th>
								@if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-update') || Auth::user()->permission('hotel-delete'))
                                    <th class="px-4 w-10">{{ __('hotel.action') }}</th>
								@endif
                                    <th class="px-5 w-10">{{ __('hotel.hotel-name') }}</th>
                                    <th class="px-5 w-10">{{ __('hotel.description') }}</th>
                                    <th class="px-4 w-10">{{ __('hotel.star') }}</th>
                                    <th class="px-1 w-10">{{ __('hotel.booking-hotel-id') }}</th>
                                    <th class="px-3 w-10">{{__('hotel.pg-api-key') }}</th>
                                    <th class="px-5 w-10">{{__('hotel.pg-api-secreet') }}</th>
                                    <th class="px-4 w-10">{{__('hotel.status') }}</th>
                                    <th class="px-5 w-10">{{__('hotel.created-by') }}</th>
                                    <th class="px-5 w-10">{{__('hotel.created-at') }}</th>
                                    <th class="px-5 w-10">{{__('hotel.updated-by') }}</th>
                                    <th class="px-5 w-10">{{__('hotel.updated-at') }}</th>

                                </tr>
                            </thead>
                            <tbody id="TbodyHotel">
                            @if($data != null)
                                @foreach ($data as $data)
                                    <tr id="{{ $data['id'] }}">
                                        <td  style="text-align:center" valign="middle" >{{$loop->iteration}}</td>
									@if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-update') || Auth::user()->permission('hotel-delete'))
                                        <td class="px-4 py-2">
										@if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-update'))
                                        <a href="get_edit_hotel/{{ $data['id'] }}" ><i class="fas fa-pen"></i></a>
										@endif
                                        &nbsp;&nbsp;&nbsp;
                                        @if (session()->get('role')=='Admin' || Auth::user()->permission('hotel-delete'))
                                        <a onclick="deleteData({{ $data['id'] }})" class="button delete-confirm" id="delete"><i class="fas fa-trash"></i></a>
                                        @endif
                                        
                                        </td>
									@endif
                                        <td>{{$data['name']}}</td>
                                        <td>{{$data['description']}}</td>
                                        <td  style="text-align:center" valign="middle" >{{ $data['hotel_star'] }}</td>
                                        <td>{{ $data['be_hotel_id'] }}</td>
                                        <td>{{ $data['mpg_api_key'] }}</td>
                                        <td>{{ $data['mpg_secret_key'] }}</td>
                                        <td>@if($data['status'] =='1')
                                                Active
                                            @else
                                                Not Active
                                            @endif
                                        </td>
                                        <td>{{ $data['created_by'] }}</td>
                                        <td style="text-align:center" valign="middle">{{ $data['created_at'] }}</td>
                                        <td>{{ $data['updated_by'] }}</td>
                                        <td style="text-align:center" valign="middle">{{ $data['updated_at'] }}</td>
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

<script>
    //         $('.delete-confirm').on('click', function (event) {
    //     event.preventDefault();
    //     const url = $(this).attr('href');
    //     swal({
    //         title: 'Are you sure?',
    //         text: 'This record and it`s details will be permanently deleted!',
    //         icon: 'warning',
    //         buttons: ["Cancel", "Yes!"],
    //     }).then(function(value) {
    //         if (value) {
    //             window.location.href = url;
    //         }
    //     });
    // });
    $(document).ready(function() {
        // getData();
        dataTableLoad();
} );
function deleteData(id){
        // console.log(id);
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
                                    console.log(data);
                                        $.ajaxSetup({
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                            }
                                        });
                                        $.ajax({
                                            type: 'POST',
                                            url: 'hotel/delete_hotel',
                                            data: data,
                                        success:function(request)
                                    {
                                        if(request.status == true){
                                            // alert('Success');
                                            // document.getElementById("msg").html(request.message);
                                            // document.getElementById("msg").style.display = "block";
                                            alert("{{ __('message.data_deleted_success') }}");
                                            // loaderTable();
                                            refreshTable();
                                            // setTimeout(location.reload.bind(location), 2000);
                                            // location.reload();
                                            // setTimeout(location.reload.bind(location), 2800);
                                        }else{
                                            alertErorr(request.message);
                                        }
                                    }
                                    });
                                }
                            });
    }

function loaderTable() {
    var x = document.getElementById("loader");
    var y = document.getElementById("table");
    y.className = "hide";
    x.className = "show";
    // setTimeout(function(){ x.className = x.className.replace("show", "hide"); }, 2000);
    }
    function dataTableLoad(data) {
        $('#hotelTable').DataTable({
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
            searching: true,
            columnDefs: [ {
                        targets: 3,
                        render: function ( data, type, row ) {
                            return data.length > 25 ?
                                data.substr( 0, 25 ) +'…' :
                                data;
                            }
                        },
                        {
                        targets: 6,
                        render: function ( data, type, row ) {
                            return data.length > 25 ?
                                data.substr( 0, 25 ) +'…' :
                                data;
                            }
                        },
                        {
                        targets: 7,
                        render: function ( data, type, row ) {
                            return data.length > 25 ?
                                data.substr( 0, 25 ) +'…' :
                                data;
                            }
                        },
                        {
                        targets: 8,
                        render: function ( data, type, row ) {
                            return data.length > 25 ?
                                data.substr( 0, 25 ) +'…' :
                                data;
                            }
                        }  

                    ]
        }).columns.adjust()
        //  .responsive.recalc()

            $('#outletTable').on( 'column-sizing.th', function ( e, settings ) {
            console.log( 'Column width recalculated in table' );
        } );

        // $('#hotelTable tbody').on( 'click', '#delete', function () {
        //                 var table = $('#hotelTable').DataTable();
        //                 var data = table.row( $(this).parents('tr') ).data()
        //                 var id = data['DT_RowId']; 
        //                 event.preventDefault();
                            
        //                 });
    }
function refreshTable() {
    // console.log('ada');
        $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
        $.ajax({
                    type: 'get',
                    url: 'hotel/get_hotel_all',
                    error: function (request, error) {
                    $('#TbodyHotel').empty();
                        $('#TbodyHotel').append(
                        '<tr>'+
                            '<td colspan="12">'+arguments[2]+'</td>'+
                        '</tr>');
                         
                },
                success: function (data) {
                    var i = 1;
                var active = "";
                var table = $('#hotelTable').DataTable();
                    table.destroy();
                $('#TbodyHotel').empty();
                $.each(data, function(index, data)
                {
                    if(data.status == 1){
                        active = "Active";
                    } else {
                        active = "Not Active";
                    }
                    // console.log(data);
                    
                    $('#TbodyHotel').append(
                    '<tr id="'+data.id+'">'+
                        '<td  style="text-align:center" valign="middle" >'+i+'</td>'+
                         '<td class="px-4 py-2">'+
                         '<a href="get_edit_hotel/'+data.id+'" ><i class="fas fa-pen"></i></a>'+
                                    '&nbsp;&nbsp;&nbsp;'+
                        '<a onclick="deleteData('+data.id+')" class="button delete-confirm" id="delete"><i class="fas fa-trash"></i></a>'+
                        '</td>'+
                        '<td>'+data.name+'</td>'+
                        '<td>'+data.description+'</td>'+
                        '<td  style="text-align:center" valign="middle" >'+data.hotel_star+'</td>'+
                        '<td>'+data.be_hotel_id+'</td>'+
                        '<td>'+data.mpg_api_key+'</td>'+
                        '<td>'+data.mpg_secret_key+'</td>'+
                        '<td>'+active+'</td>'+
                        '<td>'+data.created_by+'</td>'+
                        '<td style="text-align:center" valign="middle">'+data.created_at+'</td>'+
                        '<td>'+data.updated_by+'</td>'+
                        '<td style="text-align:center" valign="middle">'+data.updated_at+'</td>'+
                    '</tr>');
                    i += 1
                });
                dataTableLoad();
            //     $('#hotelTable').DataTable({
            //         data: data,
            // scrollX: true,
            // scrollY:"480px",
            // scrollCollapse: true,
            // pageLength: 10,
            // info: false,
            // ordering: false,
            // autoWidth: false,
            // retrieve: true,
            // destroy: true,
            // searching: false,
            // columnDefs: [ {
            //             targets: 3,
            //             render: function ( data, type, row ) {
            //                 return data.length > 25 ?
            //                     data.substr( 0, 25 ) +'…' :
            //                     data;
            //                 }
            //             },
            //             {
            //             targets: 6,
            //             render: function ( data, type, row ) {
            //                 return data.length > 25 ?
            //                     data.substr( 0, 25 ) +'…' :
            //                     data;
            //                 }
            //             },
            //             {
            //             targets: 7,
            //             render: function ( data, type, row ) {
            //                 return data.length > 25 ?
            //                     data.substr( 0, 25 ) +'…' :
            //                     data;
            //                 }
            //             },
            //             {
            //             targets: 8,
            //             render: function ( data, type, row ) {
            //                 return data.length > 25 ?
            //                     data.substr( 0, 25 ) +'…' :
            //                     data;
            //                 }
            //             }  

            //         ]
            //     });
            //         $('#hotelTable').on( 'column-sizing.th', function ( e, settings ) {
            //         console.log( 'Column width recalculated in table' );
            //     });
                }
            });
           
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
</script>
</x-app-layout>
