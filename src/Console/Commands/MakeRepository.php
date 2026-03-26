<?php

namespace BlaiseBueno\LaravelDomainToolkit\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepository extends Command
{
    protected $signature = "make:repository {name} {--force : Overwrite existing files}";
    protected $description = "Create a repository and interface.";

    public function handle(): int
    {
        $name = trim($this->argument('name'));

        if (empty($name)) {
            $this->error("Invalid repository name.");
            return Command::FAILURE;
        }

        $interfaceCreated = $this->generateInterface($name);
        $repoCreated = $this->generateRepository($name);

        if ($repoCreated) {
            $this->components->info("Repository [app/Repositories/{$name}Repository.php] created successfully.");
        }

        if ($interfaceCreated) {
            $this->components->info("Interface [app/Repositories/Interfaces/{$name}Interface.php] created successfully.");
        }

        $bindingAdded = $this->bindToServiceProvider($name);

        if ($bindingAdded) {
            $this->info("{$name}Interface -> {$name}Repository bound.");
        }

        return Command::SUCCESS;
    }

    protected function generateInterface(string $name): bool
    {
        $class = "{$name}Interface";
        $namespace = "App\Repositories\Interfaces";

        $path = base_path("app/Repositories/Interfaces/{$class}.php");

        $stub = File::get($this->resolveStub('repository-interface.stub'));

        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $class],
            $stub
        );

        return $this->createFile($path, $content, $class);
    }

    protected function generateRepository(string $name): bool
    {
        $class = "{$name}Repository";
        $namespace = "App\Repositories";

        $path = base_path("app/Repositories/{$class}.php");

        $stub = File::get($this->resolveStub('repository.stub'));

        $content = str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
                '{{ interface }}',
                '{{ model }}',
            ],
            [
                $namespace,
                $class,
                "{$name}Interface",
                $name,
            ],
            $stub
        );

        return $this->createFile($path, $content, $class);
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
            if (!$this->confirm("{$class} already exists. Overwrite?")) {
                return false;
            }
        }

        File::put($path, $content);

        return true;
    }

    protected function bindToServiceProvider(string $name): bool
    {
        $providerPath = base_path('app/Providers/RepositoryServiceProvider.php');

        if (!File::exists($providerPath)) {
            return false;
        }

        $binding = "\$this->app->bind(\\App\\Repositories\\Interfaces\\{$name}Interface::class, \\App\\Repositories\\{$name}Repository::class);";

        $content = File::get($providerPath);

        if (str_contains($content, $binding)) {
            return false;
        }

        $content = preg_replace(
            '/(public function boot\(\)\s*\{)/',
            "$1\n        {$binding}",
            $content
        );

        File::put($providerPath, $content);

        return true;
    }
}