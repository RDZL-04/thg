
<title>{{ $judul }}</title>
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
            {{ $judul }}
        </h2>
    </x-slot>
    <div id="app">
        {{-- @include('/flash-message') --}}

        @yield('content')
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg"> @include('/flash-message')
            <br>
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4 center">
                    <form id="addMenu" name="addMenu" action="{{ route('outlet_user.store') }}" method="post">
                    <fieldset>
                        <legend>User Outlet {{ $rest_name }}</legend><br>
                        <div class="form-row">
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="selectIdUser">{{ __('outlet.full-name') }}</label><label style="color:red">* </label>
                                    <input type="hidden" class="form-control" id="txtOutletId" placeholder="IdOutlet" name="txtOutletId" value="{{ $txtOutletId }}">
                                         <input type="hidden" class="form-control" name="created_by" id="created_by" value="{{ session()->get('full_name')}}">
                                    <select name="selectIdUser" id="selectIdUser" class="form-control selectpicker" data-size="4" data-live-search="true" autofocus required>
                                        <option value="" data-name="">--{{ __('combobox.select-user') }}--</option>
                                        @foreach ($data_user as $datax)
                                            @if (!empty($data_old))
                                                @if($data_old['selectIdUser'] == $datax['id'])
                                                    <option value="{{ $datax['id'] }}" selected>{{ $datax['full_name'] }}</option>
                                                @else
                                                    <option value="{{ $datax['id'] }}">{{ $datax['full_name'] }}</option>
                                                @endif
                                            @else
                                                <option value="{{ $datax['id'] }}" data-role="{{ $datax['role_nm'] }}" data-email="{{ $datax['email'] }}">{{ $datax['full_name'] }}</option>
                                            @endif
                                        @endforeach        
                                    </select>
                                </div><br>
                                <div class="form-group">
                                    <label for="txtEmail">{{ __('outlet.email') }}</label>
                                    @if (!empty($data_old))
                                    <input type="text" class="form-control" id="txtEmail" name="txtEmail" value="{{ $data_old['txtEmail'] }}" disabled>
                                    @else
                                    <input type="text" class="form-control" id="txtEmail" name="txtEmail" disabled>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="txtRole">{{ __('outlet.role') }}</label>
                                    @if (!empty($data_old))
                                    <input type="text" class="form-control" id="txtRole" name="txtRole" value="{{ $data_old['txtRole'] }}" disabled>
                                    @else
                                    <input type="text" class="form-control" id="txtRole" name="txtRole" disabled>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        @csrf <br>
                        <div class="d-flex flex-row-reverse">
                            <div class="p-2">
                                <input type="button" class="btn btn-warning float-right" value="{{ __('button.cancel') }}" onclick="window.location.href = '{{ url('master/fnboutlet/get_edit_outlet/').'/'. $txtOutletId}}'" >
                            </div>
                            @if( (strtolower(session()->get('role')) == 'admin') || (Auth::user()->permission('outlet-edit')) )
                            <div class="p-2">
                                <button type="submit" class="btn btn-primary float-right" >{{ __('button.add') }}</button>
                            </div>
                            @endif
                        </div>

                    </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>

    $('#selectIdUser').on('change',function(e){
        // console.log(e.target.value);
        var role = $("option[value=" + $(this).val() + "]", this).attr('data-role');
        var email = $("option[value=" + $(this).val() + "]", this).attr('data-email');
        var txtRole = document.getElementById('txtRole');
        var txtEmail = document.getElementById('txtEmail');
        // console.log(email);
        txtRole.value = role;
        txtEmail.value = email;
    });

</script>

</x-app-layout>
