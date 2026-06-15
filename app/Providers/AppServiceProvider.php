<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set locale Indonesia untuk Carbon
        \Carbon\Carbon::setLocale('id');

        // Default pagination view - TailAdmin style
        Paginator::defaultView('vendor.pagination.tailwind');

        // Gates untuk role-based authorization
        Gate::define('admin', function ($user) {
            return in_array($user->role, ['admin', 'super_admin']);
        });

        Gate::define('audit', function ($user) {
            return in_array($user->role, ['admin', 'audit', 'super_admin']);
        });

        Gate::define('manager', function ($user) {
            return in_array($user->role, ['admin', 'manager', 'super_admin']);
        });

        Gate::define('sales', function ($user) {
            return in_array($user->role, ['admin', 'sales', 'super_admin']);
        });
    }
}
