<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Master System</title>
<!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.css"/>
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script> -->
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
</style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
		{{ __('msystem.title') }}
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
                        <center>
                        <div class="max-w-5xl mx-auto sm:px-6 lg:px-4" id="loader"></div>
                        </center>
                        <div class="max-w-5xl mx-auto sm:px-6 lg:px-4" id="table" >
                        <br>
                        @if (session()->get('role')=='Admin')
                        <button type="button" class="btn  btn-primary float-right" data-toggle="modal" data-target="#exampleModal" data-backdrop="static" data-keyboard="false"  id="btn_add" > {{ __('button.add') }}</button>
                        @endif
                        <br>
                        <br>
                            <table class="table-bordered" id="systemTable" class="display" style="width:100%">
                                <thead>
                                    <tr class="bg-gray-100" style="text-align:center" valign="middle" >
                                        <th class="px-2" style="width:10px">{{ __('table.no') }}</th>
                                        <th class="px-4 w-10">{{ __('table.action') }}</th>
                                        <th class="px-4 w-10">{{ __('msystem.system-type') }}</th>
                                        <th class="px-5 w-10">{{ __('msystem.system-cd') }}</th>
                                        <th class="px-5 w-10">{{ __('msystem.system-value') }}</th>
                                        <th class="px-5 w-10">{{ __('msystem.system-image') }}</th>
                                        <th class="px-5 w-10">{{__('msystem.created-by') }}</th>
                                        <th class="px-5 w-10">{{__('msystem.created-at') }}</th>
                                        <th class="px-5 w-10">{{__('msystem.changed-by') }}</th>
                                        <th class="px-5 w-10">{{__('msystem.updated-at') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="bodySystem">
                                @if($data != null)
                                    @foreach ($data as $data)
                                    <tr id="{{ $data['id'] }}">
                                    <td>{{$loop->iteration}}</td>
                                    <td class="px-4 py-2" style="text-align: center;">
                                    @if (session()->get('role')=='Admin')
                                    <button id="edit" onclick="getEdit('{{ $data['id'] }}')" data-backdrop="static" data-keyboard="false"><i class="fas fa-pen"></i></button>
                                        &nbsp;&nbsp;&nbsp;
                                        <button id="edit" data-toggle="modal" data-backdrop="static" data-keyboard="false"  onclick="onDelete('{{ $data['id'] }}')"  ><i class="fas fa-trash"></i></button>                                        
                                    @else
                                    &nbsp;
                                    @endif
                                    </td>
                                    <td>{{$data['system_type']}}</td>
                                    <td>{{$data['system_cd']}}</td>
                                    <td>{{$data['system_value']}}</td>
                                    
                                    <td style="text-align:center;"><font color="blue">
                                    @if($data['system_img'] != null)
                                    <a href="" data-toggle="modal" data-target="#exampleModalImage" onclick="showImage('{{$data['system_img']}},{{$data['system_value']}}')">View Image</a></td>
                                        @else
                                        -
                                        @endif
                                    </font>
                                    </td>
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
       @if (session()->get('role')=='Admin')
       <form id="myForm" method='post' action='' enctype='multipart/form-data'>
          <div class="form-group">
            <input type="hidden" class="form-control" id="id_system" name="id">
            <label for="System_type" class="col-form-label">{{ __('msystem.system-type') }}</label><label style="color:red">* </label>
            <input type="text" class="form-control" name="system_type" id="system_type" placeholder="{{ __('msystem.system-type') }}" autofocus required>
          </div>
          <div class="form-group">
            <label for="system_type" class="col-form-label">{{ __('msystem.system-cd') }}</label><label style="color:red">* </label>
            <input type="text" class="form-control" name="system_cd" id="system_cd" placeholder="{{ __('msystem.system-cd') }}" required>
          </div>
          <div class="form-group">
            <label for="system_type" class="col-form-label">{{ __('msystem.system-value') }}</label><label style="color:red">* </label>
            <input type="text" class="form-control" name="system_value" id="system_value" placeholder="{{ __('msystem.system-value') }}" required>
            <select name="system_value_enabler" id="system_value_enabler" class="form-control" style="display: none;">
                <option value="Enable">Enable</option>
                <option value="Disable">Disable</option>
            </select>
          </div>
          <div class="form-group">
            <label for="system_type" class="col-form-label">{{ __('msystem.system-image') }}</label>
            <input type="file" class="form-control" name="system_img" id="system_img">
            <label style="font-size: 12px;">{{__('msystem.image-desc')}}</label>
          </div>
          <div class="form-group">
          <input type="hidden" class="form-control" name="created_by" id="created_by" value="{{session()->get('full_name')}}">
            <input type="hidden" class="form-control" name="updated_by" id="updated_by" value="{{session()->get('full_name')}}">
          </div>
        </form>
      </div>
      <div class="modal-footer">
      @csrf
        <button type="button" id="btnSave" class="btn btn-primary">{{ __('button.save') }}</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('button.close') }}</button>
        </form>
    @endif
      </div>
    </div>
  </div>
