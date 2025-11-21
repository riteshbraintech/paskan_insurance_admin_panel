<?php

namespace App\Providers;

use App\Listeners\LogUserActivity;
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

use App\Models\CMSPage;
use App\Observers\CMSPageObserver; 

use App\Models\Category;
use App\Observers\CategoryObserver;

use App\Models\Categoryformfield;
use App\Observers\CategoryformfieldObserver;

use App\Models\User;
use App\Observers\UserObserver;

use App\Models\Banner;
use App\Observers\BannerObserver;

use App\Models\FAQ;
use App\Observers\FAQObserver;

use App\Models\Article;
use App\Observers\ArticleObserver;
use App\Observers\UserActivityObserver;

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

        CMSPage::observe(CMSPageObserver::class);
        Category::observe(CategoryObserver::class);
        Categoryformfield::observe(CategoryformfieldObserver::class);
        User::observe(UserObserver::class);
        Banner::observe(BannerObserver::class);
        FAQ::observe(FAQObserver::class);
        Article::observe(ArticleObserver::class);
        User::observe(UserActivityObserver::class);

    }
}
