<?php

namespace App\Modules\Notification\Application\Handler;

use App\Modules\Notification\Application\Command\SendNotification;
use App\Modules\Notification\Domain\Service\Notifier;
use App\Modules\Client\Domain\Repository\ClientRepository;

final class SendNotificationHandler
{
    public function __construct(
        private ClientRepository $clients,
        private Notifier $notifier
    ) {}

    public function handle(SendNotification $cmd): void
    {
        $client = $this->clients->findById($cmd->clientId);

        if (!$client) {
            throw new \RuntimeException("Client not found");
        }

        $this->notifier->notify($client, $cmd->message);
    }
}
