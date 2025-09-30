<?php

namespace Tests\Feature;

use App\Modules\Client\Infrastructure\EloquentClientModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CreditCheckApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_credit_check_returns_successful_decision()
    {
        $client = EloquentClientModel::factory()->create([
            'score' => 700,
            'income' => 3000,
            'age' => 35,
            'region' => 'BR',
        ]);

        $payload = [
            'clientId' => $client->id,
            'name' => 'Personal Loan',
            'amount' => 1000,
            'rate' => '10%',
            'startDate' => now()->toDateString(),
            'endDate' => now()->addYear()->toDateString(),
        ];

        $response = $this->postJson(route('credits.check'), $payload);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'approved',
                'rejectionReasons',
                'interestRate',
            ])
            ->assertJson([
                'approved' => true,
                'rejectionReasons' => [],
                'interestRate' => '10',
            ]);
    }

    public function test_credit_check_fails_validation_if_required_fields_are_missing(): void
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

    public function test_credit_check_fails_if_wrong_client_format()
    {
        $payload = [
            'clientId' => '456',
            'amount' => 'some money',
            'rate' => 10,
            'startDate' => now()->toDateString(),
            'endDate' => now()->addYear()->toDateString(),
        ];

        $response = $this->postJson(route('credits.check'), $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['clientId']);
    }

    public function test_credit_check_fails_if_wrong_client()
    {
        $payload = [
            'clientId' => (string) Str::ulid(),
            'name' => 'Credit name',
            'amount' => '1000',
            'rate' => 10,
            'startDate' => now()->toDateString(),
            'endDate' => now()->addYear()->toDateString(),
        ];

        $response = $this->postJson(route('credits.check'), $payload);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_credit_check_fails_if_wrong_amount()
    {
        $client = EloquentClientModel::factory()->create([
            'score' => 700,
            'income' => 2000,
            'age' => 30,
            'region' => 'BR',
        ]);

        $payload = [
            'clientId' => $client->id,
            'amount' => 'some money',
            'rate' => 10,
            'startDate' => now()->toDateString(),
            'endDate' => now()->addYear()->toDateString(),
        ];

        $response = $this->postJson(route('credits.check'), $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['amount']);
    }
}