</div>

 <!-- Modal image-->
 <div class="modal fade" id="exampleModalImage" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Images</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body row justify-content-md-center modalImage" style="text-align:center;">
            
      </div>
      <div class="modal-footer">
        </form>
      </div>
    </div>
  </div>
</div>



<script>

    $(document).ready(function() {
        dataTable();    
        // onLoadDelete();
        $('#btn_edit').attr('disabled','disabled');
        $('#btn_delete').attr('disabled', 'disabled');  

        var modal = $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var modal = $(this)
            modal.find('.modal-title').text('Master System')
        
        });

        var modalClose = $('#exampleModal').on('hide.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var modal = $(this)
            $('#id_system').val("")
            $('#system_type').val("");
            $('#system_cd').val("");
            $('#system_value').val("");
            $('#system_img').val("");
        
        });
    });

    function loaderTable() {
        var x = document.getElementById("loader");
        var y = document.getElementById("table");
        y.className = "hide";
        x.className = "show";
        // setTimeout(function(){ x.className = x.className.replace("show", "hide"); }, 2000);
    }

    function dataTable(){
        $('#systemTable').DataTable({
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
            columnDefs: [ {
                targets: 4,
                render: function ( data, type, row ) {
                    return data.length > 25 ?
                        data.substr( 0, 25 ) +'…' :
                        data;
                }
            }]
        }).columns.adjust();

        $('#systemTable').on( 'column-sizing.th', function ( e, settings ) {
            console.log( 'Column width recalculated in table' );
        });
    }

    function getEdit(id){
        // console.log(id);
        $('#exampleModal').modal('show');
        $.get('system/get_id/'+id,function (data){
            $('#id_system').val(data.id);
            $('#system_type').val(data.system_type);
            $('#system_cd').val(data.system_cd);
            $('#system_value').val(data.system_value);
            $('#system_value_enabler').val(data.system_value);

            if (data.system_type == 'card_section_enabler' && data.system_cd == 'menu') {
                $('#system_value').hide();
                $('#system_value_enabler').show();
            } else {
                $('#system_value_enabler').hide();
                $('#system_value').show();
            }
        });
    }

    function refreshTable(){
        $.get('system/get_system_all',function (data) {
            var table = $('#systemTable').DataTable();
            table.destroy();
            var i =1;
            // console.log(data);
            $('#bodySystem').empty();
            $.each(data, function(index, data) {

                // var updated_by = data.update_by;
                var image = encodeURI(data.system_img);
                    // console.log(image);
                var str = data.system_value 
                var res = str.replaceAll("<", "");
                res = res.replaceAll(">", "");
                
                if(data.system_img != null){
                var view ='<a href="" data-toggle="modal" data-target="#exampleModalImage" onClick="showImage(\'' + data.system_img + ',' + data.system_value +'\')">View Image</a></td>';
                }else{
                    var view = "-";
                }
                
                // updated_by = res.replaceAll("null", "");
                if(data.updated_by == null){
                    var updated_by = '';
                }else{
                    var updated_by = data.updated_by;
                }
                
                $('#bodySystem').append(
                    
                    '<tr id="'+data.id+'">'+
                    '<td>'+i+'</td>'+
                    '<td class="px-4 py-2" style="text-align: center;">'+
                    '<button id="edit" data-toggle="modal" data-backdrop="static" data-keyboard="false"  onclick="getEdit('+data.id+')"  ><i class="fas fa-pen"></i></button>'+
                                    '&nbsp;&nbsp;&nbsp;'+
                        '<button id="edit" data-toggle="modal" data-backdrop="static" data-keyboard="false"  onclick="onDelete('+data.id+')"  ><i class="fas fa-trash"></i></button>'+
                        '</td>'+
                        '<td>'+data.system_type+'</td>'+
                        '<td>'+data.system_cd+'</td>'+
                        '<td>'+substring(res)+'</td>'+
                        '<td style="text-align:center;"><font color="blue">'+view+'</font></td>'+
                        '<td>'+data.created_by+'</td>'+
                        '<td style="text-align:center;">'+data.created_at+'</td>'+
                        '<td>'+updated_by+'</td>'+
                        '<td style="text-align:center;">'+data.updated_at+'</td>'+
                        '</tr>');
                        // console.log(updated_by);
                i +=1;
            });

            $('#systemTable').DataTable({
                processing: true,
                scrollX: true,
                scrollY:"480px",
                scrollCollapse: true,
                pageLength: 10,
                info: false,
                ordering: false,
                autoWidth: true,
                destroy:true,
                columnDefs: [{
                    targets: 4,
                    render: function ( data, type, row ) {
                        return data.length > 25 ?
                            data.substr( 0, 25 ) +'…' :
                            data;
                    }
                }]
            });

            $('#systemTable').on( 'column-sizing.th', function ( e, settings ) {
                console.log( 'Column width recalculated in table' );
            } );
        } );
        
    }

    function onDelete(id) {
        console.log(id);
        var url = 'delete_system/delete_system/'+id;
            swal({
                title: '{{__('message.are_you_sure')}}',
                text: '{{__('message.delete_permanent')}}',
                icon: 'warning',
                buttons: ["{{__('message.btn_delete_cancel')}}", "{{__('message.btn_delete_yes')}}"],
            }).then(function(value) {
                if (value) {
                    window.location.href = url;
                }
            });
    }

    function showImage(request){
        //  console.log(image);
        request.split(",");
        //  entry = prompt("Enter your name")
        entryArray = request.split(",");
        //  console.log(entryArray[0]);
        image = entryArray[0];
        value =entryArray[1];
        $('.modalImage').empty();
        if(image == 'null'){
            //  console.log('tidak ada gambar');
            $('.modalImage').append(
                '<figure class="figure">'+
                '<img src="{{url('')}}/system-images/default.jpg" class="figure-img img-fluid rounded" style="width:250px;height:250px;>'+
                '<figcaption class="figure-caption">No Images Found</figcaption>'+
                '</figure>'
            );
        }else{
            // console.log('ada gambar');
            $('.modalImage').append(
                '<figure class="figure">'+
                '<img  src=" {{url('')}}/'+image+'" class="figure-img img-fluid rounded" style="width:250px;height:250px;">'+
                '<figcaption class="figure-caption">'+value+'</figcaption>'+
                '</figure>'
            );
        }
    }

    $('#btnSave').on('click',function(){
        // console.log($('#system_type').value);
        var id =  $('#id_system').val();
        if (id == ''){
            var data=new FormData($("#myForm")[0]);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                type: 'POST',
                url: 'system/save_system',
                data: data,
                mimeType: "multipart/form-data",
                contentType: false,
                cache: false,
                processData: false,
                success:function(request) {
                    request = (JSON.parse(request));
                    if(request.status == true){
                        
                        alert(request.message);
                        $('#exampleModal').modal('hide');
                        // loaderTable();
                        // location.reload();
                        refreshTable();
                    }else{
                        alertModal(request.message);
                    }
                }
            });
        }else{
            var data=new FormData($("#myForm")[0]);
            if ($('#system_type').val() === 'card_section_enabler') {
                data.delete('system_value');
                data.set('system_value', $('#system_value_enabler').val());
                data.delete('system_value_enabler');
            }
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: 'system/edit_system',
                data: data,
                mimeType: "multipart/form-data",
                contentType: false,
                cache: false,
                processData: false,
                success:function(request) {
                    request = (JSON.parse(request));
                    if(request.status == true){
                        alert(request.message);
                        $('#exampleModal').modal('hide');
                        refreshTable()
                    }else{
                        $('#exampleModal').modal('hide');
                        alert(request.message);
                    }
                }
            });
        }
    });

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
