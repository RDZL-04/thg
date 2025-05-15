<style>
  p {
    width: 250px; white-space: nowrap; 
    overflow: hidden;
    text-overflow: ellipsis; 
  }
</style>
<body>
<div class="w-100 p-4 mb-1" style="background-color: #fff;">
  <table class="table table-borderless" style="text-align: center; vertical-align: middle;">
      @if ($data == null)
        {{__('hotel.no-image')}}
      @else
      @foreach (array_chunk($data,4) as $data_barcode)
      <tr>
        @foreach ($data_barcode as $barcode)                      
        <td style="width: 250px;">
          <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->generate($barcode['barcode'])) !!} "style="margin: 10px;">
          <p style="font-size: 12px;margin-bottom: 5px;">{{$barcode['outlet']}}</p>
        </td>
        @endforeach
      </tr>
        @endforeach 
      @endif
  </table>
</div>
</body>