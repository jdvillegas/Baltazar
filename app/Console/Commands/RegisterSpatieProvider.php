<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RegisterSpatieProvider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spatie:register-provider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register the Spatie Permission Service Provider';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $providersFile = base_path('bootstrap/providers.php');
        
        if (!File::exists($providersFile)) {
            $this->error('The providers.php file does not exist.');
            return 1;
        }

        $providers = include $providersFile;
        
        if (!is_array($providers)) {
            $this->error('The providers.php file does not return an array.');
            return 1;
        }

        $spatieProvider = 'Spatie\\Permission\\PermissionServiceProvider::class';
        
        if (in_array($spatieProvider, $providers)) {
            $this->info('The Spatie Permission Service Provider is already registered.');
            return 0;
        }

        $providers[] = $spatieProvider;
        
        $content = "<?php\n\nreturn [\n";
        
        foreach ($providers as $provider) {
            $content .= "    " . $provider . ",\n";
        }
        
        $content .= "];\n";
        
        File::put($providersFile, $content);
        
        $this->info('The Spatie Permission Service Provider has been registered successfully.');
        
        return 0;
    }
}
