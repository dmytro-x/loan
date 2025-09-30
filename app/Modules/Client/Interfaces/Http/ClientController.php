<?php

namespace App\Modules\Client\Interfaces\Http;

use App\Modules\Client\Application\Command\CreateClient;
use App\Modules\Client\Application\Handler\CreateClientHandler;
use App\Modules\Client\Infrastructure\EloquentClientModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends Controller
{
    public function store(Request $request, CreateClientHandler $handler)
    {
        $validated = $request->validate([
            'name'   => 'required|string',
            'age'    => 'required|integer|min:0',
            'region' => 'required|string',
            'income' => 'required|numeric|min:0',
            'score'  => 'required|integer|min:0',
            'pin'    => 'required|string',
            'email'  => 'required|email',
            'phone'  => 'required|string',
        ]);

        $payload = array_merge(
            ['id' => (string) \Illuminate\Support\Str::ulid()],
            $validated
        );

        $command = new CreateClient(
            ...$payload
        );

        $handler->handle($command);

        return response()->json([
            'id' => $command->id,
            'message' => 'Client created successfully',
        ], Response::HTTP_CREATED, [
            'Location' => route('clients.show', $command->id),
        ]);
    }

    public function show($clientId)
    {
        abort_unless(Str::isUlid($clientId), Response::HTTP_UNPROCESSABLE_ENTITY);

        $client = EloquentClientModel::findOrFail($clientId);

        return response()->json($client);
    }
}
