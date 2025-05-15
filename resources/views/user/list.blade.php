<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Master User</title>
    <!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.2/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.css"/> -->
    
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
        /* -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
        animation: fadein 0.5s, fadeout 0.5s 2.5s; */
    }
    #snackbarModal.hide {
        /* visibility: hidden; */
        display : none;
        /* -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
        animation: fadein 0.5s, fadeout 0.5s 2.5s; */
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
		{{ __('user.title') }}
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
                    @if (session()->get('role')=='Admin' || strtoupper(session()->get('role')) == 'ADMIN IT')
                    <button type="button" class="btn  btn-primary float-right" data-toggle="modal"  data-target="#userModal" data-backdrop="static" data-keyboard="false" id="btn_add" >{{ __('button.add-user') }}</button>
                    @endif
                    <br>
                    <br>
                        <table class="table-bordered" id="userTable" class="display" style="width:100%">
                            <thead>
                                <tr class="bg-gray-100" style="text-align:center" valign="middle" >
                              
                                    <th class="px-2" style="width:10px">{{ __('user.no') }}</th>
                                    <th class="px-4 w-10">{{ __('user.action') }}</th>
                                    <th class="px-5 w-10">{{ __('user.name') }}</th>
                                    <th class="px-5 w-10">{{ __('user.user-name') }}</th>
                                    <th class="px-4 w-10">{{ __('user.email') }}</th>
                                    <th class="px-1 w-10">{{ __('user.phone') }}</th>
                                    <th class="px-1 w-10">{{__('user.role')}}</th>
                                    <th class="px-1 w-10">{{__('table.created-by')}}</th>
                                    <th class="px-1 w-10">{{__('table.created-at')}}</th>
                                    <th class="px-1 w-10">{{__('table.updated-by')}}</th>
                                    <th class="px-1 w-10">{{__('table.updated-at')}}</th>

                                </tr>
                            </thead>
                            <tbody id="TbodyUser">
                            @if($data != null)
                                @foreach ($data as $data)
                                <tr id="{{ $data['id'] }}">
                                <td>{{$loop->iteration}}</td>
                                <td class="px-4 py-2" style="text-align: center;">
                                @if (session()->get('role')=='Admin' || strtoupper(session()->get('role')) == 'ADMIN IT')
                                <button id="edit" onclick="getEdit('{{ $data['id']}}')" data-backdrop="static" data-keyboard="false"><i class="fas fa-pen"></i></button>
                                    &nbsp;&nbsp;&nbsp;
                                <button id="delete" onclick="deleteData('{{ $data['id'] }}')"><i class="fas fa-trash"></i></button>
                                @else
                                &nbsp
                                @endif
                                </td>
                                <td>{{$data['full_name']}}</td>
                                <td>{{$data['user_name']}}</td>
                                <td>{{$data['email']}}</td>
                                <td>{{$data['phone']}}</td>
                                <td>{{$data['role_nm']}}</td>
                                <td>{{ $data['created_by'] }}</td>
                                <td style="text-align:center;">{{ $data['created_at'] }}</td>
                                <td>{{ $data['updated_by'] }}</td>
                                <td style="text-align:center;">{{ $data['updated_at'] }}</td>
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
                @if (session()->get('role')=='Admin' || strtoupper(session()->get('role')) == 'ADMIN IT')
                    <form  id="myForm">
                    <div class="form-group">
                        <input type="hidden" class="form-control" id="id_user">
                        <label for="full_name" class="col-form-label">{{ __('user.fullname') }}</label><label style="color:red">* </label>
                        <input type="text" class="form-control" id="full_name" autofocus>
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-form-label">{{ __('user.user-name') }}</label><label style="color:red">* </label>
                        <input type="text" class="form-control" id="user_name">
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-form-label">{{ __('user.email') }}</label><label style="color:red">* </label>
                        <input type="email" class="form-control" id="email">
                    </div>
                    <div class="form-group">
                        <label for="phone" class="col-form-label">{{ __('user.phone') }}</label><label style="color:red">* </label>
                        <input type="text" class="form-control" id="phone" datavalidation-ignore="$" minlength="9" maxlength="15" pattern="^(\+\d{0,8}\s)?\(?\d{4}\)?[\s.-]?\d{4}[\s.-]?\d{7}$" required>
                    </div>
                    <div class="form-group">
                        
                        <label for="role" class="col-form-label">{{ __('user.role') }}</label><label style="color:red">* </label>
                        <input type="hidden" class="form-control" id="role_user">
                        <select name="selectRole" class="form-control selectpicker" id="role" data-size="5" data-live-search="true" title="">
                        </select>
                    </div>
                    <div class="form-group">
                    <input type="hidden" class="form-control" name="created_by" id="created_by" value="{{session()->get('full_name')}}">
                        <input type="hidden" class="form-control" name="updated_by" id="updated_by" value="{{session()->get('full_name')}}">
                    </div>
                    <div class="form-group" id="divPassword" style="display:block">
                        <label for="password" class="col-form-label" >{{ __('user.password') }}</label><label style="color:red">* </label>
                            <div class="input-group border" id="show_hide_password">
                                <input type="password" class="flex form-control" id="password" >
                                    <span class="input-group-addon" style="margin : auto 5px">
                                        <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                    </span>
                            </div>
                    </div>
                    <!-- <div class="form-group" id="divCpassword" style="display:block">
                        <label for="c_password" class="col-form-label" >{{ __('user.c_password') }}</label><label style="color:red">* </label>
                        <div class="input-group border" id="show_hide_password">
                                <input type="password" class="flex form-control" id="c_password" >
                                    <span class="input-group-addon" style="margin : auto 5px">
                                        <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                    </span>
                            </div>
                    </div> -->
                    <div class="form-group">
                    <input type="hidden" class="form-control" name="created_by" id="created_by" value="{{session()->get('full_name')}}">
                        <input type="hidden" class="form-control" name="changed_by" id="changed_by" value="{{session()->get('full_name')}}">
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
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.2/js/bootstrap-select.min.js"></script> -->
    <script>

