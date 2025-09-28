<?php

namespace App\Modules\Client\Application\Command;

final class CreateClient
{
    public function __construct(
        public string $id,
        public string $name,
        public int $age,
        public string $region,
        public string $pin,
        public string $email,
        public string $phone,
        public string $income,
        public int $score,
    ) {}
}
