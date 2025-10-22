<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SsoClient extends Model
{
    protected $fillable = [
        'name',
        'client_id',
        'client_secret',
        'redirect_uris',
        'is_active'
    ];

    protected $casts = [
        'redirect_uris' => 'array',
        'is_active' => 'boolean',
    ];

    public function authorizationCodes(): HasMany
    {
        return $this->hasMany(SsoAuthorizationCode::class, 'client_id');
    }

    public function accessTokens(): HasMany
    {
        return $this->hasMany(SsoAccessToken::class, 'client_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_sso_client_assignments');
    }

    public function isRedirectUriValid(string $uri): bool
    {
        return in_array($uri, $this->redirect_uris);
    }
}
