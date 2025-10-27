<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Models\Admin;
use App\Observers\AdminObserver;

use App\Models\Lead;
use App\Observers\LeadObserver;


use App\Models\Log;
use App\Observers\LogObserver;

use App\Models\Bid;
use App\Observers\BidObserver;

use App\Models\Client;
use App\Observers\ClientObserver;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Admin::observe(AdminObserver::class);
        Lead::observe(LeadObserver::class);
        Bid::observe(BidObserver::class);
        Log::observe(LogObserver::class);
        Client::observe(ClientObserver::class);
    }
}
