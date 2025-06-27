<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('own-offer', function(User $user, Offer $offer){
            return $user->id === $offer->user_id;
        });

    }
}
