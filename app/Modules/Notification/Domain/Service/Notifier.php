<?php
namespace App\Modules\Notification\Domain\Service;

use App\Modules\Client\Domain\Entity\Client;

interface Notifier
{
    public function notify(Client $client, string $message): void;
}
