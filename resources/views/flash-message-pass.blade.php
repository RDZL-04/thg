@if ($message = Session::get('success'))
<div class="alert alert-success">
    {{ $message }}
</div>
@endif
  
@if ($message = Session::get('error'))
<div class="fade show" role="alert" style="color: red">
    {{ $message }}
    
</div>
@endif
   
@if ($message = Session::get('warning'))
<div class="alert alert-warning">
    <button type="button" class="close" data-dismiss="alert">×</button>    
    {{ $message }}
</div>
@endif