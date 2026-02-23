<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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
        // Custom validation rule: date must be today or in the future
        Validator::extend('today_or_future', function ($attribute, $value, $parameters, $validator) {
            $date = Carbon::parse($value);
            $today = Carbon::now()->startOfDay();
            return $date->greaterThanOrEqualTo($today);
        }, 'The :attribute must be today or a future date.');
    }
}

