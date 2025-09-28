<?php
namespace App\Modules\Client\Domain\Repository;

use App\Modules\Client\Domain\Entity\Client;

interface ClientRepository
{
    public function save(Client $client): void;
    public function findById(string $id): ?Client;
}
