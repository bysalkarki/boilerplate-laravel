<?php

declare(strict_types=1);

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('users', UserController::class)->middleware([
        'index' => 'permission:read-user',
        'show' => 'permission:read-user',
        'create' => 'permission:create-user',
        'store' => 'permission:create-user',
        'edit' => 'permission:update-user',
        'update' => 'permission:update-user',
        'destroy' => 'permission:delete-user',
    ]);
});
