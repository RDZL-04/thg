<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Profile') }}</title>
    <!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.2/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.css"/>
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.2/js/bootstrap-select.min.js"></script> -->
    <style>
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

    </style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
		{{ __('Profile') }}
        </h2>
    </x-slot>
    <div id="app">
        @include('/flash-message')


        @yield('content')
    </div>

    <div class="py-12">
    <!-- allert pop up -->
    
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="alert alert-success alert-block " id="snackbar" style ="display:none ">
        </div>
        <div class="alert alert-danger alert-block " id="snackbarErorr" style ="display:none ">
        </div>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <br>
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                    <form id="editProfile" name="editProfile" action="{{route('user.edit.profile')}}" method="post">
                    <fieldset>
                <legend>{{ __('user.general-info') }}</legend>
                        <div class="form-row">
                <div class="col col-md-4">
                    <div class="form-group">
                        <input type="hidden" class="form-control" name="id" id="txtIdUser" value="{{$data['id']}}">
                        <label for="full_name" class=" ">{{ __('user.fullname') }}</label><label style="color:red">* </label>
                        <input type="text" class="form-control" name="full_name" id="txtFullName" value="{{$data['full_name']}}" autofocus>
                    </div>
                    <div class="form-group">
                        <label for="email" class=" ">{{ __('user.email') }}</label><label style="color:red">* </label>
                        <input type="email" class="form-control"  name="email" id="txtEmail" value="{{$data['email']}}" disabled="disabled">
                    </div>
                    <div class="form-group">
                        <label for="phone" class=" ">{{ __('user.phone') }}</label><label style="color:red"> </label>
                        <input type="text" class="form-control" name="phone" id="txtPhone" value="{{$data['phone']}}" data-validation="number" 
                                        data-validation-allowing="number" datavalidation-ignore="$" minlength="9" maxlength="15">
                                         <!-- pattern="^(\+\d{0,8}\s)?\(?\d{4}\)?[\s.-]?\d{4}[\s.-]?\d{7}$"> -->
                    </div>
                    <div class="form-group">
                        
                        <label for="role" class=" ">{{ __('user.role') }}</label><label style="color:red"> </label>
                        <input type="text" class="form-control"  name="role" id="txtRole" value="{{$data['role_nm']}}" disabled="disabled">
                    </div>
                    <div class="form-group">
                                    <label for="dob" class=" ">{{ __('user.dob') }}</label><label style="color:red"> </label>
                                    <input type="date" class="form-control"  name="date_of_birth" id="dob" data-date-format="Y-m-d" value="{{$data['date_of_birth']}}">
                                </div>
                    <div class="form-group">
                        <label for="password" class=" ">{{ __('msystem.password') }}</label><label style="color:red"> </label>
                        <input for="password" class="form-control" value="******" disabled><a href="#" class="" onclick="modalPassword()" >Change Password</a>
                    </div>
                    
                    
                    
                            </div>
                            <div class="col col-md-4">
                                <div class="form-group">
                                    <label for="jenkel" class=" " >{{ __('user.jenkel') }}</label><label style="color:red"> </label>
                                    <select class="form-control" name="gender" id="txtGender">
                                        @if($dataGender != null)
                                            @if($data['gender'] != null || $data['gender'] !="")
                                            @foreach($dataGender as $gender)
                                                @if($data['gender'] == $gender['system_value'])
                                                <option value="{{$gender['system_cd']}}" selected>{{ $gender['system_value'] }}</option>
                                                @else
                                                <option value="{{$gender['system_cd']}}">{{ $gender['system_value'] }}</option>
                                                @endif
                                            @endforeach
                                            @else
                                            <option value="" selected>--Select Gender--</option>
                                            @foreach($dataGender as $gender)
                                                <option value="{{$gender['system_cd']}}">{{ $gender['system_value'] }}</option>
                                            @endforeach
                                            @endif
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                        <label for="city" class=" ">{{ __('user.country') }}</label><label style="color:red"> </label>
                                        <select id="txtCountry" class="form-control" onchange="selectCountry()" name="country">
                                        @if($dataCountry != null)
                                            @if($data['country'] != null)
                                                @foreach($dataCountry as $country)
                                                @if($data['country'] == $country['system_cd'])
                                                <option value="{{$country['system_cd']}}" selected>{{ $country['system_value'] }}</option>
                                                @else
                                                <option value="{{$country['system_cd']}}">{{ $country['system_value'] }}</option>
                                                @endif
                                                @endforeach
                                            @else
                                                <option value="" selected>--Select Country--</option>
                                                @foreach($dataCountry as $country)
                                                <option value="{{$country['system_cd']}}">{{ $country['system_value'] }}</option>
                                                @endforeach
                                            @endif
                                        @endif
                                        </select>
                                </div>
                                <div class="form-group" id="txt_province" style="display:none;">
                                        <label for="city" class=" ">{{ __('user.state-province') }}</label><label style="color:red"> </label>
                                         <select id="state_province" class="form-control" name="state_province">
                                            @if($dataProvince != null && count($dataProvince) != 0)
                                                @foreach($dataProvince as $province)
                                                    @if($data['state_province'] === $province['system_cd'])
                                                    <option value="{{$province['system_cd']}}" selected>{{ $province['system_value'] }}</option>
                                                    @else
                                                    <option value="{{$province['system_cd']}}">{{ $province['system_value'] }}</option>
                                                    @endif                                                
                                                @endforeach
                                            @else
                                            <option value="" selected>--Select Province--</option>
                                            @endif
                                        </select>  
                                </div>                        
                                <div class="form-group">
                                        <label for="city" class=" ">{{ __('user.city') }}</label><label style="color:red"> </label>
                                        <input type="text" class="form-control"  name="city" id="txtCity" value="{{$data['city']}}">
                                </div>
                                <div class="form-group">
                                        <label for="city" class=" ">{{ __('user.postal-cd') }}</label><label style="color:red"> </label>
                                        <input type="text" class="form-control" name="postal_cd" onkeypress="javascript:return isNumber(event)"  id="txtPostalCd" value="{{$data['postal_cd']}}">
                                </div>
                                <div class="form-group">
                                        <label for="Maps" class=" ">{{ __('user.address') }}</label><label style="color:red"> </label>
                                        <textarea class="form-control" id="txtAddress" name="address" rows="3" placeholder="Enter Address" style="margin-top: 0px;margin-bottom: 0px;height: 124px;resize: none;">{{$data['address']}}</textarea>
                                </div>
                                @csrf
                            
                                </form>
                            </div>
                            <div class="col col-md-4">
                                <div class="form-group">
                                <form id="editImage" name="editImage" action="{{route('user.save.images')}}" method="post" enctype="multipart/form-data">
                                <input type="hidden" class="form-control" name="txtIdUser" id="txtIdUser" value="{{$data['id']}}">
                                        <label for="Maps" class=" ">{{ __('user.photo') }}</label><label style="color:red"> </label>
                                        @if (empty($data['image']))
                                        <input type='file' class="form-control form-control-sm"  name="image" id="image" value="{{ old('file') }}" required style="border: none;">
                                        <label style="font-size: 12px;">{{__('user.desc-photo')}}</label>
                                        <div class="d-flex">
                                        <img id="icon" class="rounded-circle mx-auto" src="{{url('/')}}/user-images/default.jpg"  alt="your image" style="width: 150px;height: 150px; object-fit: cover;" value="{{ old('file') }}"/>
                                        </div>
                                        @else
                                        <input type='hidden' name="oldImage" id="oldImage" value="{{$data['image']}}"/>
                                        <input type='file' class="form-control form-control-sm"  name="image" id="image" style="border: none;"/>
                                        <label style="font-size: 12px;">{{__('user.desc-photo')}}</label>
                                        <div class="d-flex">
                                        <img id="icon" class="rounded-circle mx-auto" src="{{url($data['image'])}}" alt="your image" style="width: 150px;height: 150px; object-fit: cover;" />
                                        </div>
                                        @endif
                                        @csrf
                                        <div class="d-flex flex-row-reverse">
                                            <div class="p-2">
                                                <input type="button" class="btn btn-warning float-right" value="{{ __('button.cancel') }}" onclick="window.location.href = '{{ url('home')}}'" >
                                            </div>
                                            <div class="p-2">
                                                <button type="submit" class="btn btn-primary float-right" onkeypress="return event.keyCode != 13;" >{{ __('button.upload') }}</button>
                                            </div>
                                        </div>
                                </form>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                        <div class="col col-md-4">
                        </div>
                        <div class="col col-md-4">
                        <div class="d-flex flex-row-reverse">
                           <div class="p-2">
                                <button type="submit" class="btn btn-primary float-right" onkeypress="return event.keyCode != 13;" onclick="document.getElementById('editProfile').submit();">{{ __('button.save') }}</button>
                           </div>
                           </div>
                        </div>
                        <div class="col col-md-4">
                        </div>
                        </row>
                        
                        </div>
                    </fieldset>
                    </form>
                </div>
            </div>
        </div>
    <!-- Modal -->
