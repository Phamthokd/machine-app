<?php

namespace App\Providers;

use App\Support\FeatureAccess;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFive();

        Blade::if('feature', fn (string $feature) => FeatureAccess::allows(auth()->user(), $feature));
        Blade::if('featureany', fn (...$features) => FeatureAccess::any(auth()->user(), $features));
    }
}
