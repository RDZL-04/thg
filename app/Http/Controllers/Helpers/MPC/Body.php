<?php

namespace App\Http\Controllers\Helpers\MPC;

class Body
{
  private Array $params;

  public function __construct(array $params)
  {
    $this->params = $params;
  }

  public function get($key = null) : array 
  {
    if ($key) 
      $this->params[$key];

    return $this->params;
  }
}