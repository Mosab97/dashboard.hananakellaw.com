<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /*if (App::isLocal()) {
            App::register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }*/
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        App::setLocale(session('locale', 'ar'));
    }
}
