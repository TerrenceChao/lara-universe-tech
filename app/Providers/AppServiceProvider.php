<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Lottery\GameService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('App\Domain\Lottery\GameService', function ($app) {
            $vendorRepo = $app->make('App\Repositories\VendorRepository');
            $gameVendorMappingRepo = $app->make('App\Repositories\GameVendorMappingRepository');
            return GameService::instance($vendorRepo, $gameVendorMappingRepo);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
