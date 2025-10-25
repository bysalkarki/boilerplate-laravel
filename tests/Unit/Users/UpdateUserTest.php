<?php

declare(strict_types=1);

use App\Actions\Users\UpdateUser;
use App\Dtos\Users\UpdateUserDto;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\assertDatabaseHas;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can update a user\'s name and email', function () {
    $user = User::factory()->create();
    $dto = new UpdateUserDto(
        name: 'Bishal Karki',
        email: 'bishalkarki@gmail.com',
    );

    $action = new UpdateUser();
    $action->execute($user, $dto);

    assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Bishal Karki',
        'email' => 'bishalkarki@gmail.com',
    ]);
});

it('can update a user\'s password', function () {
    $user = User::factory()->create();
    $dto = new UpdateUserDto(
        name: $user->name,
        email: $user->email,
        password: 'new-password'
    );

    $action = new UpdateUser();
    $action->execute($user, $dto);

    $user->refresh();

    expect(Hash::check('new-password', $user->password))->toBeTrue();
});

it('does not update a user\'s password if it is not provided', function () {
    $user = User::factory()->create([
        'password' => bcrypt('old-password'),
    ]);
    $oldPassword = $user->password;

    $dto = new UpdateUserDto(
        name: 'Bishal Karki',
        email: 'bishalkarki@gmail.com',
    );

    $action = new UpdateUser();
    $action->execute($user, $dto);

    $user->refresh();

    expect($user->password)->toBe($oldPassword);
});
