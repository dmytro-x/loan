<?php

namespace App\Modules\Credit\Domain\Rules;

use App\Modules\Client\Domain\Entity\Client;
use App\Modules\Credit\Application\DTO\CreditApplicationData;
use App\Modules\Credit\Application\DTO\CreditDecision;

interface CreditRule
{
    public function apply(
        Client $client,
        CreditApplicationData $data,
        CreditDecision $decision
    ): void;
}
