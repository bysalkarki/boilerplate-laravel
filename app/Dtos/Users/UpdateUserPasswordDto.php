<?php

declare(strict_types=1);

namespace App\Dtos\Users;

use SensitiveParameter;

final class UpdateUserPasswordDto
{
    public function __construct(
        #[SensitiveParameter] public string $password,
    ) {}
}
