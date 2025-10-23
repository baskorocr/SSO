<?php

namespace App\Http\Controllers;

use App\Models\SsoClient;
use App\Models\SsoAuthorizationCode;
use App\Models\SsoAccessToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SsoIdpController extends Controller
{
    public function authorize(Request $request)
    {
        $request->validate([
            'client_id' => 'required|string',
            'redirect_uri' => 'required|url',
            'response_type' => 'required|in:code',
            'state' => 'nullable|string',
        ]);

        // Find and validate client
        $client = SsoClient::where('client_id', $request->client_id)
                          ->where('is_active', true)
                          ->first();

        if (!$client) {
            return response()->json(['error' => 'invalid_client'], 400);
        }

        // Validate redirect URI
        if (!$client->isRedirectUriValid($request->redirect_uri)) {
            return response()->json(['error' => 'invalid_redirect_uri'], 400);
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            // Store SSO parameters in session and redirect to login
            session(['sso_params' => $request->all()]);
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user is assigned to this client
        if (!$user->ssoClients()->where('sso_client_id', $client->id)->exists()) {
            return response()->json(['error' => 'access_denied', 'message' => 'User not authorized for this application'], 403);
        }

        // Generate authorization code
        $code = Str::random(32);
        
        SsoAuthorizationCode::create([
            'code' => $code,
            'user_id' => $user->id,
            'client_id' => $client->id,
            'redirect_uri' => $request->redirect_uri,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Build callback URL
        $params = [
            'code' => $code,
        ];

        if ($request->state) {
            $params['state'] = $request->state;
        }

        $callbackUrl = $request->redirect_uri . '?' . http_build_query($params);

        return redirect($callbackUrl);
    }

    public function token(Request $request)
    {
        $request->validate([
            'grant_type' => 'required|in:authorization_code',
            'code' => 'required|string',
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'redirect_uri' => 'required|url',
        ]);

        // Validate client credentials
        $client = SsoClient::where('client_id', $request->client_id)
                          ->where('client_secret', $request->client_secret)
                          ->where('is_active', true)
                          ->first();

        if (!$client) {
            return response()->json(['error' => 'invalid_client'], 401);
        }

        // Find and validate authorization code
        $authCode = SsoAuthorizationCode::where('code', $request->code)
                                       ->where('client_id', $client->id)
                                       ->where('redirect_uri', $request->redirect_uri)
                                       ->first();

        if (!$authCode || !$authCode->isValid()) {
            return response()->json(['error' => 'invalid_grant'], 400);
        }

        // Mark code as used
        $authCode->update(['used' => true]);

        // Generate access token
        $token = Str::random(64);
        
        SsoAccessToken::create([
            'token' => $token,
            'user_id' => $authCode->user_id,
            'client_id' => $client->id,
            'expires_at' => Carbon::now()->addHours(1),
        ]);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ]);
    }

    public function userinfo(Request $request)
    {
        $authHeader = $request->header('Authorization');
        
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'invalid_token'], 401);
        }

        $token = substr($authHeader, 7);

        // Find and validate access token
        $accessToken = SsoAccessToken::where('token', $token)
                                    ->with(['user', 'client'])
                                    ->first();

        if (!$accessToken || !$accessToken->isValid()) {
            return response()->json(['error' => 'invalid_token'], 401);
        }

        $user = $accessToken->user;

        return response()->json([
            'sub' => $user->npk ?: (string) $user->id, // Use NPK if available, fallback to ID
            'name' => $user->name,
            'email' => $user->email,
            'email_verified' => !is_null($user->email_verified_at),
            'npk' => $user->npk,
        ]);
    }

    public function autoSyncUsers(Request $request, SsoClient $client)
    {
        if (!$client->is_active) {
            return response()->json(['error' => 'Client is not active'], 400);
        }

        try {
            $httpClient = new \GuzzleHttp\Client();
            
            // Use first redirect URI as base URL for sync endpoint
            $redirectUri = $client->redirect_uris[0];
            $baseUrl = str_replace('/auth/callback', '', $redirectUri);
            $syncUrl = $baseUrl . '/api/sync-users';
            
            // Request to SSO client for user data
            $response = $httpClient->post($syncUrl, [
                'json' => [
                    'client_id' => $client->client_id,
                    'client_secret' => $client->client_secret,
                    'action' => 'get_users'
                ],
                'timeout' => 30,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);

            $userData = json_decode($response->getBody(), true);
          
            
            if (!isset($userData['users']) || !is_array($userData['users'])) {
                return response()->json(['error' => 'Invalid response format from SSO client'], 400);
            }

            $syncedCount = 0;
            $errors = [];

            foreach ($userData['users'] as $userInfo) {
                try {
                    if (!isset($userInfo['email']) || !isset($userInfo['name'])) {
                        $errors[] = "Missing required fields for user";
                        continue;
                    }



                    $user = User::updateOrCreate(
                        ['email' => $userInfo['email']],
                        [
                            'npk' => $userInfo['npk'] ?? null,
                            'name' => $userInfo['name'],
                            'password' => $userInfo['password'],
                        ]
                    );

                    // Assign default role if user doesn't have any
                    if (!$user->hasAnyRole()) {
                        $user->assignRole('user');
                    }
                    
                    // Link user to SSO client
                    $user->ssoClients()->syncWithoutDetaching([$client->id]);
                    
                    $syncedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to sync user {$userInfo['email']}: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'synced_count' => $syncedCount,
                'errors' => $errors,
                'message' => "Successfully synced {$syncedCount} users"
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to connect to SSO client: ' . $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->validate([
            'post_logout_redirect_uri' => 'nullable|url',
        ]);

        // Clean up SSO data for the current user
        if (Auth::check()) {
            $userId = Auth::id();
            
            // Delete all access tokens
            SsoAccessToken::where('user_id', $userId)->delete();
            
            // Delete all authorization codes
            SsoAuthorizationCode::where('user_id', $userId)->delete();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->post_logout_redirect_uri) {
            return redirect($request->post_logout_redirect_uri);
        }

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function quickLogin(SsoClient $client)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user is assigned to this client
        if (!$user->ssoClients()->where('sso_client_id', $client->id)->exists()) {
            abort(403, 'Access denied');
        }

        // Use the first redirect URI for quick login
        $redirectUri = $client->redirect_uris[0] ?? null;
        if (!$redirectUri) {
            abort(400, 'No redirect URI configured');
        }

        // Generate authorization code
        $code = Str::random(32);
        
        SsoAuthorizationCode::create([
            'code' => $code,
            'user_id' => $user->id,
            'client_id' => $client->id,
            'redirect_uri' => $redirectUri,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Redirect to client application with code
        $params = [
            'code' => $code,
            'state' => 'quick_login_' . time(),
        ];

        $callbackUrl = $redirectUri . '?' . http_build_query($params);

        return redirect($callbackUrl);
    }
}
