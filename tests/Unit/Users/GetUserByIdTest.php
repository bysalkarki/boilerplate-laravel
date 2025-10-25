<?php

declare(strict_types=1);

use App\Actions\Users\GetUserById;
use App\Models\User;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can get a user by id', function () {
    $user = User::factory()->create();

    $action = new GetUserById();
    $foundUser = $action->execute($user->id);

    expect($foundUser)->toBeInstanceOf(User::class)
        ->and($foundUser->id)->toBe($user->id);
});

it('returns null if a user is not found', function () {
    $action = new GetUserById();
    $foundUser = $action->execute(123);

    expect($foundUser)->toBeNull();
});
