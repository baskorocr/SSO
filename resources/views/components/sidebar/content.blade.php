<x-perfect-scrollbar
    as="nav"
    aria-label="main"
    class="flex flex-col flex-1 gap-4 px-3"
>
    <x-sidebar.link
        title="Dashboard"
        href="{{ route('dashboard') }}"
        :isActive="request()->routeIs('dashboard')"
    >
        <x-slot name="icon">
            <x-icons.dashboard class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
        </x-slot>
    </x-sidebar.link>

    @can('manage roles')
    <x-sidebar.dropdown
        title="Management"
        :active="request()->routeIs('role-permissions.*', 'user-management.*', 'permissions.*', 'sso.admin.*')"
    >
        <x-slot name="icon">
            <x-icons.cog class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
        </x-slot>

        <x-sidebar.sublink
            title="Role & Permissions"
            href="{{ route('role-permissions.index') }}"
            :active="request()->routeIs('role-permissions.*')"
        />
        
        @can('manage users')
        <x-sidebar.sublink
            title="User Management"
            href="{{ route('user-management.index') }}"
            :active="request()->routeIs('user-management.*')"
        />
        @endcan

        <x-sidebar.sublink
            title="Permission Management"
            href="{{ route('permissions.index') }}"
            :active="request()->routeIs('permissions.*')"
        />

        <x-sidebar.sublink
            title="SSO Clients"
            href="{{ route('sso.admin.index') }}"
            :active="request()->routeIs('sso.admin.*')"
        />
    </x-sidebar.dropdown>
    @endcan

   
</x-perfect-scrollbar>
