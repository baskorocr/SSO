<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <h3 class="text-lg font-medium mb-4">Selamat datang, {{ auth()->user()->name }}!</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h4 class="font-semibold mb-2">Informasi User</h4>
                <p><strong>NPK:</strong> {{ auth()->user()->npk }}</p>
                <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
            </div>
            
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h4 class="font-semibold mb-2">Roles & Permissions</h4>
                <p><strong>Roles:</strong> 
                    @forelse(auth()->user()->roles as $role)
                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1">{{ $role->name }}</span>
                    @empty
                        <span class="text-gray-500">No roles assigned</span>
                    @endforelse
                </p>
                <p class="mt-2"><strong>Permissions:</strong>
                    @forelse(auth()->user()->getAllPermissions() as $permission)
                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-1 mt-1">{{ $permission->name }}</span>
                    @empty
                        <span class="text-gray-500">No permissions assigned</span>
                    @endforelse
                </p>
            </div>
        </div>

        <!-- SSO Applications -->
        <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <h4 class="font-semibold mb-3">SSO Applications</h4>
            @php
                $ssoClients = auth()->user()->ssoClients()->where('is_active', true)->get();
            @endphp
            
            @if($ssoClients->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($ssoClients as $client)
                        <div class="bg-white dark:bg-gray-600 p-4 rounded border hover:shadow-md transition-shadow">
                            <h5 class="font-medium text-gray-900 dark:text-white">{{ $client->name }}</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                Client ID: <code class="text-xs">{{ Str::limit($client->client_id, 20) }}</code>
                            </p>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                    ✓ Authorized
                                </span>
                                @if(count($client->redirect_uris) > 0)
                                    <a href="{{ route('sso.quick-login', $client) }}" 
                                       class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-1 px-3 rounded transition-colors"
                                       target="_blank">
                                        Login
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">You don't have access to any SSO applications yet.</p>
            @endif
        </div>
    </div>
</x-app-layout>
