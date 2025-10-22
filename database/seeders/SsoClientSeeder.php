<?php

namespace Database\Seeders;

use App\Models\SsoClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SsoClientSeeder extends Seeder
{
    public function run()
    {
        SsoClient::create([
            'name' => 'Test Application',
            'client_id' => 'test-client-id',
            'client_secret' => 'test-client-secret',
            'redirect_uris' => [
                'http://localhost:3000/auth/callback',
                'https://testapp.example.com/auth/callback'
            ],
            'is_active' => true,
        ]);

        SsoClient::create([
            'name' => 'Production App',
            'client_id' => Str::uuid(),
            'client_secret' => Str::random(64),
            'redirect_uris' => [
                'https://prodapp.example.com/auth/callback'
            ],
            'is_active' => true,
        ]);
    }
}
