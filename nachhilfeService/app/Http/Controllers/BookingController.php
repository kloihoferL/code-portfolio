<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Offer;
use App\Models\Slot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller


{

   //hier nur mit den ids
    /* public function index(): JsonResponse
    {
        $bookings = Booking::with('slot')->get();
        return response()->json($bookings, 200);
    }*/

    //hie die ganzen Beziehungen
    public function index(): JsonResponse
    {
        $bookings = Booking::with(['slot', 'offer', 'offer.course', 'offer.course.subcourses', 'giver' ,'receiver'])->get();
        return response()->json($bookings, 200);
    }



   public function makeBooking(Request $request)
{
    $bookingData = $request->all();

    $slot = Slot::find($bookingData['slot_id']);

    if (!$slot) {
        return response()->json(['message' => 'Slot not found.'], 404);
    }

    $slot->is_booked = true;
    $slot->save();

    $booking = Booking::create([
        'offer_id' => $bookingData['offer_id'],
        'slot_id' => $slot->id,
        'receiver_id' => $bookingData['receiver_id'],
        'giver_id' => $bookingData['giver_id'],
    ]);

    $offer = $slot->offer;

    if ($offer->slots()->where('is_booked', false)->count() === 0) {
        $offer->booked = true;
        $offer->save();
    }

    return response()->json([
        'message' => 'Booking created',
        'slot_id' => $slot->id,
        'is_booked' => $slot->is_booked,
    ], 201);
}






}
