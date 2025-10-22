<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit SSO Client: ') . $client->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('sso.admin.update', $client) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Client Name
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $client->name) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="redirect_uris" class="block text-sm font-medium text-gray-700 mb-2">
                                Redirect URIs (one per line)
                            </label>
                            <textarea id="redirect_uris" 
                                      name="redirect_uris" 
                                      rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      required>{{ old('redirect_uris', implode("\n", $client->redirect_uris)) }}</textarea>
                            @error('redirect_uris')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-sm mt-1">Enter each redirect URI on a new line</p>
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', $client->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Assign Users
                            </label>
                            <input type="text" 
                                   id="userSearch" 
                                   placeholder="Search users..." 
                                   class="w-full px-3 py-2 mb-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div class="max-h-40 overflow-y-auto border border-gray-300 rounded-md p-3" id="userList">
                                @foreach(App\Models\User::all() as $user)
                                    <label class="flex items-center mb-2 user-item" data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}">
                                        <input type="checkbox" 
                                               name="user_ids[]" 
                                               value="{{ $user->id }}"
                                               {{ in_array($user->id, old('user_ids', $client->users->pluck('id')->toArray())) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm">{{ $user->name }} ({{ $user->email }})</span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-gray-500 text-sm mt-1">Select users who can access this SSO client</p>
                        </div>

                        <script>
                            document.getElementById('userSearch').addEventListener('input', function() {
                                const searchTerm = this.value.toLowerCase();
                                const userItems = document.querySelectorAll('.user-item');
                                
                                userItems.forEach(item => {
                                    const name = item.dataset.name;
                                    const email = item.dataset.email;
                                    
                                    if (name.includes(searchTerm) || email.includes(searchTerm)) {
                                        item.style.display = 'flex';
                                    } else {
                                        item.style.display = 'none';
                                    }
                                });
                            });
                        </script>

                        <div class="flex justify-between">
                            <a href="{{ route('sso.admin.show', $client) }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Client
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
