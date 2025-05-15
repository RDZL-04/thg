<?php

namespace App\Http\Controllers\Helpers\MPC\Adapter;

class RedeemCoupon extends AbsClass
{
  public function __construct(array $req)
  {
    parent::__construct($req);

    $this->path = '/api/v2.0/coupon/instance/redeem';
    $this->rules = [];
  }
}