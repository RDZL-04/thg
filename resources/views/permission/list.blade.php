<meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('permission.title') }}</title>
    {{-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous"> --}}
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"> --}}
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.2/css/bootstrap-select.min.css"> --}}
    {{-- <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css"/> --}}
    {{-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.css"/> --}}
    {{-- <script src="https://code.jquery.com/jquery-3.3.1.js"></script> --}}
    {{-- <script src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script> --}}
    {{-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> --}}
    {{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.bundle.min.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.2/js/bootstrap-select.min.js"></script> --}}
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
		.toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20px; }
  .toggle.ios .toggle-handle { border-radius: 20px; }
    </style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
		{{ __('permission.title') }}
        </h2>
    </x-slot>
    <div class="py-12">
    
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" style="min-height: 25rem;">
            <div id="app">
        @include('/flash-message')

        @yield('content')
    </div>
                <div class="max-w-5xl mx-auto sm:px-6 lg:px-4">
                    <br>
                    <div class="container">
                        <div class="row justify-content-md-left">
                            <div class="col-md-1">
                               <label for="labelHotel">{{ __('permission.role') }}</label>
                            </div>
                            <div class="col-md-4">
                            <select class="form-control md-6 selectpicker" id="idRole" data-size="5" data-live-search="true">
                                <option value="">--{{ __('combobox.select-role') }}--</option>
                            @if($data != null)
                                @foreach ($data as $datax)
									@if(strtolower($datax['role_nm']) != 'admin')
                                    <option value="{{ $datax['id'] }}">{{ $datax['role_nm'] }}</option>
									@endif
                                @endforeach
                            @endif
                            </select>
                            </div>
                        </div>
						<br/>
						<div class="row justify-content-md-left">
						<div class="col-md-12">
							<small>Note: The <strong>Admin</strong> role always has full access to the system. No need to setup.</small>
						</div>
						</div>
                        <br>
                        
                    </div>
                    
                    <br>
					
					<nav class="nav">
						<a class="nav-link active" href="javascript:void(0)" onclick="show_menu('all')">Show All</a>
						<a class="nav-link" href="javascript:void(0)" onclick="show_menu('hotel')">Hotel Permission</a>
						<a class="nav-link" href="javascript:void(0)" onclick="show_menu('outlet')">Outlet Permission</a>
						<a class="nav-link" href="javascript:void(0)" onclick="show_menu('utility')">Utility Permission</a>
						<a class="nav-link" href="javascript:void(0)" onclick="show_menu('mice')">Mice Permission</a>
					</nav>

                    <br>
					<table class="table table-striped">
					  <thead>
						<tr>
						  <th scope="col" id="head-permission-title">All Permission Access</th>
						  <th scope="col">Grant</th>
						</tr>
					  </thead>
					  <tbody id="permission-all">
						<tr><td colspan="2" style="text-align: center;">Please select role..</td></tr>
					  </tbody>
					</table>
					<br>
					
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $('#idRole').val('').trigger('change');
    });

    $('#idRole').on('change',function(e){
		// console.log(e.target.value);
		var role_id =  e.target.value;
		var url = "/permission/get?role_id=" + role_id;
		var row = '';
		
		$.get(url, function (data)
		{                
			if(data.length == 0 || data.message != undefined)
			{
				clear_card();
			}
			else
			{	
				$('#permission-all').empty();
				$.each(data, function(index, datax)
				{
					create_checkbox('permission-all', datax);
				});
			}
		});
	});
	
	function show_menu(menu)
	{
		if(menu == 'all')
		{
			$('#head-permission-title').html('All Permission Access');
			$('.row-permission').show();
		}
		else if(menu == 'hotel')
		{
			$('#head-permission-title').html('Hotel Permission Access');
			$('.row-permission').hide();
			$('.hotel-class').show();
		}
		else if(menu == 'outlet')
		{
			$('#head-permission-title').html('Outlet Permission Access');
			$('.row-permission').hide();
			$('.outlet-class').show();
		}
		else if(menu == 'utility')
		{
			$('#head-permission-title').html('Utility Permission Access');
			$('.row-permission').hide();
			$('.utility-class').show();
		}
		else if(menu == 'mice')
		{
			$('#head-permission-title').html('Mice Permission Access');
			$('.row-permission').hide();
			$('.mice-class').show();
		}
	}
	
	function hide_menu(menu)
	{
		$('.row-permission').hide();
	}
	
	function clear_card()
	{
		$('#permission-all').empty();
				
		$('#permission-all').append('<tr><td colspan="2" style="text-align: center;">Please select role..</td></tr>');
		
	}
	
	function create_checkbox(id, data)
	{
		var  myclass = '';
		
		if(data.permission_name.includes('hotel'))
		{
			myclass = 'hotel-class';
		}
		else if(data.permission_name.includes('outlet'))
		{
			myclass = 'outlet-class';
		}
		else if(data.permission_name.includes('utility'))
		{
			myclass = 'utility-class';
		}
		else 
		{
			myclass = 'mice-class';
		}
		
		var row = '<tr class="row-permission '+myclass+'">';
		row += '<td><strong>'+data.permission_name+'</strong><br/><small>'+data.description+'</small></td>';
		row += '<td>';
		
		if(data.grant_access == '1')
		{
			row += '<input id="'+data.permission_name+'" type="checkbox" class="form-control toggle" data-toggle="toggle" data-style="ios" data-offstyle="danger" checked>';
		}
		else
		{
			row += '<input id="'+data.permission_name+'" type="checkbox" class="form-control toggle" data-toggle="toggle" data-style="ios" data-offstyle="danger">';
		}
		row += '</td>';
		row += '</tr>';
		
		$('#'+id).append(row);
		
		
		$('#'+data.permission_name).bootstrapToggle({
			on: 'Allow',
			off: 'Denied'
		});
		$('#'+data.permission_name).change(function() {
		  set_permission(data.permission_name, $(this).prop('checked'));
		})
		
		
	}
	
	var SetOnProgress = false;
	
	function set_permission(permission, grant)
	{
		var role = $('#idRole').val();
		//console.log('role: ' + role);
		//console.log('permission: ' + permission);
		//console.log('grant: ' + grant);
		
		if(SetOnProgress) return;
		SetOnProgress = true;
		
		$.ajax({
			url: '/permission/set',
			type: 'POST',
			dataType: 'JSON',
			data: {
				role: role,
				permission: permission,
				grant: grant
			},
			headers :{
				'X-CSRF-TOKEN' : '{{ csrf_token() }}'
			},
			success: function(response) {
				console.log(response);
				SetOnProgress = false;
			}
		});
		
	}
	
    function substring(text)
	{
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
