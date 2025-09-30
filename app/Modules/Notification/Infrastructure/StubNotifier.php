<?php
namespace App\Modules\Notification\Infrastructure;

use App\Modules\Notification\Domain\Service\Notifier;
use App\Modules\Client\Domain\Entity\Client;

final class StubNotifier implements Notifier
{
    public function notify(Client $client, string $message): void
    {
        logger("Message for {$client->name}: {$message}");
    }
}
