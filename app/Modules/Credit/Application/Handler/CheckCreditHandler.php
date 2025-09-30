<?php

namespace App\Modules\Credit\Application\Handler;

use App\Modules\Credit\Application\Command\CheckCredit;
use App\Modules\Credit\Application\DTO\CreditApplicationData;
use App\Modules\Credit\Application\DTO\CreditDecision;
use App\Modules\Client\Domain\Repository\ClientRepository;
use App\Modules\Credit\Domain\RuleSets\RulesSet;

final class CheckCreditHandler
{
    public function __construct(
        private ClientRepository $clients,
        private RulesSet $rulesSet,
    ) {}

    public function handle(CheckCredit $command): CreditDecision
    {
        $client = $this->clients->findById($command->clientId);

        $applicationData = CreditApplicationData::fromArray([
            'name'       => $command->name,
            'amount'     => $command->amount,
            'rate'       => $command->rate,
            'start_date' => $command->startDate,
            'end_date'   => $command->endDate,
        ]);

        return $this->rulesSet->validate($client, $applicationData);
    }
}
