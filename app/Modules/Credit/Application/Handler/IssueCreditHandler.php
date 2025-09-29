<?php

namespace App\Modules\Credit\Application\Handler;

use App\Modules\Credit\Application\Command\IssueCredit;
use App\Modules\Credit\Domain\Entity\Credit;
use App\Modules\Credit\Domain\Repository\CreditRepository;
use App\Modules\Client\Domain\Repository\ClientRepository;
use App\Modules\Notification\Application\Handler\SendNotificationHandler;
use App\Modules\Notification\Application\Command\SendNotification;

final class IssueCreditHandler
{
    public function __construct(
        private ClientRepository $clients,
        private CreditRepository $credits,
        private SendNotificationHandler $notifierHandler ,
    ) {}

    public function handle(IssueCredit $cmd): void
    {
        $client = $this->clients->findById($cmd->clientId);
        if (!$client) {
            throw new \RuntimeException('Client Not Found');
        }

        $credit = new Credit(
            $cmd->id,
            $cmd->clientId,
            $cmd->name,
            $cmd->amount,
            $cmd->rate,
            new \DateTimeImmutable($cmd->startDate),
            new \DateTimeImmutable($cmd->endDate),
        );

        $this->credits->save($credit);

        $this->notifierHandler->handle(
            new SendNotification(
                $client->id,
                "Credit '{$credit->name}' for {$credit->amount} issued"
            )
        );
    }
}
