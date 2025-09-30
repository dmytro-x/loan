<?php

namespace App\Modules\Credit\Application\Command;

final class IssueCredit
{
    public function __construct(
        public string $id,
        public string $clientId,
        public string $name,
        public string $amount,
        public string $rate,
        public string $startDate,
        public string $endDate,
    ) {}
}
