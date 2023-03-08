<?php

namespace App\Providers;
use App\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; 
use App\Http\View\Composers\ComposerNotifications;
use App\Http\View\Composers\ComposerOverview;
use App\Http\View\Composers\ComposerGlobals;
use App\Models\Company;

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

     View::composer('*', ComposerOverview::class);
     View::composer(['pages.*'], ComposerNotifications::class);
    }
}
