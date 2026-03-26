<?php

namespace BlaiseBueno\LaravelDomainToolkit\Support;

trait ResolvesToolkitClasses
{
    protected function resolveServiceReturn(): string
    {
        if (class_exists(\App\Support\ServiceReturn::class)) {
            return \App\Support\ServiceReturn::class;
        }

        return \BlaiseBueno\LaravelDomainToolkit\Support\ServiceReturn::class;
    }
}