<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'token' => $this->resource['token'],
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl', 3600),
            'refresh_token' => $this->resource['refresh_token'] ?? null,
            'user' => new UserResource($this->resource['user']),
        ];
    }
}
