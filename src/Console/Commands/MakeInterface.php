<?php

namespace Harshadeva\InterfaceGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class MakeInterface extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:interface {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $files;


    public function __construct()
    {
        parent::__construct();
        $files = App::make(Filesystem::class);
        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = ucFirst($this->argument('name'));
        $this->createRepository($name);
        $this->createInterface($name);
        $this->updateServiceProvider($name);
        $this->info("Repository for {$name} created successfully.");
    }

    protected function createRepository($name)
    {
        $repositoryDirectory = app_path("Repositories/{$name}");
        $repositoryFile = "{$repositoryDirectory}/{$name}Repository.php";

        if (!$this->files->isDirectory($repositoryDirectory)) {
            $this->files->makeDirectory($repositoryDirectory, 0755, true);
        }

        $stub = $this->getRepositoryStub();
        $stub = str_replace('{{name}}', $name, $stub);

        $this->files->put($repositoryFile, $stub);
    }

    protected function createInterface($name)
    {
        $repositoryDirectory = app_path("Repositories/{$name}");
        $interfaceFile = "{$repositoryDirectory}/{$name}RepositoryInterface.php";

        if (!$this->files->isDirectory($repositoryDirectory)) {
            $this->files->makeDirectory($repositoryDirectory, 0755, true);
        }

        $stub = $this->getInterfaceStub();
        $stub = str_replace('{{name}}', $name, $stub);

        $this->files->put($interfaceFile, $stub);
    }

    protected function createServiceProvider($path){
        $stub = $this->getProviderStub();
        $stub = str_replace('{{ namespace }}','App\Providers' , $stub);
        $stub = str_replace('{{ class }}','RepositoryServiceProvider' , $stub);

        $this->files->put($path, $stub);
    }

    protected function appendNewProviderToArray($providerName)
    {
        $providerBootstrapPath = base_path('bootstrap/providers.php');
        $providerContent = $this->files->get($providerBootstrapPath);
        
        $bindStatement = 'App\Providers\\' . $providerName;
        
        // Convert the file content to a PHP array
        $providers = include $providerBootstrapPath;
        
        // Check if the provider already exists
        if (!in_array($bindStatement, $providers)) {
            // Add the new provider
            $providers[] = $bindStatement;
    
            // Convert the array back to a PHP file string
            $newProviderContent = "<?php\n\nreturn [\n    " . implode(",\n    ", array_map(function ($provider) {
                return strpos($provider, '::class') !== false ? $provider : $provider . '::class';
            }, $providers)) . "\n];\n";
            
            // Save the new content back to the file
            $this->files->put($providerBootstrapPath, $newProviderContent);
            $this->info($providerName . " created and bound.");
        } else {
            $this->error($providerName . " creation failed; provider already exists 2.");
        }
    }
   

    protected function updateServiceProvider($name)
    {
        $providerName = 'RepositoryServiceProvider';
        $providerPath = app_path('Providers/'.$providerName.'.php');
        $interface = "\\App\\Repositories\\{$name}\\{$name}RepositoryInterface";
        $repository = "\\App\\Repositories\\{$name}\\{$name}Repository";

        $bindStatement = "\$this->app->bind({$interface}::class, {$repository}::class);";

        if (!$this->files->exists($providerPath)) {
            $this->createServiceProvider($providerPath);
            $this->appendNewProviderToArray($providerName);
        }

        $providerContent = $this->files->get($providerPath);

        if (strpos($providerContent, $bindStatement) === false) {
            $pattern = '/(\$this->app->bind\(.*?\);)(?!.*\$this->app->bind)/s';
            $replacement = "$1\n        $bindStatement";
            $providerContent = preg_replace($pattern, $replacement, $providerContent);

            $this->files->put($providerPath, $providerContent);
            $this->info("Repository bindings for {$name} added to RepositoryServiceProvider.");
        } else {
            $this->info("Repository bindings for {$name} already exist in RepositoryServiceProvider.");
        }
    }

    protected function getRepositoryStub()
    {
        $publishedStubPath = base_path('stubs/interface-generator/repository.stub');
        $unpublishedStubPath = __DIR__ . '/../../../stubs/repository.stub';
        if(file_exists($publishedStubPath)) return $this->files->get($publishedStubPath);
        return $this->files->get($unpublishedStubPath);
    }

    protected function getInterfaceStub()
    {
        $publishedStubPath = base_path('stubs/interface-generator/interface.stub');
        $unpublishedStubPath = __DIR__ . '/../../../stubs/interface.stub';
        if(file_exists($publishedStubPath)) return $this->files->get($publishedStubPath);
        return $this->files->get($unpublishedStubPath);
    }
   
    protected function getProviderStub()
    {
        $publishedStubPath = base_path('stubs/provider.stub');
        $unpublishedStubPath = __DIR__ . '/../../../stubs/provider.stub';
        if(file_exists($publishedStubPath)) return $this->files->get($publishedStubPath);
        return $this->files->get($unpublishedStubPath);
    }
}
