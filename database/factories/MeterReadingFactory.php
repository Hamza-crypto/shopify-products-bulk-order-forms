<?php

namespace Database\Factories;

use App\Models\MeterReading;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeterReadingFactory extends Factory
{
    protected $model = MeterReading::class;

    public function definition()
    {
        return [
            'meter_name' => $this->faker->randomElement(['meter1', 'meter2']),
            'reading_value' => $this->faker->numberBetween(0, 100),
            'created_at' => now()->subDays(rand(0, 30)), // Generate readings for the past month
        ];
    }
}