<?php

namespace App\Http\Controllers\Helpers\MPC\Adapter;

class HistoryPoint extends AbsClass
{
  public function __construct(array $req)
  {
    parent::__construct($req);

    $this->path = '/api/v2.0/point/history/query';
    $this->rules = [];
  }
}