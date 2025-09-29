<?php

namespace App\Modules\Credit\Domain\Rules;

use App\Modules\Client\Domain\Entity\Client;
use App\Modules\Credit\Application\DTO\CreditApplicationData;
use App\Modules\Credit\Application\DTO\CreditDecision;

final class AgeRule implements CreditRule
{
    public static string $rejectionMessage = 'Age does not meet the eligibility criteria';

    public function apply(Client $client, CreditApplicationData $data, CreditDecision $decision): void
    {
        if ($client->age < 18 || $client->age > 60) {
            $decision->reject(self::$rejectionMessage);
        }
    }
}
