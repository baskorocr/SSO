<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SsoAuthorizationCode extends Model
{
    protected $fillable = [
        'code',
        'user_id',
        'client_id',
        'redirect_uri',
        'expires_at',
        'used'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
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
        return !$this->used && !$this->isExpired();
    }
}
