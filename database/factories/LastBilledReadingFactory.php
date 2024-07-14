<?php

namespace Database\Factories;

use App\Models\LastBilledReading;
use Illuminate\Database\Eloquent\Factories\Factory;

class LastBilledReadingFactory extends Factory
{
    protected $model = LastBilledReading::class;

    public function definition()
    {
        return [
            'meter_name' => $this->faker->randomElement(['meter1', 'meter2']),
            'reading_value' => $this->faker->numberBetween(1000, 2000),
        ];
    }
}