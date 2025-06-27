<?php

namespace App\Http\Controllers;

use App\Models\AppointmentRequest;
use App\Models\Message;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentRequestController extends Controller
{

    public function makeAppointment(Request $request): JsonResponse
    {
        $request = $this->parseRequest($request);
        DB::beginTransaction();
        try {
            //$appointment = AppointmentRequest::create($request->all());
            $appointment = AppointmentRequest::create([
                'offer_id' => $request->offer_id,
                'sender_id' => $request->sender_id,
                'receiver_id' => Offer::find($request->offer_id)->user_id,
                'requested_time' => $request->requested_time,
                'message' => $request->message,
                'status' => 'pending',
            ]);

            DB::commit();
            return response()->json($appointment, 201);
        } catch (\Exception $e) {
            DB::rollBack(); //wenn ein Fehler auftritt, dann rollback
            return response()->json(['message' => 'Creating Appointment failed: ' . $e->getMessage()], 500);
        }
    }

    public function getAppointmentRequests()
    {
        $appointments = AppointmentRequest::with(['offer', 'sender', 'receiver'])->get();
        return response()->json($appointments, 200);
    }


    public function acceptAppointment($id){
        $appointmentRequest = AppointmentRequest::find($id);
        if ($appointmentRequest) {
            $appointmentRequest->status = 'accepted';
            $appointmentRequest->save();

            /*Message::create([
                'sender_id' => $appointmentRequest->sender_id,
                'receiver_id' => $appointmentRequest->receiver_id,
                'content' => 'Dein Anfrage für das Angebot wurde angenommen und eine Buchung wurde erstellt.'
            ]);*/

            $geberName = $appointmentRequest->receiver->name ?? 'Ein Nutzer';
            $kursTitel = $appointmentRequest->offer->name ?? 'ein Kurs';

            Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $appointmentRequest->sender_id,
                'content' => "Deine Anfrage für den Kurs $kursTitel} wurde von {$geberName} angenommen. Eine Buchung wurde erstellt."
            ]);

            return response()->json(['message' => 'Appointment request accepted successfully. And message was sent'], 200);
        } else {
            return response()->json(['message' => 'Appointment request not found'], 404);
        }
    }

    public function rejectAppointment($id){
        $appointmentRequest = AppointmentRequest::find($id);
        if ($appointmentRequest) {
            $appointmentRequest->status = 'rejected';
            $appointmentRequest->save();

            $geberName = $appointmentRequest->receiver->name ?? 'Ein Nutzer';
            $kursTitel = $appointmentRequest->offer->name ?? 'ein Kurs';

            Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $appointmentRequest->sender_id,
                'content' => "Deine Anfrage für den Kurs {$kursTitel} wurde von {$geberName} abgelehnt."
            ]);


            return response()->json(['message' => 'Appointment request rejected successfully'], 200);
        } else {
            return response()->json(['message' => 'Appointment request not found'], 404);
        }
    }

    private function parseRequest(Request $request): Request{
        $date = new \DateTime($request->requested_time);
        $request['requested_time'] = $date->format('Y-m-d H:i:s');
        return $request;
    }

}
