<?php

declare(strict_types=1);

namespace App\Dtos\Users;

final class UpdateUserDto
{
    public function __construct(
        public string $name,
        public string $email,
        public ?int $roleId = null,
    ) {}
}
