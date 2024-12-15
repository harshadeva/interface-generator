<?php

namespace Harshadeva\InterfaceGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Events\PublishingStubs;
use Illuminate\Support\Facades\Log;

class StubPublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interface-generator:stub-publish
                    {--existing : Publish and overwrite only the files that have already been published}
                    {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all stubs that are available for customization in harshadeva/interface-generator package';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! is_dir($stubsPath = $this->laravel->basePath('stubs/interface-generator'))) {
            (new Filesystem)->makeDirectory($stubsPath);
        }

        $stubs = [
             __DIR__.'/../../../stubs/interface.stub' => 'interface.stub',
             __DIR__.'/../../../stubs/repository.stub' => 'repository.stub',
        ];

        $this->laravel['events']->dispatch($event = new PublishingStubs($stubs));

        foreach ($event->stubs as $from => $to) {
            $to = $stubsPath . DIRECTORY_SEPARATOR . ltrim($to, DIRECTORY_SEPARATOR);

            if ((! $this->option('existing') && (! file_exists($to) || $this->option('force')))
                || ($this->option('existing') && file_exists($to))
            ) {
                file_put_contents($to, file_get_contents($from));
            }
        }

        $this->components->info('Harshadeva/InterfaceGenerator Stubs published successfully.');
    }
}
