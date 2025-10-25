<?php

declare(strict_types=1);

namespace App\Dtos\Roles;

final class CreateRoleDto
{
    public function __construct(
        public string $name,
    ) {}
}
