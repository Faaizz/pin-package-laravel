<?php

namespace Faaizz\PinGenerator\Tests;

use Faaizz\PinGenerator\PinGeneratorServiceProvider;

class TestcCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            PinGeneratorServiceProvider::class,
        ];
    }
}
