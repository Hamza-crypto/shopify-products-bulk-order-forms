<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MeterReadingSeeder extends Seeder
{
    public function run()
    {
        // Define variables for customization
        $startDate = '2024-07-01';
        $numberOfDays = 30; // Adjust as needed
        $meterNames = ['meter1', 'meter2']; // Adjust as needed

        $meterReadings = [];

        $readingValue = 1000;
        // Generate readings for each meter for the specified number of days

           // Adjust your seeder logic to generate unique timestamps for each meter reading
$currentDate = Carbon::parse($startDate)->startOfDay();

for ($i = 0; $i < $numberOfDays; $i++) {
    foreach ($meterNames as $meterName) {
        // Generate a unique timestamp for each meter reading
        $uniqueTimestamp = $currentDate->copy()->addSeconds(rand(1, 59)); // Add random seconds to ensure uniqueness

        $readingValue += rand(5, 20); // Random reading value for demonstration
        $meterReadings[] = [
            'meter_name' => $meterName,
            'reading_value' => $readingValue,
            'created_at' => $uniqueTimestamp,
            'updated_at' => Carbon::now(),
        ];
    }

    // Move to the next day
    $currentDate->addDay();


        }
// dd($meterReadings);
        // Insert custom values into the database
        DB::table('meter_readings')->insert($meterReadings);
    }

}
