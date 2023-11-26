<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PatientAddress>
 */
class PatientAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'zipcode' => $this->faker->address,
            'street' => $this->faker->word,
            'number' => $this->faker->numerify,
            'complement' => $this->faker->address,
            'neighborhood' => $this->faker->address,
            'city' => $this->faker->city,
            'state' => $this->faker->address
        ];
    }
}
