<?php

declare(strict_types=1);

use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::resource('roles', RoleController::class)->middleware(['auth', 'verified']);
