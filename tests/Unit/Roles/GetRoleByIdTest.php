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
