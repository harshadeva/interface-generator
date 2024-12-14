<?php

namespace Harshadeva\InterfaceGenerator;

use Harshadeva\InterfaceGenerator\Console\Commands\MakeInterface;
use Illuminate\Support\ServiceProvider;

class InterfaceGeneratorProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('make:interface',function($app){
            return new MakeInterface();
        });

        $this->commands(['make:interface']);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
