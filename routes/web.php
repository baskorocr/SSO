<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SsoIdpController;
use App\Http\Controllers\SsoAdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('test', function () {
        return "test";
    })->middleware('can:test');
    // Role & Permission management routes
    Route::prefix('role-permissions')->name('role-permissions.')->middleware('can:manage roles')->group(function () {
        Route::get('/', [RolePermissionController::class, 'index'])->name('index');
        Route::get('/create-role', [RolePermissionController::class, 'showCreateRole'])->name('create-role');
        Route::post('/create-role', [RolePermissionController::class, 'createRole'])->name('store-role');
        Route::post('/assign-role', [RolePermissionController::class, 'assignRole'])->name('assign-role');
        Route::post('/revoke-role', [RolePermissionController::class, 'revokeRole'])->name('revoke-role');
        Route::post('/assign-permission', [RolePermissionController::class, 'assignPermission'])->name('assign-permission');
        Route::post('/revoke-permission', [RolePermissionController::class, 'revokePermission'])->name('revoke-permission');
        Route::post('/update-role-permissions', [RolePermissionController::class, 'updateRolePermissions'])->name('update-role-permissions');
        Route::delete('/{role}', [RolePermissionController::class, 'deleteRole'])->name('delete-role');
    });

    // User management routes
    Route::prefix('user-management')->name('user-management.')->middleware('can:manage users')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::post('/update-role', [UserManagementController::class, 'updateRole'])->name('update-role');
        Route::post('/store', [UserManagementController::class, 'store'])->name('store');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
    });

    // Permission management routes
    Route::prefix('permissions')->name('permissions.')->middleware('can:manage roles')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::post('/', [PermissionController::class, 'store'])->name('store');
        Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
    });
});

// Button routes with permission
Route::get('/buttons/text', function () {
    return view('buttons-showcase.text');
})->middleware(['auth', 'can:access buttons'])->name('buttons.text');

Route::get('/buttons/icon', function () {
    return view('buttons-showcase.icon');
})->middleware(['auth', 'can:access buttons'])->name('buttons.icon');

Route::get('/buttons/text-icon', function () {
    return view('buttons-showcase.text-icon');
})->middleware(['auth', 'can:access buttons'])->name('buttons.text-icon');

// SSO Identity Provider routes
Route::prefix('sso')->name('sso.')->group(function () {
    Route::get('/test', function() { return 'SSO routes working'; });
    Route::get('/authorize', [SsoIdpController::class, 'authorize'])->name('authorize');
    Route::post('/token', [SsoIdpController::class, 'token'])->name('token');
    Route::get('/userinfo', [SsoIdpController::class, 'userinfo'])->name('userinfo');
    Route::post('/logout', [SsoIdpController::class, 'logout'])->name('logout');
    Route::get('/quick-login/{client}', [SsoIdpController::class, 'quickLogin'])->middleware('auth')->name('quick-login');
    
    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:manage roles'])->group(function () {
        Route::get('/', [SsoAdminController::class, 'index'])->name('index');
        Route::get('/create', [SsoAdminController::class, 'create'])->name('create');
        Route::post('/', [SsoAdminController::class, 'store'])->name('store');
        Route::get('/{client}', [SsoAdminController::class, 'show'])->name('show');
        Route::get('/{client}/edit', [SsoAdminController::class, 'edit'])->name('edit');
        Route::put('/{client}', [SsoAdminController::class, 'update'])->name('update');
        Route::post('/{client}/regenerate-secret', [SsoAdminController::class, 'regenerateSecret'])->name('regenerate-secret');
        Route::post('/{client}/revoke-tokens', [SsoAdminController::class, 'revokeTokens'])->name('revoke-tokens');
        Route::post('/{client}/auto-sync', [SsoIdpController::class, 'autoSyncUsers'])->name('auto-sync');
        Route::post('/{client}/assign-user', [SsoAdminController::class, 'assignUser'])->name('assign-user');
        Route::delete('/{client}/users/{user}', [SsoAdminController::class, 'removeUser'])->name('remove-user');
        Route::delete('/{client}', [SsoAdminController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__ . '/auth.php';
