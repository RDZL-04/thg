<?php

namespace App\Http\Controllers\Helpers\MPC\Adapter;

class AcquireCoupon extends AbsClass
{
  public function __construct(array $req)
  {
    parent::__construct($req);
    
    $this->path = '/api/v2.0/coupon/instance/acquire';
    $this->rules = [
      'transactionNo' => 'required',
      'requestData.accessToken' => 'required',
      'requestData.couponId' => 'required',
    ];
  }
}