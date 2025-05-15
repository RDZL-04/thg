<?php

namespace App\Http\Controllers\Helpers\MPC\Adapter;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Helpers\MPC\Body as MPCBody;
use App\Http\Controllers\Helpers\MPC\Header as MPCHeader;
use App\Http\Controllers\Helpers\MPC\Request as MPCRequest;

abstract class AbsClass
{
  public bool $debug = false;

  protected array $request;
  protected array $rules;
  protected string $path;

  private MPCBody $mpcBody;
  private MPCHeader $mpcHeader;
  private MPCRequest $mpcRequest;

  public function __construct(array $req)
  {
    $this->request = $req;
  }

  public function send() 
  {
    Validator::validate($this->request, $this->rules);

    $this->mpcBody = new MPCBody($this->request);
    $this->mpcHeader = new MPCHeader($this->mpcBody);
    $this->mpcRequest = new MPCRequest($this->mpcHeader, $this->mpcBody, $this->debug);
    return $this->mpcRequest->sendPost($this->path);
  }
}