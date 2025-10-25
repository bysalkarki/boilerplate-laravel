<?php

declare(strict_types=1);

namespace App\Dtos\Users;

use SensitiveParameter;

final class UpdateUserDto
{
    public function __construct(
        public string $name,
        public string $email,
        #[SensitiveParameter]
        public ?string $password = null,
    ) {}
}
