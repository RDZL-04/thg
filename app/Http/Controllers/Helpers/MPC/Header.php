<?php

namespace App\Http\Controllers\Helpers\MPC;

use App\Http\Controllers\Helpers\EncryptHelper;

class Header
{
  private EncryptHelper $enc;
  private Body $body;
  private string $contentType = 'application/json';
  private string $appId = '50002THT01';
  private string $appSecret = '';
  private string $nonce;
  private float $timestamp;
  private string $sign;

  public function __construct(
    Body $body,
    string $appId = null,
    string $appSecret = null
  ) {
    $this->enc = new EncryptHelper();
    $this->body = $body;

    if ($appId) $this->appId = $appId;
    if ($appSecret) $this->appSecret = $appSecret;

    $this->nonce = strval(floor($this->enc->random_0_1() * 100000000));
    $this->timestamp = round(microtime(true) * 1000);

    $this->genSign();
  }

  public function genSign(): void
  {
    $arr = [
      $this->appId,
      $this->nonce,
      $this->timestamp,
      $this->appSecret,
      json_encode($this->body->get())
    ];

    // Sorting sesuai ASCII
    asort($arr, 2);

    // Concat array
    $data = join('', $arr);

    // Create Hashing sha256 dan convert Hex to Bin
    $hashDatas = $this->enc->hashing($data);
    $strDatas = $this->enc->to_str($hashDatas);

    // Load Private Key file
    $path = storage_path('app/key/private.key');
    $kh = openssl_pkey_get_private(file_get_contents($path));

    // Encrypt Object Data using private key
    $encrypted = openssl_private_encrypt($strDatas, $crypttext, $kh);
    if (!$encrypted)
      throw new \Exception('Unsuccessfull', 200);

    // add sign key to Header array
    $sign = $this->enc->to_hex($crypttext);

    $this->sign = $sign;
  }

  public function get(): array
  {
    return [
      'Content-Type' => $this->contentType,
      'appId' => $this->appId,
      'nonce' => $this->nonce,
      'sign' => $this->sign,
      'timestamp' => (int) $this->timestamp,
    ];
  }
}
