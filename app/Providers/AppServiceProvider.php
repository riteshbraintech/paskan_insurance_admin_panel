<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        // sharing permisiion adn option to globally view
        $per = config("static.permission");
        $perOpt = config("static.permissionOpt");
        View::share('per', $per);
        View::share('perOpt', $perOpt);


        // blade directive for managing permission in templates
        // Blade::directive('cando', function ($role) {
        //     return "&lt;?php if (is_permission($role)) &#123; ?&gt;";
        // });

        // Blade::directive('endcando', function ($role) {
        //     return '&lt;?php &#125; ?&gt;';
        // });


    }
}
