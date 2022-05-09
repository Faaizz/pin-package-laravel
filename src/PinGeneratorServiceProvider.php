<?php

namespace Faaizz\PinGenerator;

use Illuminate\Support\ServiceProvider;

class PinGenerator extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('generator', function ($app) {
            return new Generator();
        });
    }

}
