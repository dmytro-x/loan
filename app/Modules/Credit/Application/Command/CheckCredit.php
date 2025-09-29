<?php

namespace App\Modules\Credit\Application\Command;

final class CheckCredit
{
    public function __construct(
        public string $clientId,
        public string $name,
        public string $amount,
        public string $rate,
        public string $startDate,
        public string $endDate,
    ) {}
}
