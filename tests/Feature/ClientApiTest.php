<?php

namespace Tests\Feature;

use App\Modules\Client\Infrastructure\EloquentClientModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ClientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_be_created_via_api()
    {
        $payload = [
            'name' => 'John Doe',
            'age' => 30,
            'region' => 'BR',
            'income' => 2000,
            'score' => 650,
            'pin' => '123-45-6789',
            'email' => 'john@example.com',
            'phone' => '+420123456789',
        ];

        $response = $this->postJson(route('clients.store', $payload));

        $response->assertCreated()
            ->assertJsonStructure(['id', 'message']);

        $this->assertDatabaseHas('clients', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);
    }

    public function test_client_creation_fails_if_region_not_setted()
    {
        $payload = EloquentClientModel::factory()->make(['region' => null])->toArray();

        $response = $this->postJson(route('clients.store'), $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['region']);
    }

    public function test_client_creation_fails_if_email_is_invalid()
    {
        $payload = EloquentClientModel::factory()->make(['email' => 'not-an-email'])->toArray();

        $response = $this->postJson(route('clients.store'), $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_client_creation_fails_if_income_incorrect()
    {
        $payload = EloquentClientModel::factory()->make()->toArray();
        $payload = array_merge($payload, ['income' => 'big money']);

        $response = $this->postJson(route('clients.store'), $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['income']);
    }

    public function test_get_client_by_id(){
        $client = EloquentClientModel::factory()->create();

        $response = $this->getJson(route('clients.show', $client->id));

        $response->assertOk()
            ->assertJson([
                'id' => $client->id,
                'name' => $client->name,
            ]);
    }

    public function test_returns_404_if_client_not_found()
    {
        $ulid = (string) Str::ulid();

        $response = $this->getJson(route('clients.show', $ulid));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_returns_422_if_id_is_not_ulid()
    {
        $response = $this->getJson(route('clients.show', '123'));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
