<?php

namespace App\Providers;

// 1. PASTIKAN INI DI-IMPORT DI ATAS
use App\Models\WorkOrder;
use App\Observers\WorkOrderObserver;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * The model observers for your application.
     *
     * @var array
     */
    protected $observers = [
        // 2. PASTIKAN BARIS INI ADA
        WorkOrder::class => [WorkOrderObserver::class],
    ]; // <--- Pastikan tidak ada koma yang hilang

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}   