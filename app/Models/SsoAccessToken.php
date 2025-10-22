<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SsoAccessToken extends Model
{
    protected $fillable = [
        'token',
        'user_id',
        'client_id',
        'expires_at',
        'revoked'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'revoked' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(SsoClient::class, 'client_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return !$this->revoked && !$this->isExpired();
    }
}
