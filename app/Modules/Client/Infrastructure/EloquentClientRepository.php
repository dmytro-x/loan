<?php

namespace App\Modules\Client\Infrastructure;

use App\Modules\Client\Domain\Entity\Client;
use App\Modules\Client\Domain\Repository\ClientRepository;

final class EloquentClientRepository implements ClientRepository
{
    public function save(Client $client): void
    {
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
