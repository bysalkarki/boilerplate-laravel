<?php

declare(strict_types=1);

use App\Actions\Users\DeleteUser;
use App\Dtos\Users\DeleteUserDto;
use App\Models\User;

use function Pest\Laravel\assertDatabaseMissing;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can delete a user', function () {
    $user = User::factory()->create();

    $action = new DeleteUser();
    $data = new DeleteUserDto($user);
    $action->execute($data);

    assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});
