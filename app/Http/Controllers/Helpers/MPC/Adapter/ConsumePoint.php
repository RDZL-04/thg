<?php

namespace App\Http\Controllers\Helpers\MPC\Adapter;

class ConsumePoint extends AbsClass
{
  public function __construct(array $req)
  {
    parent::__construct($req);

    $this->path = '/api/v2.0/point/consume';
    $this->rules = [];
  }
}