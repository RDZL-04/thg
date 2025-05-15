<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $judul }}</title>
    <style>
        .dropdown-toggle {
            height: 40;
        }
    </style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
		{{ $judul }}
        </h2>
    </x-slot>
    <div id="app">
        @include('/flash-message')


        @yield('content')
    </div>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <br>
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                    <form id="addUserHotel" name="addUserHotel" action="{{ route('user_hotel.save') }}" method="post" enctype="multipart/form-data">
                    <fieldset>
                    <legend>{{__('hotel.hotel-user')}} {{ $name }}</legend>
                        <div class="form-row">
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="txtName">{{__('hotel.name')}}</label><label style="color:red">* </label>
                                    <input type="hidden" class="form-control" id="txtIdHotel" placeholder="IdHotel" name="txtIdHotel" value="{{ $id }}">
                                    <select class="form-control md-6 selectpicker" name="idUser" id="idUser" data-size="5" data-live-search="true" autofocus required>
                                        <option value="">--{{ __('combobox.select-user') }}--</option>
                                    @foreach ($data_user as $data)
                                        <option value="{{ $data['id'] }}">{{ $data['full_name'] }}</option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="txtName">{{__('hotel.email')}}</label><label style="color:red">* </label>
                                    <input type="email" class="form-control" id="txtEmail" name="txtEmail" value="" required disabled="disabled">
                                </div>
                                <div class="form-group">
                                    <label for="txtName">{{__('hotel.role')}}</label><label style="color:red">* </label>
                                    <input type="text" class="form-control" id="txtRole" name="txtRole" value="" required disabled="disabled">
                                    <input type="hidden" class="form-control" id="txtcreated_by" name="txtcreated_by" value="{{session()->get('full_name')}}">
                                </div>
                            </div>
                        </div>
                        @csrf
                        <br>
                        <div class="d-flex flex-row-reverse">
                            <div class="p-2">
                                <input type="button" class="btn btn-warning float-right" value="{{ __('button.cancel') }}" onclick="window.location.href = '{{ url('get_edit_hotel').'/'. $id}}'" >
                            </div>
                            <div class="p-2">
                                <button type="submit" class="btn btn-primary float-right" >{{ __('button.save') }}</button>
                            </div>
                        </div>
                    </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
<script>
    $(document).ready(function() {
});

$('#idUser').on('change',function(e){
            //console.log(e.target.value);
            var i = 1;
            var user_id =  e.target.value;
            if(e.target.value == 0){
               console.log("kosong");
               $('#txtRole').val("");
               $('#txtEmail').val("");
            } else {
                var url = 'user/get_user_id/'+user_id;
                $.get(url, function (data)
            {
                $('#txtRole').val(data.role_nm);
                $('#txtEmail').val(data.email);
            });
            }
            
});

</script>