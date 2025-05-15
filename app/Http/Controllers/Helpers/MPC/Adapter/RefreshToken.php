<?php

namespace App\Http\Controllers\Helpers\MPC\Adapter;

class RefreshToken extends AbsClass
{
  public function __construct(array $req)
  {
    parent::__construct($req);

    $this->path = '/api/v2.0/oauth/token/refresh';
    $this->rules = [
      'transactionNo' => 'required',
      'requestData.refreshToken' => 'required',
      'requestData.grantType' => 'required',
    ];
  }
}