<?php

namespace App\Repositories;

use App\Repositories\Interfaces\EloquentInterface;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements EloquentInterface
{
    protected Model $model;

    /**
     * BaseRepository constructor.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Create a new model instance.
     */
    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    /**
     * Find a model by ID.
     */
    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Update a model by ID.
     */
    public function update(array $attributes, int $id): ?Model
    {
        $model = $this->model->find($id);

        if (! $model) {
            return null;
        }

        $model->update($attributes);

        return $model;
    }

    /**
     * Delete a model by ID.
     */
    public function delete(int $id): bool
    {
        $model = $this->model->find($id);

        if (! $model) {
            return false;
        }

        return (bool) $model->delete();
    }
}