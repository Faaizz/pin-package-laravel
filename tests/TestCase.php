<?php

namespace Faaizz\PinGenerator\Tests;

use Faaizz\PinGenerator\PinGeneratorServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class TestCase extends TestbenchTestCase
{
    use RefreshDatabase;

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
