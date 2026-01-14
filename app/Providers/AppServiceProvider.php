<?php

namespace App\Providers;

use App\Adapter\Out\Persistence\BookingRepository;
use App\Adapter\Out\Persistence\ConsultantRepository;
use App\Application\Port\Out\LoadConsultantPort;
use App\Application\Port\Out\SaveBookingPort;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            LoadConsultantPort::class,
            ConsultantRepository::class
        );

        $this->app->bind(
            SaveBookingPort::class,
            BookingRepository::class
        );
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
