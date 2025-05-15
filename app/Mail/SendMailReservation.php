<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\Helpers\MPC\Adapter\DetailCoupon;

class SendMailReservation extends Mailable
{
    use Queueable, SerializesModels;

    private $coupon = null;
    private $guest;
    private $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        //
        // dd($data['guest']);
        $this->subject = $data['subjectGuest'];
        $this->guest = $data['guest'];
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('info@transhotelgroup.com')
            ->view('ConfirmReservation')
            ->subject($this->subject)
            ->with([
                'data' => $this->data,
                'point' => $this->setDataPoint(),
                'coupon' => $this->setDataCoupon()
            ]);
    }

    private function setDataPoint()
    {
        if (
            !$this->data['reservation']['allo_point'] ||
            !$this->data['reservation']['allo_access_token']
        ) return null;

        return $this->data['reservation']['allo_point'];
    }

    private function setDataCoupon()
    {
        if (
            !$this->data['reservation']['allo_coupons_id'] ||
            !$this->data['reservation']['allo_coupons_number'] ||
            !$this->data['reservation']['allo_access_token']
        ) return null;

        $alloCouponId = $this->data['reservation']['allo_coupons_id'];
        $alloCouponNo = $this->data['reservation']['allo_coupons_number'];
        $alloAccessToken = $this->data['reservation']['allo_access_token'];
        $reservationAmount = $this->data['reservation']['be_amountAfterTax'];

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
