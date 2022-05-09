<?php

namespace Faaizz\PinGenerator;

use Faaizz\PinGenerator\Console\InstallPinGenerator;
use Illuminate\Support\ServiceProvider;

class PinGeneratorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('generator', function ($app) {
            return new Generator();
        });

        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'pingenerator');
    }

    public function boot()
    {
        $this->commands([
            InstallPinGenerator::class,
        ]);

        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('pingenerator.php'),
        ], 'config');
    }
}
