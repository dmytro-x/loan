<?php

namespace App\Modules\Notification\Providers;

use App\Modules\Credit\Domain\RuleSets\RulesSet;
use App\Modules\Credit\Domain\RuleSets\TestAssignmentRulesSet;
use App\Modules\Notification\Domain\Service\Notifier;
use App\Modules\Notification\Infrastructure\StubNotifier;
use Illuminate\Support\ServiceProvider;
use App\Modules\Credit\Domain\Repository\CreditRepository;
use App\Modules\Credit\Infrastructure\EloquentCreditRepository;

class NotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Notifier::class, StubNotifier::class);
    }
}
