<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('SSO Client: ') . $client->name }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('sso.admin.edit', $client) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                <a href="{{ route('sso.admin.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Client Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Client Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $client->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $client->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $client->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Client ID</label>
                            <p class="mt-1 text-sm text-gray-900 font-mono bg-gray-100 p-2 rounded">{{ $client->client_id }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Client Secret</label>
                            <div class="mt-1 flex items-center space-x-2">
                                <p class="text-sm text-gray-900 font-mono bg-gray-100 p-2 rounded flex-1">{{ $client->client_secret }}</p>
                                <form action="{{ route('sso.admin.regenerate-secret', $client) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? This will invalidate all existing tokens.')">
                                    @csrf
                                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded text-xs">
                                        Regenerate
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Redirect URIs</label>
                            <div class="mt-1 space-y-1">
                                @foreach($client->redirect_uris as $uri)
                                    <p class="text-sm text-gray-900 font-mono bg-gray-100 p-2 rounded">{{ $uri }}</p>
                                @endforeach
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Created</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $client->created_at->format('M d, Y H:i:s') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $client->updated_at->format('M d, Y H:i:s') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex space-x-2">
                        <form action="{{ route('sso.admin.revoke-tokens', $client) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? This will revoke all active access tokens.')">
                            @csrf
                            <button type="submit" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                                Revoke All Tokens
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Assigned Users -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Assigned Users</h3>
                        <div class="flex space-x-2">
                            <!-- Auto Sync Button -->
                            <button onclick="autoSyncUsers()" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-1 px-3 rounded text-sm">
                                Auto Sync Users
                            </button>
                            
                            @if($availableUsers->count() > 0)
                                <form action="{{ route('sso.admin.assign-user', $client) }}" method="POST" class="flex items-center space-x-2">
                                    @csrf
                                    <select name="user_id" class="border border-gray-300 rounded px-3 py-1 text-sm">
                                        <option value="">Select User</option>
                                        @foreach($availableUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        Assign
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    
                    @if($client->users->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($client->users as $user)
                                <div class="border rounded p-3 flex justify-between items-center">
                                    <div>
                                        <p class="font-medium">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                        @if($user->npk)
                                            <p class="text-xs text-gray-400">NPK: {{ $user->npk }}</p>
                                        @endif
                                    </div>
                                    <form action="{{ route('sso.admin.remove-user', [$client, $user]) }}" method="POST" class="inline" onsubmit="return confirm('Remove this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Remove</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No users assigned to this client.</p>
                    @endif
                </div>
            </div>

            <!-- Integration Example -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Integration Example</h3>
                    
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <h4 class="font-medium mb-2">Authorization URL:</h4>
                        <code class="text-sm break-all">
                            {{ url('/sso/authorize') }}?client_id={{ $client->client_id }}&redirect_uri={{ urlencode($client->redirect_uris[0] ?? '') }}&response_type=code&state=YOUR_STATE
                        </code>
                        
                        <h4 class="font-medium mb-2 mt-4">Token Exchange (POST to {{ url('/sso/token') }}):</h4>
                        <pre class="text-sm"><code>{
    "grant_type": "authorization_code",
    "code": "AUTHORIZATION_CODE",
    "client_id": "{{ $client->client_id }}",
    "client_secret": "{{ $client->client_secret }}",
    "redirect_uri": "{{ $client->redirect_uris[0] ?? '' }}"
}</code></pre>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recent Authorization Codes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Recent Authorization Codes</h3>
                        
                        @if($client->authorizationCodes->count() > 0)
                            <div class="space-y-2">
                                @foreach($client->authorizationCodes as $code)
                                    <div class="border rounded p-3">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-sm font-mono">{{ Str::limit($code->code, 20) }}...</p>
                                                <p class="text-xs text-gray-500">User ID: {{ $code->user_id }}</p>
                                                <p class="text-xs text-gray-500">{{ $code->created_at->diffForHumans() }}</p>
                                            </div>
                                            <span class="px-2 py-1 text-xs rounded {{ $code->used ? 'bg-gray-100 text-gray-800' : ($code->isExpired() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800') }}">
                                                {{ $code->used ? 'Used' : ($code->isExpired() ? 'Expired' : 'Valid') }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No authorization codes yet.</p>
                        @endif
                    </div>
                </div>

                <!-- Recent Access Tokens -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Recent Access Tokens</h3>
                        
                        @if($client->accessTokens->count() > 0)
                            <div class="space-y-2">
                                @foreach($client->accessTokens as $token)
                                    <div class="border rounded p-3">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-sm font-mono">{{ Str::limit($token->token, 20) }}...</p>
                                                <p class="text-xs text-gray-500">User ID: {{ $token->user_id }}</p>
                                                <p class="text-xs text-gray-500">{{ $token->created_at->diffForHumans() }}</p>
                                            </div>
                                            <span class="px-2 py-1 text-xs rounded {{ $token->revoked ? 'bg-gray-100 text-gray-800' : ($token->isExpired() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800') }}">
                                                {{ $token->revoked ? 'Revoked' : ($token->isExpired() ? 'Expired' : 'Active') }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No access tokens yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function autoSyncUsers() {
            if (!confirm('This will sync users from the SSO client. Continue?')) {
                return;
            }

            const button = event.target;
            button.disabled = true;
            button.textContent = 'Syncing...';

            fetch('{{ route("sso.admin.auto-sync", $client) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Sync result:', data);
                    alert(`Success! ${data.message}`);
                    if (data.errors && data.errors.length > 0) {
                        console.log('Sync errors:', data.errors);
                    }
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while syncing users');
            })
            .finally(() => {
                button.disabled = false;
                button.textContent = 'Auto Sync Users';
            });
        }
    </script>
</x-app-layout>
