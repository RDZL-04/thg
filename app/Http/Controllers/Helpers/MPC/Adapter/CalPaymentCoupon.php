<?php

namespace App\Http\Controllers\Helpers\MPC\Adapter;

class CalPaymentCoupon extends AbsClass
{
  public function __construct(array $req)
  {
    parent::__construct($req);

    $this->path = '/api/v2.0/coupon/payment/instance/list/calculate';
    $this->rules = [];
  }
}