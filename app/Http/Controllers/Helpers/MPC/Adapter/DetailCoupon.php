<?php

namespace App\Http\Controllers\Helpers\MPC\Adapter;

class DetailCoupon extends AbsClass
{
  public function __construct(array $req)
  {
    parent::__construct($req);

    $this->path = '/api/v2.0/coupon/instance/detail/query';
    $this->rules = [
      'transactionNo' => 'required',
      'requestData.accessToken' => 'required',
      'requestData.couponId' => 'required',
      'requestData.couponNo' => 'required',
    ];
  }
}