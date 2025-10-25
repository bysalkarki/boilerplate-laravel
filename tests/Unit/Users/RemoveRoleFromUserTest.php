<?php

declare(strict_types=1);

use App\Actions\Users\RemoveRoleFromUser;
use App\Dtos\Users\RemoveRoleFromUserDto;
use App\Models\User;
use Spatie\Permission\Models\Role;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can remove a role from a user', function () {
    $role = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole($role);

    expect($user->hasRole('Admin'))->toBeTrue();

    $dto = new RemoveRoleFromUserDto($user, $role);

    $action = new RemoveRoleFromUser();
    $action->execute($dto);

    expect($user->fresh()->hasRole('Admin'))->toBeFalse();
});
