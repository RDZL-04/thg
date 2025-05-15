<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('role.title') }}</title>
    <!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.css"/> -->
    
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
</style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
		{{ __('role.title') }}
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
                <!-- <div ></div> -->
                <div class="alert alert-success alert-block " id="snackbar" style ="display:none ">
                </div>
                <div class="alert alert-danger alert-block " id="snackbarErorr" style ="display:none ">
                </div>
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                    <center>
                    <div class="max-w-5xl mx-auto sm:px-6 lg:px-4" id="loader"></div>
                    </center>
                   
                    <div class="max-w-5xl mx-auto sm:px-6 lg:px-4" id="table" >
                    <br>
                    @if (session()->get('role')=='Admin')
                    <button type="button" class="btn  btn-primary float-right" data-toggle="modal"  data-target="#userModal" data-backdrop="static" data-keyboard="false" id="btn_add" >{{ __('role.add-role') }}</button>
                    @endif
                    <br>
                    <br>
                        <table class="table-bordered" id="userTable" class="display" style="width:100%">
                            <thead>
                                <tr class="bg-gray-100" style="text-align:center" valign="middle" >
                              
                                    <th class="px-2" style="width:10px;">{{ __('role.no') }}</th>
                                    <th class="px-4 w-10">{{ __('role.action') }}</th>
                                    <th class="px-5 w-10">{{ __('role.name') }}</th>
                                    <th class="px-4 w-10">{{ __('role.description') }}</th>
                                    <th class="px-1 w-10">{{ __('role.created_by') }}</th>
                                    <th class="px-1 w-10">{{__('role.created_at')}}</th>
                                    <th class="px-1 w-10">{{__('role.update_by')}}</th>
                                    <th class="px-1 w-10">{{__('role.update_at')}}</th>

                                </tr>
                            </thead>
                            <tbody id="TbodyUser">
                            @if($data != null)
                                @foreach ($data as $data)
                                <tr id="{{ $data['id'] }}">
                                <td>{{$loop->iteration}}</td>
                                <td class="px-4 py-2" style="text-align: center;">
                                @if (session()->get('role')=='Admin')
                                <button id="edit" onclick="getEdit('{{ $data['id'] }}&&{{$data['role_nm']}}&&{{$data['description']}}')" data-backdrop="static" data-keyboard="false"><i class="fas fa-pen"></i></button>
                                    &nbsp;&nbsp;&nbsp;
                                <button id="delete" onclick="deleteData('{{ $data['id'] }}')"><i class="fas fa-trash"></i></button>
                                @else
                                    &nbsp;
                                @endif
                                </td>
                                <td>{{$data['role_nm']}}</td>
                                <td>{{$data['description']}}</td>
                                <td>{{$data['created_by']}}</td>
                                <td style="text-align:center;">{{$data['created_at']}}</td>
                                <td>{{$data['updated_by']}}</td>
                                <td style="text-align:center;">{{$data['updated_at']}}</td>
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

        <!-- Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                @if (session()->get('role')=='Admin')
                    <form  id="myForm">
                    <div class="form-group">
                        <input type="hidden" class="form-control" id="id_role">
                        <label for="role_nm" class="col-form-label">{{ __('role.name') }}</label><label style="color:red">* </label>
                        <input type="text" class="form-control" name="role_nm" id="role_nm" placeholder="{{ __('role.name') }}" >
                    </div>
                    <div class="form-group">
                        <label for="description" class="col-form-label">{{ __('role.description') }}</label><label style="color:red">* </label>
                        <textarea class="form-control" id="description" name="txtDescription" rows="3" placeholder="Enter Description">{{ old('txtDescription') }}</textarea>
                    </div>
                    <div class="form-group">
                    <input type="hidden" class="form-control" name="created_by" id="created_by" value="{{session()->get('full_name')}}">
                        <input type="hidden" class="form-control" name="changed_by" id="updated_by" value="{{session()->get('full_name')}}">
                    </div>
                    </form>
                </div>
                <div class="modal-footer">
                @csrf
                    <button type="button" id="btnSave" class="btn btn-primary">{{ __('button.save') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('button.close') }}</button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script> -->
    <script>

    $(document).ready(function() {
        dataTable();    
         $('#userModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var modal = $(this)
            modal.find('.modal-title').text('Master Role')    
            $('#role_nm').focus();
            // setTimeout(function() { $('input[name="role_nm"]').focus() }, 3000);    
            // $('input:text:visible:first').focus();
            // document.getElementById("role_nm").focus();
            });
         var modalClose = $('#userModal').on('hide.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var modal = $(this)
                $('#id_role').val("")
                $('#role_nm').val("");
                $('#description').val("");       
            });
    });        

    function loaderTable() {
    var x = document.getElementById("loader");
    var y = document.getElementById("table");
    y.className = "hide";
    x.className = "show";
    // setTimeout(function(){ x.className = x.className.replace("show", "hide"); }, 2000);
    }
    function dataTable(data){
        // $('#userTable').data.reload();
                    $('#userTable').DataTable({
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
                        $('#userTable').on( 'column-sizing.th', function ( e, settings ) {
                        console.log( 'Column width recalculated in table' );
                        });
    }
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
                    // console.log(data);
                    $.ajaxSetup({
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                });
                    $.ajax({
                            type: 'POST',
                            url: 'role/delete_role',
                            data: data,
                            success:function(request)
                    {
                        if(request.status == true){
                            alert(request.message);
                            // loaderTable();
                            refreshTable();
                            // window.location.href = "{{URL::to('role')}}"
                            // setTimeout(, 2800);
                        }else{
                            alertErorr(request.message);
                        }
                    }
                });
                }
        });
    }
    function refreshTable()
    {
        // console.log('ada');
       
        $.get('role/get_data',function (data)
        {            var table = $('#userTable').DataTable();
            table.destroy();
                // console.log(data);
                var i = 1;
                var role = "";
                
                 $('#TbodyUser').empty();
                $.each(data, function(index, data)
                {
                    // console.log(data);
                    $('#TbodyUser').append(
                    '<tr id="'+data.id+'">'+
                        '<td style="width:10px;">'+i+'</td>'+
                         '<td class="px-4 py-2" style="text-align:center;">'+
                         '<button id="edit" onclick="getEdit(\'' + data.id + '&&' + data.role_nm + '&&' + data.description + '\')" data-keyboard="false"><i class="fas fa-pen"></i></button>'+
                                    '&nbsp;&nbsp;&nbsp;'+
                        '<button id="delete" onclick="deleteData('+data.id+')"><i class="fas fa-trash"></i></button>'+
                        '</td>'+
                        '<td>'+data.role_nm+'</td>'+
                        '<td>'+data.description+'</td>'+
                        '<td>'+data.created_by+'</td>'+
                        '<td style="text-align:center;">'+data.created_at+'</td>'+
                        '<td>'+data.updated_by+'</td>'+
                        '<td style="text-align:center;">'+data.updated_at+'</td>'+
                    '</tr>');
                    i += 1
                });
                    $('#userTable').DataTable({
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
                        $('#userTable').on( 'column-sizing.th', function ( e, settings ) {
                        console.log( 'Column width recalculated in table' );
                        });
        });
        // dataTable();
    }
    function getEdit(data){
        var res = data.split("&&");
        // console.log(res[0]);
        $('#id_role').val(res[0]);
        $('#role_nm').val(res[1]);
        $('#description').val(res[2]);
        $('#userModal').modal('show');
                        
    }
    // add and edit
    $('#btnSave').on('click',function()
    {
        var id =  $('#id_role').val();
        if (id == '')
        {
            var data = {
            role_nm : $('#role_nm').val(),
            description : $('#description').val(),
            created_by : $('#created_by').val(),
            }
            console.log(data);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: 'role/save_role',
                    data: data,
                success:function(request)
            {
                if(request.status == true){
                    alert(request.message);
                    $('#userModal').modal('hide');
                    refreshTable();
                    // loaderTable();
                    // setTimeout(location.reload.bind(location), 2000);
                }else{
                    alertModal(request.message);
                }
            }
            });
        }
        else
        {
            var data = {
            id : $('#id_role').val(),
            role_nm : $('#role_nm').val(),
            description : $('#description').val(),
            updated_by : $('#updated_by').val(),
            }
            console.log('data');
            $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: 'role/edit_role',
                    data: data,
                success:function(request)
            {
                if(request.status == true){
                    alert(request.message);
                    $('#userModal').modal('hide');
                    refreshTable();
                    
                }else{
                    alertModal(request.message);
                }
            }
            });
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
