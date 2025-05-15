<?php

namespace App\Http\Controllers\Helpers\MPC\Adapter;

class BalancePoint extends AbsClass
{
  public function __construct(array $req)
  {
    parent::__construct($req);

    $this->path = '/api/v2.0/point/balance/query';
    $this->rules = [];
  }
}