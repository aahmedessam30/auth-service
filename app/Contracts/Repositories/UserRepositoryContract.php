<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\User;

interface UserRepositoryContract
{
    /**
     * Find a user by email address.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Create a new user.
     */
    public function createUser(array $data): User;

    /**
     * Verify user credentials.
     */
    public function verifyCredentials(string $email, string $password): ?User;

    /**
     * Find a user by ID.
     */
    public function findById(int $id): ?User;
}
