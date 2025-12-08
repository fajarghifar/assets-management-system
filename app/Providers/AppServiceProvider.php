<?php

namespace App\Providers;

use App\Models\Area;
use App\Models\Item;
use App\Models\Location;
use App\Observers\AreaObserver;
use App\Observers\ItemObserver;
use App\Observers\LocationObserver;
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
        Area::observe(AreaObserver::class);
        Location::observe(LocationObserver::class);
        Item::observe(ItemObserver::class);
    }
}
