<?php

namespace BlaiseBueno\LaravelDomainToolkit\Console\Commands;

use BlaiseBueno\LaravelDomainToolkit\Support\ResolvesStubPath;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeDto extends Command
{
    use ResolvesStubPath;

    protected $signature = "make:dto {name} {--force : Overwrite existing file}";
    protected $description = "Create a new DTO class.";

    public function handle(): int
    {
        $name = trim($this->argument('name'));

        if (empty($name)) {
            $this->error("Invalid DTO name.");
            return Command::FAILURE;
        }

        $path = $this->generateDto($name);

        if (!$path) {
            $this->warn("No files created.");
            return Command::FAILURE;
        }

        $this->components->info("DTO [{$path}] created successfully.");

        return Command::SUCCESS;
    }

    protected function generateDto(string $name): string|false
    {
        // 1. Split input into segments
        $segments = preg_split('/[\/\\\\]+/', $name);
        $segments = array_filter($segments);

        // 2. Extract class name (last segment)
        $rawClass = array_pop($segments);

        // 3. Normalize class name
        $class = Str::studly($rawClass);
        $class = preg_replace('/Dto$/i', '', $class); // remove existing suffix
        $class .= 'Dto';

        // 4. Normalize folder segments
        $folderNamespace = collect($segments)
            ->map(fn ($part) => Str::studly($part))
            ->implode('\\');

        // 5. Build namespace
        $namespace = $folderNamespace
            ? "App\\DTO\\{$folderNamespace}"
            : "App\\DTO";

        // 6. Build path
        $folderPath = $folderNamespace
            ? str_replace('\\', '/', $folderNamespace)
            : null;

        $path = $folderPath
            ? "app/DTO/{$folderPath}/{$class}.php"
            : "app/DTO/{$class}.php";

        $absolutePath = base_path($path);

        // 7. Load stub
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