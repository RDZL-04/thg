<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"> -->
<style> 
#rcorners2 {
    border-radius: 10px;
    border: 2px solid #6c757d;
    padding-top: 5px;
    padding-right: 30px;
    padding-bottom: 5px;
    padding-left: 30px;
    width: 30px;
    height: 10px;
}
</style>
<body>
<div class="w-100 p-3" style="text-align:center;background-color: #333;color: #CAA761" >
    <h3>Order History</h3>
</div>
<div class="w-100 p-3" style="text-align:center;background-color: #fff; color:#212529">
@foreach($data['order'] as $order)

    <table class="table table-borderless" style="width:100%; font-size:14px;" >
        <tr>
            <td>
            <button type="button" class="btn btn-success">Paid</button>
            
            </td>
            <td style="text-align:right;">
            {{$order['created_at']}}
            </td>
        </tr>
        <tr>
            <td>
            <img id="icon" src="{{url('/')}}/asset/icon/account-grey.svg" alt="your image" style="width:5%;" />
            </td>
            <td style="text-align:left;  font-size:18px;">
            <b>{{$order['customer_name']}} - {{$order['table_no']}}</b><br>
            member
            </td>
        </tr>
        @foreach($order['order_detail'] as $menu)
        <tr>
            <td style="font-size:16px;">
            <b>{{$menu['quantity']}}x {{$menu['menu_name']}}</b>
            @if($menu['note'] !== null)
            <br>{{$menu['note']}}
            @endif
            </td>
            <td style="text-align:right;">
            Rp. {{$menu['price']}}
            </td>
        </tr>
        @endforeach
        <tr>
        <b>
            <td style="font-size:16px;">
            Total Price
            </td>
            <td style="text-align:right; color:#C0AB69;">
            Rp. {{$order['total_price']}}
            </td>
        </b>        
        </tr>
    </table>
    <hr>
@endforeach
</div>
    
    
    

</div>
 
</body>
