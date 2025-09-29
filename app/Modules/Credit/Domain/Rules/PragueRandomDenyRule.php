<?php

namespace App\Modules\Credit\Domain\Rules;

use App\Modules\Client\Domain\Entity\Client;
use App\Modules\Credit\Application\DTO\CreditApplicationData;
use App\Modules\Credit\Application\DTO\CreditDecision;
use App\Modules\Credit\Domain\Region\Regions;

final class PragueRandomDenyRule implements CreditRule
{
    public static int $denyRate = 2;
    public static string $rejectionMessage = 'Some parameters does not meet the eligibility criteria';

    public function apply(Client $client, CreditApplicationData $data, CreditDecision $decision): void
    {
        if ($client->region != Regions::PRAGUE) {
            return;
        }

        $randValue = rand(1, 100);

        if ($randValue <= self::$denyRate) {
            $decision->reject(self::$rejectionMessage);
        }
    }
}
