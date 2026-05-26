<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\WorkOrder;
use App\Observers\WorkOrderObserver;

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
        // Register WorkOrder Observer
        WorkOrder::observe(WorkOrderObserver::class);
    }
}