<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        
        return view('role-permissions.index', compact('users', 'roles', 'permissions'));
    }

    public function showCreateRole()
    {
        return view('role-permissions.create-role');
    }

    public function assignRole(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->assignRole($request->role);
        
        return back()->with('success', 'Role assigned successfully');
    }

    public function revokeRole(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->removeRole($request->role);
        
        return back()->with('success', 'Role revoked successfully');
    }

    public function assignPermission(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->givePermissionTo($request->permission);
        
        return back()->with('success', 'Permission assigned successfully');
    }

    public function revokePermission(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->revokePermissionTo($request->permission);
        
        return back()->with('success', 'Permission revoked successfully');
    }

    public function updateRolePermissions(Request $request)
    {
        $role = Role::findOrFail($request->role_id);
        $permissions = $request->permissions ?? [];
        
        $role->syncPermissions($permissions);
        
        return back()->with('success', 'Role permissions updated successfully');
    }

    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name'
        ]);

        Role::create(['name' => $request->name]);
        
        return back()->with('success', 'Role created successfully');
    }

    public function deleteRole(Role $role)
    {
        // Check if role has users
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role that has assigned users');
        }

        $role->delete();
        
        return back()->with('success', 'Role deleted successfully');
    }
}
