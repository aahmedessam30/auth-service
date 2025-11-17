<?php

namespace App\Repositories;

use App\Contracts\Repositories\RepositoryContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements RepositoryContract
{
    protected Model $model;

    /**
     * Get all records.
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Get paginated records.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    /**
     * Find a record by ID.
     */
    public function find(int|string $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Find a record by ID or fail.
     */
    public function findOrFail(int|string $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new record.
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update a record.
     */
    public function update(int|string $id, array $data): bool
    {
        $record = $this->findOrFail($id);

        return $record->update($data);
    }

    /**
     * Delete a record.
     */
    public function delete(int|string $id): bool
    {
        $record = $this->findOrFail($id);

        return $record->delete();
    }

    /**
     * Find records by conditions.
     */
    public function findByConditions(array $conditions): Collection
    {
        $query = $this->model->query();

        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query->get();
    }
}
