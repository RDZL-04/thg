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
    <h4>Payment Confirmation</h4>
</div>
<div class="w-100 p-3" style="text-align:center;background-color: #eee; color:#212529">
    <h5>Thank You</h5>
</div>
<div class="w-100 p-3" style="background-color: #fff;">
<h4 style="text-align:center;">{{$data['reservation']['name_hotel']}}</h4>
    <p> Your Order and Payment has been confirmed with the following details:
    <hr>
    <p><b>{{$data['reservation']['be_room_type_nm']}}</b></p>
    <p>{{$data['reservation']['be_rate_plan_name']}}</p>
    <p>

        @foreach ($data['reservation']['hotel_facility'] as $facility)
            @if ($loop->last)
            {{$facility['name']}} 
            @else
            {{$facility['name']}} |
            @endif
        @endforeach
    </p>
    <hr>
    <table class="table table-borderless" style="width:100%;" >
        <tr style="height: 20px;">
        <td><img  width='50' height='50'  id="icon" src="{{url('/')}}/asset/icon/calendar.jpg" alt="your image" /></td>
            <td style="border-right: 1px solid #cdd0d4;">
            Check-in<br>
            <color style="color:#C0AB69">
            {{$data['reservation']['checkin_dt']}}
            </color>
            </td>
            <td><img width='50' height='50' id="icon" src="{{url('/')}}/asset/icon/calendar.jpg" alt="your image"  /></td>
            <td>
            Check-out
            <br>
            <color style="color:#C0AB69">
            {{$data['reservation']['checkout_dt']}}
            </color>
            </td>
        </tr>
    </table>
    <hr>
    <table class="table table-borderless" style="width:100%;">
        <tr>
            <td style="text-align:right;">
            <img id="icon" src="{{url('/')}}/asset/icon/account-grey.jpg" alt="your image" />
            </td>
            <td style="border-right: 1px solid #cdd0d4;color:#C0AB69;">{{$data['reservation']['ttl_adult']}} <color style="color:#212529">Adult</color></td>
            <td style="border-right: 1px solid #cdd0d4;color:#C0AB69;">{{$data['reservation']['ttl_children']}} <color style="color:#212529"> Children </color></td>
            <td style="color:#C0AB69;">{{$data['reservation']['ttl_room']}} <color style="color:#212529">Room</color></td>
        </tr>
    </table>
    <hr>
    <p><b>Special Request</b></p>
    @if($data['reservation']['special_request'] != null)
    <p>{{$data['reservation']['special_request']}}</p>
    @else
    <p>No Special Request</p>
    @endif
    <p><b>Guest</b></p>
    <p>{{$data['guest']['full_name']}}</p>
    <p>{{$data['guest']['phone']}}</p>
    <hr>
    <table class="table table-borderless" style="width:100%;">
        <tr>
            <td>Sub Total</td>
            <td style="text-align:right; color:#C0AB69;">Rp. {{$data['reservation']['be_grossAmountBeforeTax']}}</td>
        </tr>
        @if(isset($point) && !empty($point))
            <tr>
                <td>Point</td>
                <td style="text-align:right; color:#C0AB69;">Rp. {{$point}}</td>
            </tr>
        @endif

        @if(isset($coupon) && !empty($coupon))
            <tr>
                <td>Discount</td>
                <td style="text-align:right; color:#C0AB69;">Rp. {{$coupon}}</td>
            </tr>
        @endif

        <tr>
            <td>Taxes</td>
            <td style="text-align:right; color:#C0AB69;">Rp. {{$data['reservation']['tax']}}</td>
        </tr>
        <tr>
            <td>Total Price</td>
            <td style="text-align:right; color:#C0AB69;">Rp. {{$data['reservation']['price']}}</td>
        </tr>
    </table>
    <p style="text-align:center;"><b>Confirmation Number</b></p>
    <div class="container">
        <div class="row">
            <div class="col-sm">
            </div>
            <div class="col-sm">
                <div class="shadow-none p-4 mb-4 bg-light" style="text-align:center;">
                <p><b>{{$data['reservation']['be_uniqueId']}}</b></p>
                </div>
            </div>
            <div class="col-sm">
            </div>
        </div>
    </div>
</div>
 
</body>
