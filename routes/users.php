<?php

declare(strict_types=1);

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('users', UserController::class);

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index')->middleware('permission:read-user');
        Route::get('/create', [UserController::class, 'create'])->name('create')->middleware('permission:create-user');
        Route::post('/', [UserController::class, 'store'])->name('store')->middleware('permission:create-user');
        Route::get('{user}/edit', [UserController::class, 'edit'])->name('edit')->middleware('permission:update-user');
        Route::put('{user}', [UserController::class, 'update'])->name('update')->middleware('permission:update-user');
        Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy')->middleware('permission:delete-user');
    });
});
