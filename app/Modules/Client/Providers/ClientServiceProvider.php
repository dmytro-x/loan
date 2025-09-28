<?php

namespace App\Modules\Client\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Client\Domain\Repository\ClientRepository;
use App\Modules\Client\Infrastructure\EloquentClientRepository;

class ClientServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ClientRepository::class, EloquentClientRepository::class);
    }
}
