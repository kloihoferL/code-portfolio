<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentRequest extends Model
{
    protected $fillable = [
        'offer_id', 'sender_id', 'receiver_id',
        'requested_time','message', 'status'
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    // Angebot hat einen Giver User
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Angebot hat einen Empfänger User
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }


}
