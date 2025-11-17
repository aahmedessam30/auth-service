<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryContract;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository implements UserRepositoryContract
{
    private const CACHE_TTL = 600; // 10 minutes

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * Find a user by email address.
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Create a new user.
     */
    public function createUser(array $data): User
    {
        return $this->model->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Verify user credentials.
     */
    public function verifyCredentials(string $email, string $password): ?User
    {
        $user = $this->findByEmail($email);

        if (! $user) {
            return null;
        }

        if (! Hash::check($password, $user->password)) {
            return null;
        }

        return $user;
    }

    /**
     * Find a user by ID.
     * Uses cache to reduce database queries.
     */
    public function findById(int $id): ?User
    {
        return Cache::remember("user_profile:{$id}", self::CACHE_TTL, function () use ($id) {
            return $this->model->find($id);
        });
    }

    /**
     * Clear user cache (call after updates).
     */
    public function clearUserCache(int $id): void
    {
        Cache::forget("user_profile:{$id}");
    }
}
