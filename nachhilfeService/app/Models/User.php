<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', //nur name nicht first and last
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return ['user' => [
            'id' => $this->id,
            'role' => $this->role,
        ]
        ];
    }


    public function offers(): HasMany {
        return $this->hasMany(Offer::class);
    }

    public function bookingsAsGiver(): HasMany {
        return $this->hasMany(Booking::class, 'giver_id');
    }

    public function bookingsAsReceiver(): HasMany {
        return $this->hasMany(Booking::class, 'receiver_id');
    }

    public function isGiver(){
        return $this->role === 'geber';
    }

    public function isTaker(){
        return $this->role === 'nehmer';
    }


}