$("#show_hide_password a").on('click', function(event) {
        event.preventDefault();
        if($('#show_hide_password input').attr("type") == "text"){
            $('#show_hide_password input').attr('type', 'password');
            $('#show_hide_password i').addClass( "fa-eye-slash" );
            $('#show_hide_password i').removeClass( "fa-eye" );
        }else if($('#show_hide_password input').attr("type") == "password"){
            $('#show_hide_password input').attr('type', 'text');
            $('#show_hide_password i').removeClass( "fa-eye-slash" );
            $('#show_hide_password i').addClass( "fa-eye" );
        }
    });
    $(document).ready(function() {
        dataTable();    
        
        var modal = $('#userModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var modal = $(this);
            modal.find('.modal-title').text('Master User');
            $(this).find('#full_name').focus();
            getSelectRole();
            // $('#full_name').trigger('focus');
            });
         var modalClose = $('#userModal').on('hide.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var modal = $(this)
                $('#id_user').val("")
                $('#full_name').val("");
                $('#user_name').val("");
                $('#email').val("");
                $('#phone').val("");
                $('#password').val("");
                $('#role_user').val("");
                document.getElementById("divPassword").style.display='block';     
            });
    });        

    function getSelectRole(){
        $.get('role/get_data',function (data)
                {
                    var role = $('#role_user').val();
                    // console.log(role);
                    $('#role').empty();
                    $.each(data, function(index, data)
                    {
                        // console.log(data.id);
                        if(role == data.role_nm){
                            $('#role').append(
                            '<option value ="'+data.id+'" selected>'+data.role_nm+'</option>'
                            );
                        }
                        else{
                            $('#role').append(
                            '<option value ="'+data.id+'">'+data.role_nm+'</option>'
                            );
                        }
                        
                        $('#role').selectpicker('refresh');
                    });
                    
                });
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
        }).columns.adjust();
        
        $('#userTable').on( 'column-sizing.th', function ( e, settings ) {
            console.log( 'Column width recalculated in table' );
        });
    }
    function refreshTable(){
        $.get('user/get_data',function (data)
        {
            var table = $('#userTable').DataTable();
            table.destroy();
                // console.log(data);
                var i = 1;
                var role = "";
                $('#TbodyUser').empty();
                $.each(data, function(index, data)
                {
                    if(data.updated_by == null){
                        var updated_by = '';
                    }else{
                        var updated_by = data.updated_by;
                    }
                    $('#TbodyUser').append(
                    '<tr id="'+data.id+'">'+
                        '<td>'+i+'</td>'+
                        '<td class="px-4 py-2" style="text-align:center;">'+
                         '<button id="edit" onclick="getEdit(\'' + data.id +'\')" data-keyboard="false"><i class="fas fa-pen"></i></button>'+
                                    '&nbsp;&nbsp;&nbsp;'+
                        '<button id="delete" onclick="deleteData('+data.id+')"><i class="fas fa-trash"></i></button>'+
                        '</td>'+
                        '<td>'+data.full_name+'</td>'+
                        '<td>'+data.user_name+'</td>'+
                        '<td>'+data.email+'</td>'+
                        '<td>'+data.phone+'</td>'+
                        '<td>'+data.role_nm+'</td>'+
                        '<td>'+data.created_by+'</td>'+
                        '<td style="text-align:center;">'+data.created_at+'</td>'+
                        '<td>'+updated_by+'</td>'+
                        '<td style="text-align:center;">'+data.updated_at+'</td>'+
                    '</tr>');
                    i += 1
                });
                    $('#userTable').DataTable({
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
    }
    function getEdit(id){
        // var res = data.split("&&");
        // console.log(id);
        $.get('user/get_user_id/'+id,function (data){
            // console.log(data.id);
            $('#id_user').val(data.id);
            $('#full_name').val(data.full_name);
            $('#user_name').val(data.user_name);
            $('#email').val(data.email);
            $('#phone').val(data.phone);
            $('#role_user').val(data.role_nm);
            // document.getElementById("role").selectedIndex=data.id_role;
            document.getElementById("divPassword").style.display='none';
            $('#userModal').modal('show');       
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
                            url: 'user/delete_user',
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
    // add and edit
    $('#btnSave').on('click',function()
    {
        var id =  $('#id_user').val();
        if (id == '')
        {
            var data = {
            full_name : $('#full_name').val(),
            email : $('#email').val(),
            phone : $('#phone').val(),
            id_role : $('#role').val(),
            password : $('#password').val(),
            user_name : $('#user_name').val(),
            created_by : $('#created_by').val(),
            }
            // console.log(data);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: 'user/save_user',
                    data: data,
                success:function(request)
            {
                if(request.status == true){
                    alert(request.message);
                    $('#userModal').modal('hide');
                    // setTimeout(refreshTable(), 2000);
                    refreshTable();
                }else{
                    alertModal(request.message);
                }
            }
            });
        }
        else
        {
            var data = {
            id : $('#id_user').val(),
            full_name : $('#full_name').val(),
            email : $('#email').val(),
            phone : $('#phone').val(),
            id_role : $('#role').val(),
            password : $('#password').val(),
            user_name : $('#user_name').val(),
            updated_by : $('#updated_by').val(),
            }
            // console.log('data');
            $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: 'user/edit_user',
                    data: data,
                success:function(request)
            {
                if(request.status == true){
                    alert(request.message);
                    $('#userModal').modal('hide');
                    // setTimeout(location.reload.bind(location), 2000);
                    refreshTable()
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
    x.innerHTML = '<button type="button" class="close" onclick="dismissAlertModal()">×</button>'+message;
    // setTimeout(function(){ x.className = x.className.replace("show", "hide"); }, 2000);
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

    function dismissAlertModal(){
        var x = document.getElementById("snackbarModal");
        // x.innerHTML += ;
        x.className = "hide";
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
