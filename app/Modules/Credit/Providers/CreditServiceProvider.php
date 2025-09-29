<?php

namespace App\Modules\Credit\Providers;

use App\Modules\Credit\Domain\RuleSets\RulesSet;
use App\Modules\Credit\Domain\RuleSets\TestAssignmentRulesSet;
use Illuminate\Support\ServiceProvider;
use App\Modules\Credit\Domain\Repository\CreditRepository;
use App\Modules\Credit\Infrastructure\EloquentCreditRepository;

class CreditServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CreditRepository::class, EloquentCreditRepository::class);
        $this->app->bind(RulesSet::class, TestAssignmentRulesSet::class);
    }
}
