<?php

declare(strict_types=1);

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it can create users', function () {
    $data = new App\Dtos\Users\CreateUserDto(
        name: 'John Doe',
        email: 'johnDoe@gmail.com',
        password: 'password',
    );

    $user = (new App\Actions\Users\CreateUser())->execute($data);

    $this->assertDatabaseHas('users', $user->only('id', 'name', 'email', 'password'));
});
