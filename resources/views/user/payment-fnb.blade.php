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
<div class="w-100 p-3" style="text-align:center;background-color: #4D1C1C;color: #fff" >
    <h3>Payment Confirmation</h3>
</div>
<div class="w-100 p-3" style="text-align:center;background-color: #eee; color:#212529">
    <h5>Thank You</h5>
</div>
<div class="w-100 p-3" style="background-color: #fff;">
    <p> Your Order and Payment has been confirmed with the following details:
</div>
<div class="w-100 p-3" style="background-color: #fff;">
    <h4 style="text-align:center;">{{$data['order']['name_outlet']}}</h4>
    
        @foreach ($data['order_detail'] as $order)
        <table class="table table-borderless" style="width:100%;">
            <tr>
                <td><b>{{$order['menu_name']}}</b></td>
                <td style="text-align:right;">Rp. {{$order['price']}}</td>
            </tr>
            <tr>
                @if(!empty($order['order_sdishs']))
                <td>
                @if($order['note'] != null)
                    {{$order['note']}}<br>
                @endif
                    @foreach($order['order_sdishs'] as $sdish)
                        {{$sdish['menu_name']}}<br>
                    @endforeach
                </td>
                
                @else
                <td>{{$order['note']}}</td>
                    
                @endif
                <td style="text-align:right;">
                {{$order['quantity']}}x
                    <!-- <span class="align-middle" id="rcorners2">{{$order['quantity']}}x</span> -->
                </td>
            </tr>
        </table>
        <hr>
        @endforeach
        
    <table class="table table-borderless" style="width:100%;">
        <tr>
            <td>Sub Total</td>
            <td style="text-align:right; color:#C0AB69;">Rp. {{$data['order']['sub_total_price']}}</td>
        </tr>
        <tr>
            <td>Taxes</td>
            <td style="text-align:right; color:#C0AB69;">Rp. {{$data['order']['tax']}}</td>
        </tr>
        <tr>
            <td>Total Price</td>
            <td style="text-align:right; color:#C0AB69;">Rp. {{$data['order']['total_price']}}</td>
        </tr>
    </table>
    <h5 style="text-align:center;">Transaction Number</h5>
    <div class="container">
        <div class="row">
            <div class="col-sm">
            </div>
            <div class="col-sm">
                <div class="shadow-none p-4 mb-4 bg-light" style="text-align:center;">
                <h4>{{$data['order']['transaction_no']}}</h4>
                </div>
            </div>
            <div class="col-sm">
            </div>
        </div>
    </div>

    
    

</div>
 
</body>
