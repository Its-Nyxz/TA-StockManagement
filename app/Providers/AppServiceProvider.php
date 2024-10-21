<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
        Carbon::setLocale('ID');

        Gate::define('super&admin', function(){
            return Auth::user()->role_id != 3;
        });
        Gate::define('user', function(){
            return Auth::user()->role_id == 3;
        });
        Gate::define('super', function(){
            return Auth::user()->role_id == 1;
        });
    }
}
