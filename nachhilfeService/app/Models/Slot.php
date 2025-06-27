<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slot extends Model
{
    protected $fillable = ['start_time', 'end_time', 'offer_id', 'is_booked'];

    //ein slot gehört zu einem Angebot
    public function offer():BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function booking():BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }



}
