<?php

namespace BlaiseBueno\LaravelDomainToolkit\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use BlaiseBueno\LaravelDomainToolkit\Support\ResolvesToolkitClasses;

class MakeService extends Command
{
    use ResolvesToolkitClasses;

    protected $signature = "make:service {name} {--force : Overwrite existing file}";
    protected $description = "Create a new service class.";

    public function handle(): int
    {
        $name = trim($this->argument('name'));

        if (empty($name)) {
            $this->error("Invalid service name.");
            return Command::FAILURE;
        }

        $path = $this->generateService($name);

        if (!$path) {
            $this->warn("No files created.");
            return Command::FAILURE;
        }

        $this->components->info("Service [{$path}] created successfully.");

        return Command::SUCCESS;
    }

    protected function generateService(string $name): string|false
    {
        $class = $name . 'Service';
        $namespace = 'App\Services';

        $path = "app/Services/{$class}.php";
        $absolutePath = base_path($path);

        $stub = File::get($this->resolveStub('service.stub'));

        $serviceReturn = $this->resolveServiceReturn();

        $content = str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
                '{{ interface }}',
                '{{ serviceReturnNamespace }}'
            ],
            [
                $namespace,
                $class,
                "{$name}Interface",
                $serviceReturn
            ],
            $stub
        );

        $created = $this->createFile($absolutePath, $content, $class);

        return $created ? $path : false;
    }

    protected function resolveStub(string $stub): string
    {
        $custom = base_path("stubs/domain-toolkit/{$stub}");

        if (File::exists($custom)) {
            return $custom;
        }

        return __DIR__ . '/../../Stubs/' . $stub;
    }

    protected function createFile(string $path, string $content, string $class): bool
    {
        $directory = dirname($path);

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::exists($path) && !$this->option('force')) {
            if (!$this->confirm("Service {$class} already exists. Overwrite?")) {
                $this->warn("Skipped: {$path}");
                return false;
            }
        }

        File::put($path, $content);

        return true;
    }
}