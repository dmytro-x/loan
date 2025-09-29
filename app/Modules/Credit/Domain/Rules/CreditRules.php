<?php

namespace App\Modules\Credit\Domain\Rules;

use App\Modules\Client\Domain\Entity\Client;
use App\Modules\Credit\Application\DTO\CreditApplicationData;
use App\Modules\Credit\Application\DTO\CreditDecision;

final class CreditRules
{
    /** @var CreditRule[] */
    private $rules;

    public function __construct(iterable $rules)
    {
        $this->rules = $rules;
    }

    public function validate(Client $client, CreditApplicationData $data): CreditDecision
    {
        $decision = new CreditDecision();
        $decision->interestRate = $data->rate;

        foreach ($this->rules as $rule) {
            $rule->apply($client, $data, $decision);
        }

        return $decision;
    }
}
