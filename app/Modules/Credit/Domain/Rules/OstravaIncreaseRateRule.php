<?php

namespace App\Modules\Credit\Domain\Rules;

use App\Modules\Client\Domain\Entity\Client;
use App\Modules\Credit\Application\DTO\CreditApplicationData;
use App\Modules\Credit\Application\DTO\CreditDecision;
use App\Modules\Credit\Domain\Region\Regions;

final class OstravaIncreaseRateRule implements CreditRule
{
    public static $increaseRateValue = 5;

    public function apply(Client $client, CreditApplicationData $data, CreditDecision $decision): void
    {
        if ($client->region == Regions::OSTRAVA) {
            $decision->increaseInterestRate(self::$increaseRateValue);
        }
    }
}
