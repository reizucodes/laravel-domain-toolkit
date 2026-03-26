<?php

namespace BlaiseBueno\LaravelDomainToolkit\Console\Commands;

use BlaiseBueno\LaravelDomainToolkit\Support\ResolvesStubPath;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeDto extends Command
{
    use ResolvesStubPath;

    protected $signature = "make:dto {name} {folder?} {--force : Overwrite existing file}";
    protected $description = "Create a new DTO class.";

    public function handle(): int
    {
        $name = trim($this->argument('name'));
        $folder = $this->argument('folder');

        if (empty($name)) {
            $this->error("Invalid DTO name.");
            return Command::FAILURE;
        }

        $path = $this->generateDto($name, $folder);

        if (!$path) {
            $this->warn("No files created.");
            return Command::FAILURE;
        }

        $this->components->info("DTO [{$path}] created successfully.");

        return Command::SUCCESS;
    }

    protected function generateDto(string $name, ?string $folder): string|false
    {
        $class = Str::studly($name);

        if (!str_ends_with($class, 'Dto')) {
            $class .= 'Dto';
        }

        $folder = $folder
            ? collect(preg_split('/[\/\\\\]+/', $folder))
                ->filter()
                ->map(fn ($part) => Str::studly($part))
                ->implode('\\')
            : null;

        $namespace = $folder
            ? "App\\DTO\\{$folder}"
            : "App\\DTO";

        $path = $folder
            ? "app/DTO/" . str_replace('\\', '/', $folder) . "/{$class}.php"
            : "app/DTO/{$class}.php";

        $absolutePath = base_path($path);

        $stub = File::get($this->resolveStub('dto.stub'));

        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $class],
            $stub
        );

        $created = $this->createFile($absolutePath, $content, $class);

        return $created ? $path : false;
    }

    protected function createFile(string $path, string $content, string $class): bool
    {
        $directory = dirname($path);

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::exists($path)) {

            if (!$this->option('force')) {

                if (!$this->confirm("DTO {$class} already exists. Overwrite?")) {
                    $this->warn("Skipped: {$path}");
                    return false;
                }

            }
        }

        File::put($path, $content);

        return true;
    }
}