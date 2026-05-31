<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
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
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
  public function boot(): void
    {
        // Intercept every 'can:' check in the system
        Gate::before(function ($user, $ability) {
            
            // 1. Safety check: Ensure the user actually has a role attached
            if (!$user->role || !isset($user->role->permissions)) {
                return null; 
            }

            $permissions = $user->role->permissions;

            // 2. Super Admin Override: If they have 'all', grant access to everything
            if (is_array($permissions) && in_array('all', $permissions)) {
                return true;
            }

            // 3. Specific Permission Check: Grant access if their role has the specific ability
            if (is_array($permissions) && in_array($ability, $permissions)) {
                return true;
            }

            // Return null to let the system fall through to default denial (403)
            return null;
        });
    }
}
