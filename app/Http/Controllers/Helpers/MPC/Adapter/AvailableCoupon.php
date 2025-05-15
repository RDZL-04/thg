<?php

namespace App\Http\Controllers\Helpers\MPC\Adapter;

class AvailableCoupon extends AbsClass
{
  public function __construct(array $req)
  {
    parent::__construct($req);
    
    $this->path = '/api/v2.0/coupon/instance/list/query';
    $this->rules = [
      'transactionNo' => 'required',
      'requestData.accessToken' => 'required',
      'requestData.page.currentPage' => 'required',
      'requestData.page.pageSize' => 'required',
    ];
  }
}