<?php

namespace App\Modules\Credit\Application\Handler;

use App\Modules\Credit\Application\Command\CheckCredit;
use App\Modules\Credit\Application\Command\IssueCredit;
use App\Modules\Credit\Application\DTO\CreditApplicationData;
use App\Modules\Credit\Application\DTO\CreditDecision;
use App\Modules\Credit\Domain\Entity\Credit;
use App\Modules\Credit\Domain\Repository\CreditRepository;
use App\Modules\Client\Domain\Repository\ClientRepository;
use App\Modules\Credit\Domain\RuleSets\RulesSet;
use App\Modules\Notification\Application\Handler\SendNotificationHandler;
use App\Modules\Notification\Application\Command\SendNotification;

final class IssueCreditHandler
{
    public function __construct(
        private ClientRepository $clients,
        private RulesSet $rulesSet,
        private CreditRepository $credits,
        private SendNotificationHandler $notifierHandler,
    ) {}

    public function handle(IssueCredit $command): CreditDecision
    {
        $client = $this->clients->findById($command->clientId);
        if (!$client) {
            throw new \RuntimeException('Client Not Found');
        }

        $applicationData = CreditApplicationData::fromArray([
            'name'       => $command->name,
            'amount'     => $command->amount,
            'rate'       => $command->rate,
            'start_date' => $command->startDate,
            'end_date'   => $command->endDate,
        ]);

        $decision = $this->rulesSet->validate($client, $applicationData);

        $credit = new Credit(
            id: $command->id,
            clientId: $command->clientId,
            name: $command->name,
            amount: $command->amount,
            rate: $decision->interestRate,
            startDate: new \DateTimeImmutable($command->startDate),
            endDate: new \DateTimeImmutable($command->endDate),
            isApproved: $decision->approved,
            rejectionReasons: $decision->rejectionReasons,
        );

        $this->credits->save($credit);

        $this->notifierHandler->handle(
            new SendNotification(
                $client->id,
                "Credit '{$credit->name}' for {$credit->amount}" . ($decision->approved ? ' issued' : ' declined')
            )
        );

        return $decision;
    }
}