<div class="modal fade" id="modalPassword" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{ __('msystem.change_password') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <!-- modal toast-->
       <div id="snackbarModal"></div>
       <!-- <form id="myForm" method='post' enctype='multipart/form-data'> -->
          <div class="form-group">
            <input type="hidden" class="form-control" name="id" id="txtIdUser" value="{{$data['id']}}">
            <label for="System_type" class="col-form-label">{{ __('msystem.old_password') }}</label><label style="color:red">* </label>
            <input type="password" class="form-control" name="old_pass" id="old_pass" placeholder="{{ __('msystem.old_password') }}" autofocus required>
          </div>
          <div class="form-group">
            <label for="system_type" class="col-form-label">{{ __('msystem.password') }}</label><label style="color:red">* </label>
            <input type="password" class="form-control" name="new_pass" id="new_pass" placeholder="{{ __('msystem.password') }}" required>
          </div>
          <div class="form-group">
            <label for="system_type" class="col-form-label">{{ __('msystem.c_password') }}</label><label style="color:red">* </label>
            <input type="password" class="form-control" name="confirm_pass" id="confirm_pass" placeholder="{{ __('msystem.c_password') }}" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
      @csrf
        <button type="button" id="btnChangePassword" class="btn btn-primary">{{ __('button.save') }}</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('button.close') }}</button>
        <!-- </form> -->
      </div>
    </div>
  </div>
