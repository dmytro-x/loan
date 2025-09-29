<?php

namespace App\Modules\Credit\Domain\Rules;

use App\Modules\Client\Domain\Entity\Client;
use App\Modules\Credit\Application\DTO\CreditApplicationData;
use App\Modules\Credit\Application\DTO\CreditDecision;

final class ScoreRule implements CreditRule
{
    public static string $rejectionMessage = 'Score does not meet the eligibility criteria';

    public function apply(Client $client, CreditApplicationData $data, CreditDecision $decision): void
    {
        if ($client->score <= 500) {
            $decision->reject(self::$rejectionMessage);
        }
    }
}
