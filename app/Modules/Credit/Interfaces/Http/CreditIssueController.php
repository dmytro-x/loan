<?php

namespace App\Modules\Credit\Interfaces\Http;

use App\Modules\Client\Infrastructure\EloquentClientModel;
use App\Modules\Credit\Application\Command\IssueCredit;
use App\Modules\Credit\Application\Handler\IssueCreditHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CreditIssueController extends Controller
{
    public function __construct(private IssueCreditHandler $handler) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'clientId' => ['required', 'ulid'],
            'name' => 'required|string',
            'amount' => ['required', 'numeric'],
            'rate' => ['required', 'numeric'],
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', 'after:start_date'],
        ]);

//        $payload = array_merge(
//            ['id' => (string) \Illuminate\Support\Str::ulid()],
//            $validated
//        );
//
//        $command = new IssueCredit(
//            ...$payload,
//        );

        $client = EloquentClientModel::find($validated['clientId']);

        if (!$client) {
            return response()->json(['error' => 'Client not found'], Response::HTTP_NOT_FOUND);
        }

        $command = new IssueCredit(
            id: (string) \Illuminate\Support\Str::ulid(),
            clientId: $validated['clientId'],
            name: $validated['name'],
            amount: $validated['amount'],
            rate: $validated['rate'],
            startDate: $validated['startDate'],
            endDate: $validated['endDate'],
        );

        $decision = $this->handler->handle($command);

        if ($decision->approved) {
            return response()->json([
                'message'          => 'Credit issued successfully',
                'approved'         => $decision->approved,
                'rejectionReasons' => $decision->rejectionReasons,
                'interestRate'     => $decision->interestRate,
                'creditId'         => $command->id,
            ], Response::HTTP_CREATED);
        } else {
            return response()->json([
                'message'          => 'Credit declined',
                'approved'         => $decision->approved,
                'rejectionReasons' => $decision->rejectionReasons,
                'interestRate'     => $decision->interestRate,
                'creditId'         => $command->id,
            ], Response::HTTP_OK);
        }
    }
}
