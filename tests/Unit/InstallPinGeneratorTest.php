<?php

namespace Faaizz\PinGenerator\Tests\Unit;

use Faaizz\PinGenerator\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallPinGeneratorTest extends TestCase
{
    public function testTheInstallCommandCreatesTheConfiguration()
    {
        if (File::exists(config_path('pingenerator.php'))) {
            unlink(config_path('pingenerator.php'));
        }

        $this->assertFalse(File::exists(config_path('pingenerator.php')));

        Artisan::Call('pingenerator:install');

        $this->assertTrue(File::exists(config_path('pingenerator.php')));
    }
}
