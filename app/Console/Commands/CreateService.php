<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CreateService extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service class';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Services/{$name}.php");

        if ($this->files->exists($path)) {
            $this->error("Service {$name} already exists!");
            return;
        }

        $this->createDirectory($path);

        $stub = $this->getStub();
        $content = str_replace('{{ class }}', $name, $stub);

        $this->files->put($path, $content);
        $this->info("Service {$name} created successfully.");
    }

    protected function getStub()
    {
        return file_get_contents(resource_path('stubs/service.stub'));
    }

    protected function createDirectory($path)
    {
        $directory = dirname($path);

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }
}
