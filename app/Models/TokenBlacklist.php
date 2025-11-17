<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TokenBlacklist extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'jti';

    protected $table = 'token_blacklist';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'jti',
        'user_id',
        'expires_at',
        'blacklisted_at',
        'reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'blacklisted_at' => 'datetime',
            'user_id' => 'integer',
        ];
    }

    /**
     * Get the user that owns the blacklisted token.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
