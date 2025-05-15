<?php

namespace App\Http\Controllers\Helpers\MPC\Adapter;

class GenAuth extends AbsClass
{
  public function __construct(array $req)
  {
    parent::__construct($req);

    $this->path = '/api/v2.0/oauth/authorize';
    $this->rules = [];
  }
}