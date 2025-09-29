<?php

namespace App\Modules\Credit\Domain\Entity;

final class Credit
{
    public function __construct(
        public string $id,
        public string $clientId,
        public string $name,
        public string $amount,
        public string $rate,
        public \DateTimeImmutable $startDate,
        public \DateTimeImmutable $endDate,
    ) {}
}
