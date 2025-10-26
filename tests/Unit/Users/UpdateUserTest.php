<?php

declare(strict_types=1);

use App\Actions\Users\UpdateUser;
use App\Dtos\Users\UpdateUserDto;
use App\Models\User;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\assertDatabaseHas;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->role = Role::create(['name' => 'test-role']);
});

it('can update a user\'s name and email', function () {
    $user = User::factory()->create();
    $dto = new UpdateUserDto(
        name: 'Bishal Karki',
        email: 'bishalkarki@gmail.com',
        roleId: $this->role->id,
    );

    $action = new UpdateUser();
    $action->execute($user, $dto);

    assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Bishal Karki',
        'email' => 'bishalkarki@gmail.com',
    ]);
});

it('can update a user\'s role', function () {
    $user = User::factory()->create();
    $oldRole = Role::create(['name' => 'old-role']);
    $user->assignRole($oldRole);

    $newRole = Role::create(['name' => 'new-role']);

    $dto = new UpdateUserDto(
        name: $user->name,
        email: $user->email,
        roleId: $newRole->id,
    );

    $action = new UpdateUser();
    $action->execute($user, $dto);

    $user->refresh();
    expect($user->hasRole('new-role'))->toBeTrue();
    expect($user->hasRole('old-role'))->toBeFalse();
});
