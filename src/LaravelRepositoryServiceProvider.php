<?php

namespace BlaiseBueno\LaravelDomainToolkit;

use Illuminate\Support\ServiceProvider;

class DomainToolkitServiceProvider extends ServiceProvider
{
    /** 
    * Tag name used for publishing repository stubs. 
    */
    public const TAG = 'laravel-domain-toolkit';
    /**
     * Bootstrap any package services.
     *
     * Publishes repository stub files to the Laravel application's structure.
     * Files are only published when the `vendor:publish` Artisan command is run.
     * Existing files are skipped unless the `--force` flag is passed.
     *
     * Features:
     * - Only runs during `vendor:publish` command.
     * - Supports `--force` to overwrite existing files.
     * - Logs skipped files to the console.
     * - Tags publishable files under `laravel-domain-toolkit`.
     *
     * Usage:
     *  php artisan vendor:publish --tag=laravel-domain-toolkit
     *  php artisan vendor:publish --tag=laravel-domain-toolkit --force
     *
     * @return void
     */
    public function boot(): void
    {
        if (! $this->isRunningVendorPublish()) {
            return;
        }

        $publishable = [
            __DIR__ . '/stubs/RepositoryServiceProvider.php' => app_path('Providers/RepositoryServiceProvider.php'),
            __DIR__ . '/stubs/MakeRepository.php' => app_path('Console/Commands/MakeRepository.php'),
            __DIR__ . '/stubs/BaseRepository.php' => app_path('Repositories/BaseRepository.php'),
            __DIR__ . '/stubs/EloquentInterface.php' => app_path('Repositories/Interfaces/EloquentInterface.php'),
        ];

        $forcePublishing = in_array('--force', $_SERVER['argv'], true);

        $filtered = [];

        foreach ($publishable as $sourceFile => $targetFile) {
            $targetDir = dirname($targetFile);

            // Ensure the target directory exists
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Add to filtered list if target doesn't exist or --force is passed
            if (!file_exists($targetFile) || $forcePublishing) {
                $filtered[$sourceFile] = $targetFile;
            } else {
                // Log skipped file
                $this->logToConsole("Skipped: {$targetFile} already exists.");
            }
        }

        // Only register publishables if there's something new to publish
        if (!empty($filtered)) {
            $this->publishes($filtered, self::TAG);
        }
    }
    /**
     * Outputs a message to the terminal if running in console.
     *
     * Used for logging skipped file notices during publishing,
     * especially when files already exist and --force is not applied.
     *
     * @param string $message The message to output to the console.
     * @return void
     */
    protected function logToConsole(string $message): void
    {
        if ($this->isRunningVendorPublish()) {
            echo "{$message}" . PHP_EOL;
        }
    }
    /**
    * Determine if the current command is `vendor:publish`.
    *
    * This check ensures that stub publishing logic only runs when explicitly
    * invoked via `php artisan vendor:publish`, preventing unnecessary file
    * existence checks and console logging during other commands like `make:*`.
    *
    * @return bool True if the current CLI command is `vendor:publish`; false otherwise.
    */
    protected function isRunningVendorPublish(): bool
    {
        return $this->app->runningInConsole()
            && isset($_SERVER['argv'][1])
            && $_SERVER['argv'][1] === 'vendor:publish';
    }
        /**
     * Register any application services.
     *
     * This method is reserved for binding classes into the service container.
     * Currently no services are registered, but the method is defined for future use.
     *
     * @return void
     */
    public function register(): void
    {

    }
}
