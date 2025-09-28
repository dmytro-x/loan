<?php

namespace App\Modules\Client\Application\Handler;

use App\Modules\Client\Application\Command\CreateClient;
use App\Modules\Client\Domain\Entity\Client;
use App\Modules\Client\Domain\Repository\ClientRepository;

final class CreateClientHandler
{
    public function __construct(private ClientRepository $repo) {}

    public function handle(CreateClient $cmd): void
    {
        $client = new Client(
            $cmd->id,
            $cmd->name,
            $cmd->age,
            $cmd->region,
            $cmd->pin,
            $cmd->email,
            $cmd->phone,
            $cmd->income,
            $cmd->score
        );
        $this->repo->save($client);
    }
}