</div>
</div>
<?php
    if($dataProvince != null && count($dataProvince) != 0){
        $listProvince = 1;
    }else{
        $listProvince = 0;
    }
?>
</x-app-layout>
<script>
$(document).ready(function() {
    province = "{{$data['state_province']}}";
    var list_province = "{{$listProvince}}";
    console.log(province)
    if(list_province == 1){
        document.getElementById("txt_province").style.display = "block";
    }
    else if(province != null && province != ""){
        document.getElementById("txt_province").style.display = "block";
    }

    $('#dob').attr('max', todayDate());
    var modalClose = $('#modalPassword').on('hide.bs.modal', function (event) {
                
                var modal = $(this)
                $('#old_pass').val("");
                $('#new_pass').val("");
                $('#confirm_pass').val("");
                
                });
});
function modalPassword(){
    $('#modalPassword').modal('show');
}
$('#btnChangePassword').on('click',function(){
    
    var id =  $('#txtIdUser').val();
    var old_password =  $('#old_pass').val();
    var password =  $('#new_pass').val();
    var confirm_password =  $('#confirm_pass').val();
    if(old_password== ""){
        alertModal('Old Password Can not be empty');
        return false;
    }else if(password== ""){
        alertModal('Password Can not be empty');
        return false;
    }else if(confirm_password== ""){
        alertModal('Confirm Password Can not be empty');
        return false;
    }
    if(password === old_password){
        alertModal('The new password cannot be the same as the old password');
        return false;
    }
    if(password !== confirm_password){
        alertModal('Password not match');
        return false;
    }

    if(id != "" || old_password != "" || password != "" || confirm_password != ""){
        var data = {
            old_password : old_password,
            id : id,
            password : password,
        }
        $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: 'change_password',
                    data: data,
                success:function(request)
            {
                // console.log(request)
                if(request.status == true){
                    alert(request.message);
                    $('#modalPassword').modal('hide');
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


function dismissAlertModal(){
    var x = document.getElementById("snackbarModal");
    // x.innerHTML += ;
    x.className = "hide";
}
function alert(message) {
        console.log(message)
    var x = document.getElementById("snackbar");
            x.style.display = "block";
    x.innerHTML = '<button type="button" class="close" onclick="dismissAlert()">×</button>'+message;
    
    }

function todayDate() {
        var today = new Date(); // get the current date
        var dd = today.getDate(); //get the day from today.
        var mm = today.getMonth()+1; //get the month from today +1 because january is 0!
        var yyyy = today.getFullYear(); //get the year from today

        //if day is below 10, add a zero before (ex: 9 -> 09)
        if(dd<10) {
            dd='0'+dd
        }

        //like the day, do the same to month (3->03)
        if(mm<10) {
            mm='0'+mm
        }

        //finally join yyyy mm and dd with a "-" between then
        return yyyy+'-'+mm+'-'+dd;
    }

function selectCountry(){
  var country = document.getElementById("txtCountry").value;
  if(country !=""){
    $.get('/user/get_province/'+country, function (data){
        if(data.length != 0){
            document.getElementById("txt_province").style.display = "block";
            $('#state_province').empty();
            $('#state_province').append('<option value="">--Select Province--</option>');
            $.each(data, function(index, data)
            {
            
                $('#state_province').append('<option value="'+data.system_cd+'">'+data.system_value+'</option>');
            
            });
        }else{
            document.getElementById("txt_province").style.display = "none";
            $('#state_province').empty();
            $('#state_province').append('<option value="">--Select Province--</option>');
        }
    });
  }
  console.log('ubah country'+country);
}

function isNumber(evt)
  {
      var charCode = (evt.which) ? evt.which : event.keyCode
      if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;

      return true;
  }

function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    
    reader.onload = function(e) {
      $('#icon').attr('src', e.target.result);
    }
    
    reader.readAsDataURL(input.files[0]); // convert to base64 string
  }
}

$("#image").change(function() {
  readURL(this);
});
</script>