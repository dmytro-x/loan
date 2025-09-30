<?php

namespace App\Modules\Notification\Application\Command;

class SendNotification
{
    public function __construct(
        public string $clientId,
        public string $message
    ) {}
}
