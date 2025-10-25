<?php

declare(strict_types=1);

use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('roles', RoleController::class)->middleware([
        'index' => 'permission:read-role',
        'show' => 'permission:read-role',
        'create' => 'permission:create-role',
        'store' => 'permission:create-role',
        'edit' => 'permission:update-role',
        'update' => 'permission:update-role',
        'destroy' => 'permission:delete-role',
    ]);
});
