<?php

namespace Faaizz\PinGenerator\Tests;

use Faaizz\PinGenerator\PinGeneratorServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class TestCase extends TestbenchTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            PinGeneratorServiceProvider::class,
        ];
    }
}
