<?php

namespace App\Providers;

use App\Models\Area;
use App\Models\Item;
use App\Models\Location;
use App\Models\ItemStock;
use App\Observers\AreaObserver;
use App\Observers\ItemObserver;
use App\Models\FixedItemInstance;
use App\Observers\LocationObserver;
use App\Observers\ItemStockObserver;
use App\Models\InstalledItemInstance;
use Illuminate\Support\ServiceProvider;
use App\Observers\FixedItemInstanceObserver;
use App\Observers\InstalledItemInstanceObserver;

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
        FixedItemInstance::observe(FixedItemInstanceObserver::class);
        InstalledItemInstance::observe(InstalledItemInstanceObserver::class);
        ItemStock::observe(ItemStockObserver::class);
    }
}
