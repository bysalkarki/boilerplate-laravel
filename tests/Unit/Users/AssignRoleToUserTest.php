<?php

declare(strict_types=1);

use App\Actions\Users\AssignRoleToUser;
use App\Dtos\Users\AssignRoleToUserDto;
use App\Models\User;
use Spatie\Permission\Models\Role;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can assign a role to a user', function () {
    $user = User::factory()->create();
    $role = Role::create(['name' => 'Admin', 'guard_name' => 'web']);

    $dto = new AssignRoleToUserDto($user, $role);

    $action = new AssignRoleToUser();
    $action->execute($dto);

    expect($user->hasRole('Admin'))->toBeTrue();
});
