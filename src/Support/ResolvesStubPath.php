<?php

namespace BlaiseBueno\LaravelDomainToolkit\Support;

use Illuminate\Support\Facades\File;

trait ResolvesStubPath
{
    protected function resolveStub(string $stub): string
    {
        $custom = base_path("stubs/domain-toolkit/{$stub}");

        if (File::exists($custom)) {
            return $custom;
        }

        $vendor = __DIR__ . '/../Stubs/' . $stub;

        if (!File::exists($vendor)) {
            throw new \RuntimeException("Stub [{$stub}] not found.");
        }

        return $vendor;
    }
}