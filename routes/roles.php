<?php

declare(strict_types=1);

use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('roles', RoleController::class);

    Route::prefix('roles')->name('roles.')->group(function () {
        // New permission assignment routes
        Route::get('{role}/assign-permissions', [RoleController::class, 'assignPermissions'])->name('assign-permissions')->middleware('permission:update-role');
        Route::put('{role}/assign-permissions', [RoleController::class, 'storePermissions'])->name('store-permissions')->middleware('permission:update-role');

        Route::get('/', [RoleController::class, 'index'])->name('index')->middleware('permission:read-role');
        Route::get('/create', [RoleController::class, 'create'])->name('create')->middleware('permission:create-role');
        Route::post('/', [RoleController::class, 'store'])->name('store')->middleware('permission:create-role');
        Route::get('{role}/edit', [RoleController::class, 'edit'])->name('edit')->middleware('permission:update-role');
        Route::put('{role}', [RoleController::class, 'update'])->name('update')->middleware('permission:update-role');
        Route::delete('{role}', [RoleController::class, 'destroy'])->name('destroy')->middleware('permission:delete-role');

    });
});
