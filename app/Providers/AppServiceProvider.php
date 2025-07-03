<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ActuacionService;
use App\Services\Contracts\ActuacionServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ActuacionServiceInterface::class, function ($app) {
            return new ActuacionService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
