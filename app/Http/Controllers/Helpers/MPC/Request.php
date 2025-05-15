<?php

namespace App\Http\Controllers\Helpers\MPC;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class Request
{
  private string $uri = 'https://uatua.ctcorpmpc.com';
  private Body $body;
  private Header $header;
  private bool $debug = false;

  public function __construct(
    Header $header, 
    Body $body, 
    bool $debug = false
  ) {
    $this->body = $body;
    $this->header = $header;
    $this->debug = $debug;
  }

  public function sendPost(string $path) : Response 
  {
    $res = Http::withHeaders($this->header->get())
      ->withOptions(["verify"=>false])
      ->post(
        $this->uri . $path, 
        $this->body->get()
      );

    if (!$this->debug) 
      return $res;
      
    dd(
      $this->uri . $path,
      $this->header->get(),
      $this->body->get(),
      $res->getBody()->getContents()
    );
  }
}