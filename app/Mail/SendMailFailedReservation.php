<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;
use App\Models\Guest;
use App\Http\Controllers\Helpers\MPC\Adapter\DetailCoupon;

class SendMailFailedReservation extends Mailable
{
  use Queueable, SerializesModels;

  private ?Guest $guest;
  private ?Reservation $resv;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct(string $orderId)
  {
    $param = ['transaction_no' => $orderId];
    $this->resv = Reservation::get_reservation_email($param);

    if ($this->resv) {
      $this->guest = Guest::where('id', $this->resv->customer_id)->first();
    }

    $this->subject("Failed booking {$this->resv->be_uniqueId}")
      ->from('info@transhotelgroup.com');
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->view('FailedReservation')
      ->with('data', [
        'reservation' => $this->resv,
        'guest' => $this->guest,
        'point' => $this->setDataPoint(),
        'coupon' => $this->setDataCoupon()
      ]);
  }

  private function setDataPoint()
  {
    if (
      !$this->resv['reservation']['allo_point'] ||
      !$this->resv['reservation']['allo_access_token']
    ) return null;

    return $this->resv['reservation']['allo_point'];
  }

  private function setDataCoupon()
  {
    if (
      !$this->resv['reservation']['allo_coupons_id'] ||
      !$this->resv['reservation']['allo_coupons_number'] ||
      !$this->resv['reservation']['allo_access_token']
    ) return null;

    $alloCouponId = $this->resv['reservation']['allo_coupons_id'];
    $alloCouponNo = $this->resv['reservation']['allo_coupons_number'];
    $alloAccessToken = $this->resv['reservation']['allo_access_token'];
    $reservationAmount = $this->resv['reservation']['be_amountAfterTax'];

    $reqBody = [
      "transactionNo" => $this->genTransNo(),
      "requestData" => [
        "accessToken" => $alloAccessToken,
        "couponId" => $alloCouponId,
        "couponNo" => $alloCouponNo,
      ],
    ];

    $helper = new DetailCoupon($reqBody);
    $res = $helper->send();
    $resData = json_decode($res->getBody()->getContents());

    if ($resData->code !== "0")
      throw new \Exception($resData->message);

    if (!isset($resData->responseData) || !isset($resData->responseData->couponInstance))
      throw new \Exception("Failed to get detail coupon, information data coupon is missing.");

    $dataCoupon = $resData->responseData->couponInstance;
    $discountAmount = null;

    if ($dataCoupon->faceValue) {
      $discountAmount = (int) $dataCoupon->faceValue;
    }

    if ($dataCoupon->discountRate) {
      $total = (int) $reservationAmount;
      $discountRate = (int) str_replace("0.", "", $dataCoupon->discountRate);
      $discountAmount = (int) ($discountRate / 100) * $total;
    }

    return $discountAmount;
  }

  protected function genTransNo()
  {
    $date = date('ymd');
    $middleNo = 'ARKTHG' . mt_rand(100000000000, 999999999999) . mt_rand(10000000, 99999999);;
    $data = $date . $middleNo;
    return $data;
  }
}
