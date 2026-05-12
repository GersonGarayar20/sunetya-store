<?php

namespace Webkul\Culqi\Providers;

use Illuminate\Support\ServiceProvider;

class CulqiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/payment-methods.php',
            'payment_methods'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/system.php',
            'core'
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'culqi');

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'culqi');
    }
}
