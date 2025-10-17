<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Permission Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Permission Management</h3>
                        <button onclick="showAddPermissionModal()" class="bg-green-600 text-white px-4 py-2 rounded text-sm">
                            Add New Permission
                        </button>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Permission Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned to Roles</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($permissions as $permission)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $permission->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $permission->roles->count() }} roles</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button onclick="showEditPermissionModal({{ $permission->id }}, '{{ $permission->name }}')" 
                                                class="text-blue-600 hover:text-blue-900">Edit</button>
                                            <form method="POST" action="{{ route('permissions.destroy', $permission) }}" class="inline" 
                                                onsubmit="return confirm('Are you sure you want to delete this permission?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Permission Modal -->
    <div id="addPermissionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Add New Permission</h3>
                    <button onclick="closeAddPermissionModal()" class="text-gray-400 hover:text-gray-600">×</button>
                </div>
                
                <form method="POST" action="{{ route('permissions.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Permission Name</label>
                        <input type="text" name="name" required class="w-full border-gray-300 rounded-md" placeholder="e.g. view reports">
                    </div>
                    
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeAddPermissionModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Create Permission</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Permission Modal -->
    <div id="editPermissionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Edit Permission</h3>
                    <button onclick="closeEditPermissionModal()" class="text-gray-400 hover:text-gray-600">×</button>
                </div>
                
                <form id="editPermissionForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Permission Name</label>
                        <input type="text" id="editPermissionName" name="name" required class="w-full border-gray-300 rounded-md">
                    </div>
                    
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeEditPermissionModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update Permission</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showAddPermissionModal() {
            document.getElementById('addPermissionModal').classList.remove('hidden');
        }
        
        function closeAddPermissionModal() {
            document.getElementById('addPermissionModal').classList.add('hidden');
        }
        
        function showEditPermissionModal(id, name) {
            document.getElementById('editPermissionForm').action = `/permissions/${id}`;
            document.getElementById('editPermissionName').value = name;
            document.getElementById('editPermissionModal').classList.remove('hidden');
        }
        
        function closeEditPermissionModal() {
            document.getElementById('editPermissionModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
