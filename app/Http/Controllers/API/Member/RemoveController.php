<?php

namespace App\Http\Controllers\API\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Members;
use App\Models\OrderDining;
use App\Models\Review;
use App\Models\Reservation;
use App\Models\Guest;

class RemoveController extends Controller
{
  /**
   * Handle the incoming request.
   * TODO: F&B belum masuk ke proses pengecekan transaksi yg menggantung.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function __invoke(Request $request)
  {
    try {
      $validated = $request->validate([
        'user_id' => 'required|exists:App\Models\Members,id|numeric',
        'phone' => 'required|numeric',
      ]);

      $member = Members::where('id',$validated['user_id'])->first();
      if ($member->phone !== $validated['phone'])
        throw new \Exception('Phone number is invalid, make sure phone number of member is valid', 401);

      $guest = Guest::where('id_member', $validated['user_id']);
      $dataGuest = $guest->get()->toArray();
      $guestIds = array_column($dataGuest, 'id');

      $reservation = Reservation::where('is_member', 1)->whereIn('customer_id', $guestIds);
      $dataReserv = $reservation->get()->toArray();
      $isProgressReserv = array_map(function ($reserv) {
        return $reserv['checkout_dt'] > date("Y-m-d") && $reserv['payment_sts'] == 'paid';
      }, $dataReserv);

      if (in_array(true, $isProgressReserv))
        throw new \Exception('Member have unfinished transaction on reservation or dinning.', 406);

      // reservation
      $reservation->delete();

      // guest
      $guest->delete();

      // review
      Review::where('customer_id', $validated['user_id'])->delete();

      // member
      $member->delete();

      return response()->json(
        [
          'status' => true,
          'message' => 'Success delete member',
          'code' => 200,
          'data' => [], 
        ], 
        200
      );
      
    } catch ( ValidationException $e ) {
      return response()->json(
        [
          'status' => false,
          'message' => $e->getMessage(),
          'code' => $e->status,
          'data' => $e->errors(), 
        ], 
        $e->status
      );
    } catch ( \Exception $e ) {
      return response()->json(
        [
          'status' => false,
          'message' => $e->getMessage(),
          'code' => $e->getCode(),
          'data' => null, 
        ], 
        $e->getCode()
      );
    } 
  }
}
