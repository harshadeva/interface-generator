<?php

namespace Harshadeva\InterfaceGenerator;

use Harshadeva\InterfaceGenerator\Console\Commands\MakeInterface;
use Harshadeva\InterfaceGenerator\Console\Commands\StubPublishCommand as CommandsStubPublishCommand;
use Illuminate\Foundation\Console\StubPublishCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
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
        $this->app->singleton('interface-generator:stub-publish',function($app){
            return new CommandsStubPublishCommand();
        });

        $this->commands(['make:interface','interface-generator:stub-publish']);

        $this->app->extend(StubPublishCommand::class, function ($command) {
            Artisan::call('interface-generator:stub-publish');
            return $command;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
