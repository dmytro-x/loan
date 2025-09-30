<?php

namespace App\Modules\Credit\Infrastructure;

use App\Exceptions\DatabaseException;
use App\Modules\Credit\Domain\Entity\Credit;
use App\Modules\Credit\Domain\Repository\CreditRepository;
use Illuminate\Database\QueryException;

final class EloquentCreditRepository implements CreditRepository
{
    public function save(Credit $credit): void
    {
        try {
            EloquentCreditModel::create([
                'id' => $credit->id,
                'client_id' => $credit->clientId,
                'name' => $credit->name,
                'amount' => $credit->amount,
                'rate' => $credit->rate,
                'start_date' => $credit->startDate->format('Y-m-d'),
                'end_date' => $credit->endDate->format('Y-m-d'),
                'is_approved' => $credit->isApproved ? 1 : 0,
                'rejection_reasons' => $credit->rejectionReasons,
            ]);
        } catch (QueryException $e) {
            \Log::error('Failed to save Credit', [
                'exception' => $e,
                'credit_id' => $credit->id,
                'client_id' => $credit->clientId,
                'sql_error' => $e->getMessage(),
                'sql_code'  => $e->getCode(),
            ]);

            throw new DatabaseException('Database error on credit save', 0, $e);
        }
    }
}
