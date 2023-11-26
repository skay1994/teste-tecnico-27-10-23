<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'document' => $this->faker->numerify('###########'),
            'cns' => $this->faker->numerify('###############'),
            'photo' => $this->faker->imageUrl,
            'name' => $this->faker->name(),
            'mother_name' => $this->faker->firstNameFemale,
            'birthdate' => $this->faker->date(max: '-30 years'),
            'phone' => $this->faker->phoneNumber,
        ];
    }
}
