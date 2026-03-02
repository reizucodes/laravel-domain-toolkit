<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "make:repository {name} {--s : Include a service file} {--force : Overwrite existing files}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class with interface implementation. Optionally include a service class (--s) and force overwrite files (--force).';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $includeService = $this->option('s');
        $serviceOnly = $this->option('s-only');

        if (empty($name)) {
            $this->error("Invalid repository name.");
            return Command::FAILURE;
        }

        $interfaceCreated = $repoCreated = $serviceCreated = false;

        if($serviceOnly) {
            $serviceCreated = $this->generateService($name);
        } else {
            $interfaceCreated = $this->generateInterface($name);
            $repoCreated = $this->generateRepository($name);
            $serviceCreated = $includeService ? $this->generateService($name) : false;
            $this->bindToServiceProvider($name);
        }

        if (!$interfaceCreated && !$repoCreated && !$serviceCreated) {
            $this->warn("No files created or overwritten.");
            return Command::FAILURE;
        }

        if($serviceOnly && $serviceCreated) {
            $message = ["{$name} Service created successfully."];
        } else {
            $message = ["{$name} Repository and Interface created successfully."];
            if ($includeService) {
                $message[] = "Service file included.";
            }
        }

        $this->info(implode(' ', $message));

        return Command::SUCCESS;
    }

    /**
     * Generate the repository interface file.
     *
     * @param string $name
     * @return bool True if created or overwritten, false if skipped.
     */
    protected function generateInterface(string $name): bool
    {
        $namespace = 'App\Repositories\Interfaces';
        $path = $this->getBasePath("app/Repositories/Interfaces/{$name}Interface.php");
        return $this->createFile($path, $this->getInterfaceTemplate($name, $namespace));
    }

    /**
     * Generate the repository class file, optionally prompting for model creation.
     *
     * @param string $name
     * @return bool True if created or overwritten, false if skipped.
     */
    protected function generateRepository(string $name): bool
    {
        $modelPath = $this->getBasePath("app/Models/{$name}.php");
        if (!File::exists($modelPath)) {
            if ($this->confirm("Model {$name} does not exist. Create it?")) {
                $this->call('make:model', ['name' => $name]);
            } else {
                $this->warn("Model {$name} not created. Manually run php artisan make:model $name");
            }
        }
        $namespace = 'App\Repositories';
        $path = $this->getBasePath("app/Repositories/{$name}Repository.php");
        return $this->createFile($path, $this->getRepositoryTemplate($name, $namespace));
    }

    /**
     * Generate the optional service class file.
     *
     * @param string $name
     * @return bool True if created or overwritten, false if skipped.
     */
    protected function generateService(string $name): bool
    {
        $namespace = 'App\Services';
        $path = $this->getBasePath("app/Services/{$name}Service.php");
        return $this->createFile($path, $this->getServiceTemplate($name, $namespace));
    }

    /**
     * Add binding statement to RepositoryServiceProvider boot method.
     *
     * @param string $name
     * @return void
     */
    protected function bindToServiceProvider(string $name): void
    {
        $providerPath = $this->getBasePath('app/Providers/RepositoryServiceProvider.php');

        if (!File::exists($providerPath)) {
            return;
        }

        $bindStatement = "\n        \$this->app->bind(\\App\\Repositories\\Interfaces\\{$name}Interface::class, \\App\\Repositories\\{$name}Repository::class);";
        $content = file_get_contents($providerPath);

        if (!str_contains($content, $bindStatement)) {
            $content = preg_replace('/(public function boot\(\)\s*\{)/', "$1" . $bindStatement, $content);
            File::put($providerPath, $content);
        }
    }

    /**
     * Get the base path with optional appended path.
     *
     * @param string $path
     * @return string
     */
    protected function getBasePath(string $path = ''): string
    {
        return base_path($path);
    }

    /**
     * Create or overwrite a file with given content, with confirmation for existing files.
     *
     * @param string $path
     * @param string $content
     * @return bool True if file created or overwritten, false if skipped.
     */
    protected function createFile(string $path, string $content): bool
    {
        $directory = dirname($path);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::exists($path)) {
            if (!$this->option('force')) {
                if (!$this->confirm("File {$path} already exists. Overwrite?")) {
                    $this->warn("Skipped: {$path}");
                    return false;
                }
            }
        }

        File::put($path, $content);
        return true;
    }

    /**
     * Get the PHP code template for the interface.
     *
     * @param string $name
     * @param string $namespace
     * @return string
     */
    protected function getInterfaceTemplate(string $name, string $namespace): string
    {
        return "<?php\n\nnamespace {$namespace};\n\ninterface {$name}Interface\n{\n    // Define methods here\n}\n";
    }

    /**
     * Get the PHP code template for the repository class.
     *
     * @param string $name
     * @param string $namespace
     * @return string
     */
    protected function getRepositoryTemplate(string $name, string $namespace): string
    {
        return "<?php\n\nnamespace {$namespace};\n\nuse App\Repositories\Interfaces\\{$name}Interface;\nuse App\Models\\{$name};\n\nclass {$name}Repository extends BaseRepository implements {$name}Interface\n{\n    public function __construct({$name} \$model)\n    {\n        parent::__construct(\$model);\n    }\n\n    // Define methods here\n}\n";
    }

    /**
     * Get the PHP code template for the service class.
     *
     * @param string $name
     * @param string $namespace
     * @return string
     */
    protected function getServiceTemplate(string $name, string $namespace): string
    {
        return "<?php\n\nnamespace {$namespace};\n\nclass {$name}Service\n{\n    // Define methods here\n}\n";
    }
}
