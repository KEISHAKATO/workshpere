<?php

namespace App\Providers;

use App\Models\Profile;
use App\Policies\ProfilePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Profile::class => ProfilePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Admins can do anything
        Gate::before(function ($user, $ability) {
            return method_exists($user, 'isAdmin') && $user->isAdmin() ? true : null;
        });
    }
}
