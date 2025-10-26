<?php

declare(strict_types=1);

use App\Actions\Roles\GetRoleById;
use Spatie\Permission\Models\Role;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can get a role by id', function () {
    $role = Role::create(['name' => 'Admin', 'guard_name' => 'web']);

    $action = new GetRoleById();
    $foundRole = $action->execute($role->id);

    expect($foundRole)->toBeInstanceOf(Role::class)
        ->and($foundRole->id)->toBe($role->id);
});

it('returns null if a role is not found', function () {
    $action = new GetRoleById();
    $foundRole = $action->execute(123);

    expect($foundRole)->toBeNull();
});

it('can get all roles', function () {
    $perPage = 5;
    App\Models\Role::factory()->count(20)->create();

    $action = new App\Actions\Roles\GetAllRoles();
    $users = $action->execute(perPage: $perPage);

    expect($users)->toBeInstanceOf(Illuminate\Pagination\LengthAwarePaginator::class)
        ->and($users->count())->toBe($perPage)
        ->and($users->total())->toBe(20);

    App\Models\Role::create(['name' => 'Sharyananananana pathana', 'guard_name' => 'web']);
    $users = $action->execute(search: 'Sharyananananana pathana');
    expect($users)->toBeInstanceOf(Illuminate\Pagination\LengthAwarePaginator::class)
        ->and($users->count())->toBe(1);
});
