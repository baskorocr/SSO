<?php

namespace App\Http\Controllers;

use App\Models\SsoClient;
use App\Models\SsoAuthorizationCode;
use App\Models\SsoAccessToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SsoAdminController extends Controller
{
    public function index()
    {
        $clients = SsoClient::withCount(['authorizationCodes', 'accessTokens', 'users'])->get();
        
        return view('sso.admin.index', compact('clients'));
    }

    public function create()
    {
        return view('sso.admin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'redirect_uris' => 'required|string',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $redirectUris = array_filter(array_map('trim', explode("\n", $request->redirect_uris)));

        $client = SsoClient::create([
            'name' => $request->name,
            'client_id' => Str::uuid(),
            'client_secret' => Str::random(64),
            'redirect_uris' => $redirectUris,
            'is_active' => true,
        ]);

        // Assign users if provided
        if ($request->user_ids) {
            $client->users()->attach($request->user_ids);
        }

        return redirect()->route('sso.admin.show', $client)
                        ->with('success', 'SSO Client created successfully!');
    }

    public function show(SsoClient $client)
    {
        $client->load(['authorizationCodes' => function($query) {
            $query->latest()->limit(10);
        }, 'accessTokens' => function($query) {
            $query->latest()->limit(10);
        }, 'users']);

        $availableUsers = User::whereNotIn('id', $client->users->pluck('id'))->get();

        return view('sso.admin.show', compact('client', 'availableUsers'));
    }

    public function edit(SsoClient $client)
    {
        $client->load('users');
        return view('sso.admin.edit', compact('client'));
    }

    public function update(Request $request, SsoClient $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'redirect_uris' => 'required|string',
            'is_active' => 'boolean',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $redirectUris = array_filter(array_map('trim', explode("\n", $request->redirect_uris)));

        $client->update([
            'name' => $request->name,
            'redirect_uris' => $redirectUris,
            'is_active' => $request->boolean('is_active'),
        ]);

        // Sync user assignments
        $client->users()->sync($request->user_ids ?? []);

        return redirect()->route('sso.admin.show', $client)
                        ->with('success', 'SSO Client updated successfully!');
    }

    public function regenerateSecret(SsoClient $client)
    {
        $client->update([
            'client_secret' => Str::random(64),
        ]);

        return redirect()->route('sso.admin.show', $client)
                        ->with('success', 'Client secret regenerated successfully!');
    }

    public function revokeTokens(SsoClient $client)
    {
        SsoAccessToken::where('client_id', $client->id)
                     ->where('revoked', false)
                     ->update(['revoked' => true]);

        return redirect()->route('sso.admin.show', $client)
                        ->with('success', 'All access tokens revoked successfully!');
    }

    public function destroy(SsoClient $client)
    {
        $client->delete();

        return redirect()->route('sso.admin.index')
                        ->with('success', 'SSO Client deleted successfully!');
    }

    public function assignUser(Request $request, SsoClient $client)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $client->users()->attach($request->user_id);

        return redirect()->route('sso.admin.show', $client)
                        ->with('success', 'User assigned successfully!');
    }

    public function removeUser(SsoClient $client, User $user)
    {
        $client->users()->detach($user->id);

        return redirect()->route('sso.admin.show', $client)
                        ->with('success', 'User removed successfully!');
    }
}
