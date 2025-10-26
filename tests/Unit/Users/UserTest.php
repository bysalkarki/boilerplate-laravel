<?php

declare(strict_types=1);

use App\Actions\Users\CreateUser;
use App\Dtos\Users\CreateUserDto;
use Spatie\Permission\Models\Role;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->role = Role::create(['name' => 'test-role']);
});

test('it can create users', function () {
    $data = new CreateUserDto(
        name: 'John Doe',
        email: 'johnDoe@gmail.com',
        password: 'password',
        roleId: $this->role->id,
    );

    $user = (new CreateUser())->execute($data);

    $this->assertDatabaseHas('users', $user->only('id', 'name', 'email'));
    $this->assertTrue($user->hasRole('test-role'));
});
