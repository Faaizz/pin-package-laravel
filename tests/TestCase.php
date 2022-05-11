<?php

namespace Faaizz\PinGenerator\Tests;

use Faaizz\PinGenerator\PinGeneratorServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class TestCase extends TestbenchTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }
    protected function getPackageProviders($app)
    {
        return [
            PinGeneratorServiceProvider::class,
        ];
    }
}
