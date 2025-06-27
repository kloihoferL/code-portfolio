<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubCourse extends Model
{
    protected $fillable = [ 'course_id', 'name'];

    //Subcourse gehört zu einem Kurs
    public function course():BelongsTo{
        return $this->belongsTo(Course::class);
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_subcourse');
    }

}
