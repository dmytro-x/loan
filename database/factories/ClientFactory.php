<?php

namespace Database\Factories;

use App\Modules\Client\Infrastructure\EloquentClientModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Client\Infrastructure\EloquentClientModel>
 */
class ClientFactory extends Factory
{
    protected $model = EloquentClientModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'   => $this->faker->name(),
            'age'    => $this->faker->numberBetween(18, 60),
            'region' => $this->faker->randomElement(['PR', 'BR', 'OS']),
            'income' => $this->faker->randomFloat(2, 0, 10000),
            'score'  => $this->faker->numberBetween(0, 1000),
            'pin'    => $this->faker->unique()->numerify('###-##-####'),
            'email'  => $this->faker->unique()->safeEmail(),
            'phone'  => $this->faker->e164PhoneNumber(),
        ];
    }
}
