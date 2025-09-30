<?php

namespace App\Modules\Client\Infrastructure;

use App\Exceptions\DatabaseException;
use App\Modules\Client\Domain\Entity\Client;
use App\Modules\Client\Domain\Repository\ClientRepository;
use Illuminate\Database\QueryException;

final class EloquentClientRepository implements ClientRepository
{
    public function save(Client $client): void
    {
        try {
            EloquentClientModel::updateOrCreate(
                ['id' => $client->id],
                [
                    'name' => $client->name,
                    'age' => $client->age,
                    'region' => $client->region,
                    'pin' => $client->pin,
                    'email' => $client->email,
                    'phone' => $client->phone,
                    'income' => $client->income,
                    'score' => $client->score,
                ]
            );
        } catch (QueryException $e) {
            \Log::error('Failed to save Credit', [
                'exception' => $e,
                'client_id' => $client->id,
                'sql_error' => $e->getMessage(),
                'sql_code'  => $e->getCode(),
            ]);

            throw new DatabaseException('Failed to save Client', 0, $e);
        }
    }

    public function findById(string $id): ?Client
    {
        $model = EloquentClientModel::find($id);
        return $model
            ? new Client(
                $model->id,
                $model->name,
                $model->age,
                $model->region,
                $model->pin,
                $model->email,
                $model->phone,
                $model->income,
                $model->score,
            )
            : null;
    }
}
