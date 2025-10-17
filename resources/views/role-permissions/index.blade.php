<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Role Management') }}
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

            <!-- Roles Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Roles Overview</h3>
                        <button onclick="showAddRoleModal()" class="bg-green-600 text-white px-4 py-2 rounded text-sm">
                            Add New Role
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($roles as $role)
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-semibold text-lg">{{ $role->name }}</h4>
                                <span class="text-sm text-gray-500">{{ $role->users->count() }} users</span>
                            </div>
                            <div class="mb-3">
                                <span class="text-sm text-gray-600">{{ $role->permissions->count() }} permissions</span>
                            </div>
                            <div class="mb-3">
                                @foreach($role->permissions->take(3) as $permission)
                                    <div class="text-xs text-gray-700">{{ $permission->name }}</div>
                                @endforeach
                                @if($role->permissions->count() > 3)
                                    <div class="text-xs text-blue-600">+{{ $role->permissions->count() - 3 }} more</div>
                                @endif
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="showPermissions('{{ $role->name }}')" class="text-xs bg-blue-600 text-white px-3 py-1 rounded">
                                    Edit Permissions
                                </button>
                                <form method="POST" action="{{ route('role-permissions.delete-role', $role) }}" class="inline" 
                                    onsubmit="return confirm('Are you sure you want to delete this role?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs bg-red-600 text-white px-3 py-1 rounded">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Role Modal -->
    <div id="addRoleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Add New Role</h3>
                    <button onclick="closeAddRoleModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form method="POST" action="{{ route('role-permissions.store-role') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role Name</label>
                        <input type="text" name="name" required class="w-full border-gray-300 rounded-md" placeholder="Enter role name">
                    </div>
                    
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeAddRoleModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Create Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Permission Modal -->
    <div id="permissionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-lg p-6 max-w-4xl w-full mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium" id="modalTitle">Edit Role Permissions</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form id="permissionForm" method="POST" action="{{ route('role-permissions.update-role-permissions') }}">
                    @csrf
                    <input type="hidden" id="roleId" name="role_id" value="">
                    
                    <div class="mb-4">
                        <h4 class="font-medium mb-2">Select Permissions:</h4>
                        <div id="permissionsList" class="grid grid-cols-2 md:grid-cols-3 gap-2 max-h-96 overflow-y-auto border p-4 rounded">
                            @foreach($permissions as $permission)
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="permission-checkbox">
                                <span class="text-sm">{{ $permission->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update Permissions</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showAddRoleModal() {
            document.getElementById('addRoleModal').classList.remove('hidden');
        }
        
        function closeAddRoleModal() {
            document.getElementById('addRoleModal').classList.add('hidden');
        }
        
        function showPermissions(roleName) {
            const modal = document.getElementById('permissionModal');
            const title = document.getElementById('modalTitle');
            const roleIdInput = document.getElementById('roleId');
            
            title.textContent = 'Edit ' + roleName + ' Permissions';
            
            // Find role and set role ID
            const roles = @json($roles);
            const role = roles.find(r => r.name === roleName);
            roleIdInput.value = role.id;
            
            // Clear all checkboxes first
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Check permissions that role has
            if (role && role.permissions) {
                role.permissions.forEach(permission => {
                    const checkbox = document.querySelector(`input[value="${permission.name}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            }
            
            modal.classList.remove('hidden');
        }
        
        function closeModal() {
            document.getElementById('permissionModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
