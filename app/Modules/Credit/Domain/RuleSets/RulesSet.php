<?php

namespace App\Modules\Credit\Domain\RuleSets;

use App\Modules\Client\Domain\Entity\Client;
use App\Modules\Credit\Application\DTO\CreditApplicationData;
use App\Modules\Credit\Application\DTO\CreditDecision;

interface RulesSet
{
    public function validate(Client $client, CreditApplicationData $application): CreditDecision;
}
