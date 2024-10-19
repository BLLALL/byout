<?php

namespace App\Providers;

use DB;
use Illuminate\Support\Facades\Auth;
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
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
        Auth::shouldUse('api');

        // DB::listen(
        //     function($query) {
        //         dump($query->sql);
        //         dump($query->bindings);
        //         dump($query->time);
        //     }
        // );
    }
}
