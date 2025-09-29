<?php

namespace App\Modules\Credit\Domain\Rules;

use App\Modules\Client\Domain\Entity\Client;
use App\Modules\Credit\Application\DTO\CreditApplicationData;
use App\Modules\Credit\Application\DTO\CreditDecision;

final class IncomeRule implements CreditRule
{
    public static string $rejectionMessage = 'Not enough income';

    public function apply(Client $client, CreditApplicationData $data, CreditDecision $decision): void
    {
        if ($client->income < 1000) {
            $decision->reject(self::$rejectionMessage);
        }
    }
}
