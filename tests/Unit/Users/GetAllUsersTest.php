<?php

declare(strict_types=1);

use App\Actions\Users\GetAllUsers;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can get all users', function () {
    $perPage = 5;
    User::factory()->count(20)->create();

    $action = new GetAllUsers();
    $users = $action->execute(perPage: $perPage);

    expect($users)->toBeInstanceOf(LengthAwarePaginator::class)
        ->and($users->count())->toBe($perPage)
        ->and($users->total())->toBe(20);
});
