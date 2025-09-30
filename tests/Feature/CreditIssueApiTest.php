<?php

namespace Tests\Feature;

use App\Modules\Client\Infrastructure\EloquentClientModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CreditIssueApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_credit_issue_fails_validation_if_required_fields_are_missing(): void
    {
        $response = $this->postJson(route('credits.issue'), []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'clientId',
            'name',
            'amount',
            'rate',
            'startDate',
            'endDate',
        ]);
    }

    public function test_credit_issue_creates_credit()
    {
        $client = EloquentClientModel::factory()->create([
            'score' => 700,
            'income' => 3000,
            'age' => 35,
            'region' => 'BR',
        ]);

        $payload = [
            'clientId'  => $client->id,
            'name'      => 'Personal Loan',
            'amount'    => 1000,
            'rate'      => 10,
            'startDate' => now()->toDateString(),
            'endDate'   => now()->addYear()->toDateString(),
        ];

        $response = $this->postJson(route('credits.issue'), $payload);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'message',
                'approved',
                'rejectionReasons',
                'interestRate',
                'creditId',
            ])
            ->assertJson([
                'approved' => true,
                'message'  => 'Credit issued successfully',
            ]);

        $this->assertDatabaseHas('credits', [
            'id'         => $response->json('creditId'),
            'client_id'  => $client->id,
            'name'       => 'Personal Loan',
            'amount'     => 1000,
            'rate'       => 10,
            'is_approved'=> 1,
        ]);
    }

    public function test_credit_issue_creates_rejected_credit(): void
    {
        $client = EloquentClientModel::factory()->create([
            'score' => 200,
            'income' => 300,
            'age' => 17,
            'region' => 'XX',
        ]);

        $payload = [
            'clientId'  => $client->id,
            'name'      => 'Rejected Loan',
            'amount'    => 5000,
            'rate'      => 25,
            'startDate' => now()->toDateString(),
            'endDate'   => now()->addYear()->toDateString(),
        ];

        $response = $this->postJson(route('credits.issue'), $payload);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'approved',
                'rejectionReasons',
                'interestRate',
                'creditId',
            ])
            ->assertJson([
                'approved' => false,
                'message'  => 'Credit declined',
            ]);

        $this->assertDatabaseHas('credits', [
            'id'          => $response->json('creditId'),
            'client_id'   => $client->id,
            'name'        => 'Rejected Loan',
            'amount'      => 5000,
            'rate'        => 25,
            'is_approved' => 0,
        ]);
    }

    public function test_credit_issue_fails_if_wrong_client()
    {
        $payload = [
            'clientId'  => (string) Str::ulid(),
            'name'      => 'Personal Loan',
            'amount'    => 1000,
            'rate'      => 10,
            'startDate' => now()->toDateString(),
            'endDate'   => now()->addYear()->toDateString(),
        ];

        $response = $this->postJson(route('credits.issue'), $payload);

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(['error' => 'Client not found']);
    }

    public function test_credit_issue_fails_if_wrong_amount_format()
    {
        $client = EloquentClientModel::factory()->create([
            'score' => 700,
            'income'=> 2000,
            'age'   => 30,
            'region'=> 'BR',
        ]);

        $payload = [
            'clientId'  => $client->id,
            'name'      => 'Personal Loan',
            'amount'    => 'some money',
            'rate'      => 10,
            'startDate' => now()->toDateString(),
            'endDate'   => now()->addYear()->toDateString(),
        ];

        $response = $this->postJson(route('credits.issue'), $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['amount']);
    }
}
