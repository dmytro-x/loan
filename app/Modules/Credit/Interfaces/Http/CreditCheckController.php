<?php

namespace App\Modules\Credit\Interfaces\Http;

use App\Modules\Client\Infrastructure\EloquentClientModel;
use App\Modules\Credit\Application\Command\CheckCredit;
use App\Modules\Credit\Application\Handler\CheckCreditHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class CreditCheckController extends Controller
{
    public function __invoke(Request $request, CheckCreditHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'clientId' => ['required', 'ulid'],
            'name' => 'required|string',
            'amount' => ['required', 'numeric'],
            'rate' => ['required'],
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', 'after:start_date'],
        ]);

        $client = EloquentClientModel::find($validated['clientId']);

        if (!$client) {
            return response()->json(['error' => 'Client not found'], Response::HTTP_NOT_FOUND);
        }

        $command = new CheckCredit(...$validated);

        $decision = $handler->handle($command);

        return response()->json($decision);
    }
}
