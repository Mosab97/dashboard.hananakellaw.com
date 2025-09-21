<?php

namespace App\Providers;

use App\Services\Payments\MyFatoorahService;
use Illuminate\Support\ServiceProvider;

class MyFatoorahServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('myfatoorah.service', function ($app) {
            return new MyFatoorahService;
        });
    }
}
