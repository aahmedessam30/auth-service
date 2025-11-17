<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface RepositoryContract
{
    /**
     * Get all records.
     */
    public function all(): Collection;

    /**
     * Get paginated records.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a record by ID.
     */
    public function find(int|string $id): ?Model;

    /**
     * Find a record by ID or fail.
     */
    public function findOrFail(int|string $id): Model;

    /**
     * Create a new record.
     */
    public function create(array $data): Model;

    /**
     * Update a record.
     */
    public function update(int|string $id, array $data): bool;

    /**
     * Delete a record.
     */
    public function delete(int|string $id): bool;

    /**
     * Find records by conditions.
     */
    public function findByConditions(array $conditions): Collection;
}
