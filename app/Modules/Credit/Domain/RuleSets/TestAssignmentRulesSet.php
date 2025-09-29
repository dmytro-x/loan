<?php

namespace App\Modules\Credit\Domain\RuleSets;

use App\Modules\Client\Domain\Entity\Client;
use App\Modules\Credit\Application\DTO\CreditApplicationData;
use App\Modules\Credit\Application\DTO\CreditDecision;
use App\Modules\Credit\Domain\Rules\AgeRule;
use App\Modules\Credit\Domain\Rules\IncomeRule;
use App\Modules\Credit\Domain\Rules\CreditRules;
use App\Modules\Credit\Domain\Rules\OstravaIncreaseRateRule;
use App\Modules\Credit\Domain\Rules\PragueRandomDenyRule;
use App\Modules\Credit\Domain\Rules\RegionRule;
use App\Modules\Credit\Domain\Rules\ScoreRule;

class TestAssignmentRulesSet implements RulesSet
{
    private CreditRules $rules;

    public function __construct()
    {
        $this->rules = new CreditRules([
            new ScoreRule(),
            new IncomeRule(),
            new AgeRule(),
            new RegionRule(),
            new PragueRandomDenyRule(),
            new OstravaIncreaseRateRule(),
            // You can add as many rules as you want
        ]);
    }

    public function validate(Client $client, CreditApplicationData $application): CreditDecision
    {
        return $this->rules->validate($client, $application);
    }
}
