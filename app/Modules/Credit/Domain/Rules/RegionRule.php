<?php

namespace App\Modules\Credit\Domain\Rules;

use App\Modules\Client\Domain\Entity\Client;
use App\Modules\Credit\Application\DTO\CreditApplicationData;
use App\Modules\Credit\Application\DTO\CreditDecision;
use App\Modules\Credit\Domain\Region\Regions;

final class RegionRule implements CreditRule
{
    public static string $rejectionMessage = 'Region does not meet the eligibility criteria';

    public static $allowedRegions = [
        Regions::PRAGUE => 'Прага',
        Regions::BRNO => 'Брно',
        Regions::OSTRAVA => 'Острава',
    ];

    public function apply(Client $client, CreditApplicationData $data, CreditDecision $decision): void
    {
        if (!array_key_exists($client->region, self::$allowedRegions)){
            $decision->reject(self::$rejectionMessage);
        }
    }
}
