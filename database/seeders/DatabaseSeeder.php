<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\LastBilledReading;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        LastBilledReading::factory([
            'reading_value' => 1000
        ])->count(2)->create();

        $this->call(MeterReadingSeeder::class);
    }
}
