<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Course extends Model
{
    protected $fillable = ['name'];

    //ein Kurs kann zu mehreren Angeboten gehören
    public function offers(): HasMany {
        return $this->hasMany(Offer::class);
    }


  public function subcourses():HasMany{
    return $this->hasMany(SubCourse::class);
  }



}
