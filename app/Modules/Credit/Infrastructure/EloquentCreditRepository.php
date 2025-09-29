<?php

namespace App\Modules\Credit\Infrastructure;

use App\Modules\Credit\Domain\Entity\Credit;
use App\Modules\Credit\Domain\Repository\CreditRepository;

final class EloquentCreditRepository implements CreditRepository
{
    public function save(Credit $credit): void
    {
        EloquentCreditModel::create([
            'id' => $credit->id,
            'client_id' => $credit->clientId,
            'name' => $credit->name,
            'amount' => $credit->amount,
            'rate' => $credit->rate,
            'start_date' => $credit->startDate->format('Y-m-d'),
            'end_date' => $credit->endDate->format('Y-m-d'),
        ]);
    }
}
