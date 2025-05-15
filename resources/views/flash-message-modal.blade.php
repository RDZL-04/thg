@if ($message = Session::get('success'))
<div class="alert alert-success alert-block" id="success">
    <button type="button" class="close" data-dismiss="alert">×</button>    
    {{ $message }}
</div>
@endif
  
@if ($message = Session::get('error'))
<div class="alert alert-danger alert-block" id="success">
    <button type="button" class="close" data-dismiss="alert">×</button>    
    {{ $message }}
</div>
@endif
   
@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>    
    {{ $message }}
</div>
@endif
   
@if ($message = Session::get('info'))
<div class="alert alert-info alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>    
    {{ $message }}
</div>
@endif
  
<div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert">×</button>    
    
</div>

<!-- 
<div class="toast" data-delay="1000" data-autohide="false">
    <div class="toast-header">
      Success
    </div>
    <div class="toast-body">
      Some text inside the toast body
    </div>
  </div> -->