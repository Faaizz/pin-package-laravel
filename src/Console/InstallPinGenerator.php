<?php

namespace Faaizz\PinGenerator\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallPinGenerator extends Command
{
    protected $signature = 'pingenerator:install';

    protected $description = 'Install the PinGenerator package';

    public function handle()
    {
        $this->info('Installing PinGenerator...');

        $this->info('Publishing configuration...');

        $configFileName = 'pingenerator.php';

        if (! File::exists(config_path($configFileName))) {
            $this->publishConfig();
            $this->info('Published configuration');
        }

        $this->info('Installed PinGenerator');
    }

    private function publishConfig()
    {
        $params = [
            '--provider' => 'Faaizz\\PinGenerator\\PinGeneratorServiceProvider',
            '--tag' => 'config',
            '--force' => true,
        ];

        $this->call('vendor:publish', $params);
    }
}
