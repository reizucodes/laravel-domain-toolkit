<?php

namespace BlaiseBueno\LaravelDomainToolkit;

use Illuminate\Support\ServiceProvider;
use BlaiseBueno\LaravelDomainToolkit\Console\Commands\MakeDomain;
use BlaiseBueno\LaravelDomainToolkit\Console\Commands\MakeRepository;
use BlaiseBueno\LaravelDomainToolkit\Console\Commands\MakeService;
use BlaiseBueno\LaravelDomainToolkit\Console\Commands\MakeDto;

class DomainToolkitServiceProvider extends ServiceProvider
{
    public const TAG = 'laravel-domain-toolkit';

    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        // Register commands (stay in vendor)
        $this->commands([
            MakeDomain::class,
            MakeRepository::class,
            MakeService::class,
            MakeDto::class,
        ]);

        // Publish only architecture + stubs
        $this->publishes([
            /*
            |--------------------------------------------------------------------------
            | Repository Infrastructure
            |--------------------------------------------------------------------------
            */
            __DIR__.'/Repositories/BaseRepository.php'
                => app_path('Repositories/BaseRepository.php'),

            __DIR__.'/Repositories/Contracts/EloquentInterface.php'
                => app_path('Repositories/Interfaces/EloquentInterface.php'),

            /*
            |--------------------------------------------------------------------------
            | Repository Provider
            |--------------------------------------------------------------------------
            */
            __DIR__.'/Providers/RepositoryServiceProvider.php'
                => app_path('Providers/RepositoryServiceProvider.php'),

            /*
            |--------------------------------------------------------------------------
            | Stubs
            |--------------------------------------------------------------------------
            */
            __DIR__.'/Stubs'
                => base_path('stubs/domain-toolkit'),

        ], self::TAG);
    }

    public function register(): void
    {
        //
    }
}