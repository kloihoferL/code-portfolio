<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Offer extends Model
{
    protected $fillable = ['name', 'description', 'course_id', 'user_id', 'booked'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    //angebot hat einen Giver User
    public function giver():BelongsTo{
        return $this->belongsTo(User::class, 'user_id');
    }

    //angebot hat mehrere Slots
    public function slots():HasMany{
        return $this->hasMany(Slot::class);
    }


}
