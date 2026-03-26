<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface EloquentInterface
{
    /**
     * Create a new model instance.
     */
    public function create(array $attributes): Model;

    /**
     * Find a model by its primary key.
     */
    public function find(int $id): ?Model;

    /**
     * Update a model by its primary key.
     */
    public function update(array $attributes, int $id): ?Model;

    /**
     * Delete a model by its primary key.
     */
    public function delete(int $id): bool;
}