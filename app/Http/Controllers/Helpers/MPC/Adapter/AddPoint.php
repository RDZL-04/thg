<?php

namespace App\Http\Controllers\Helpers\MPC\Adapter;

class AddPoint extends AbsClass
{
  public function __construct(array $req)
  {
    parent::__construct($req);
    
    $this->path = '/api/v2.0/point/add';
    $this->rules = [];
  }
}