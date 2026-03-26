<?php

namespace BlaiseBueno\LaravelDomainToolkit\Console\Commands;

use Illuminate\Console\Command;

class MakeDomain extends Command
{
    protected $signature = "make:domain {name} {--force : Overwrite existing files}";
    protected $description = "Create a full domain scaffold (Model, Controller, Repository, Service).";

    public function handle(): int
    {
        $name = trim($this->argument('name'));

        if (empty($name)) {
            $this->error("Invalid domain name.");
            return Command::FAILURE;
        }

        $this->components->info("Generating domain [{$name}]...");
        $this->newLine();

        $this->createModel($name);
        $this->newLine();

        $this->createController($name);
        $this->newLine();

        $this->createRepository($name);
        $this->newLine();

        $this->createService($name);

        $this->newLine();
        $this->components->info("{$name} domain scaffold created successfully.");

        return Command::SUCCESS;
    }

    protected function createModel(string $name): void
    {
        $this->components->info("Creating {$name} model...");

        $this->call('make:model', [
            'name' => $name
        ]);
    }

    protected function createController(string $name): void
    {
        $this->components->info("Creating {$name} controller...");

        $this->call('make:controller', [
            'name' => "{$name}Controller"
        ]);
    }

    protected function createRepository(string $name): void
    {
        $this->components->info("Creating {$name} repository...");

        $this->call('make:repository', [
            'name' => $name,
            '--force' => $this->option('force')
        ]);
    }

    protected function createService(string $name): void
    {
        $this->components->info("Creating {$name} service...");

        $this->call('make:service', [
            'name' => $name,
            '--force' => $this->option('force')
        ]);
    }
}