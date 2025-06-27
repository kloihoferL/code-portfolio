<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = ['giver_id', 'receiver_id', 'offer_id', 'slot_id'];
    //protected $fillable = ['giver', 'receiver', 'offer', 'slot', 'course', 'course.subcourse'];



    public function giver():BelongsTo
    {
        return $this->belongsTo(User::class, 'giver_id');
    }

    public function receiver():BelongsTo{
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function offer():BelongsTo{
        return $this->belongsTo(Offer::class);
    }

    //eine Buchung besitzt mehrere Slots
    /*public function slot():HasMany{
        return $this->hasMany(Slot::class);
    }*/

    //Slot gehört zu einer Buchung
    public function slot(): BelongsTo {
        return $this->belongsTo(Slot::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }








}
