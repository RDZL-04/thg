<?php

namespace App\Http\Controllers\Helpers\Custom;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GAfour
{
  private string $uri = 'https://www.google-analytics.com/mp/collect';
  private string $measurementId;
  private string $apiSecret;
  public string $clientId;
  public string $event;
  public array $params;

  public function __construct(
    string $clientId = null,
    string $event = null
  ) {

    $this->measurementId = env('GA4ID', 'G-BG64XX2FZJ');
    $this->apiSecret = env('GA4APISECRET', '');

    if ($clientId)
      $this->clientId = $clientId;

    if ($event)
      $this->event = $event;
  }

  public function send()
  {
    $endpoint = $this->uri . "?measurement_id={$this->measurementId}&api_secret={$this->apiSecret}";
    $data = [
      'client_id' => $this->clientId,
      'events' => [
        [
          "name" => $this->event,
          "params" => $this->params
        ]
      ],
    ];

    Log::info("send ga event: {$this->event}");
    Log::info("send ga event: " . json_encode($data));

    $res = Http::withHeaders(['Content-Type' => 'application/json'])
      ->withBody(json_encode($data), 'application/json')
      ->withOptions(["verify" => false])
      ->post($endpoint);

    if ($res->status() !== 204)
      throw new \Exception('Failed to send data to GA4');
  }
}
