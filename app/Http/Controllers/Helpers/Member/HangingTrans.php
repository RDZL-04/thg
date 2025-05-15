<?php

namespace App\Http\Controllers\Helpers\Member;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Members;
use App\Models\Reservation;
use App\Models\Guest;

class HangingTrans
{
  private Members $member;
  private Builder $resv;
  private Builder $guest;

  public function __construct(Members $member)
  {
    $this->member = $member;
  }

  public function isHas()
  {
    $this->guest = Guest::where('id_member', $this->member->id);
    $dataGuest = $this->guest->get()->toArray();
    $guestIds = array_column($dataGuest, 'id');

    $this->resv = Reservation::where('is_member', 1)
      ->whereIn('customer_id', $guestIds)
      ->where('payment_sts', 'paid');
    $dataReserv = $this->resv->get()->toArray();

    $isProgressReserv = array_map(function ($reserv) {
      return $reserv['checkout_dt'] > date("Y-m-d");
    }, $dataReserv);

    return in_array(true, $isProgressReserv);
  }

  public function getGuest()
  {
    return $this->guest->get()->toArray();
  }

  public function getResv()
  {
    return $this->resv->get()->toArray();
  }
}
