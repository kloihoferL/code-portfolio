<?php

namespace App\Http\Controllers;
use App\Models\Message;

use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::with(['sender', 'receiver'])->get();
        return response()->json($messages);
    }

    public function delete($id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json(['message' => 'Nachricht nicht gefunden.'], 404);
        }

        $message->delete();

        return response()->json(['message' => 'Nachricht erfolgreich gelöscht.'], 200);
    }

}
