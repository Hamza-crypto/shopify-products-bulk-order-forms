<?php

namespace App\Http\Controllers;

use App\Models\MeterReading;
use App\Models\LastBilledReading;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MeterReadingController extends Controller
{
    public function showLastBilledReadingForm()
    {
        return view('settings.set_last_billed_reading');
    }

    public function storeLastBilledReading(Request $request)
    {
        $validated = $request->validate([
            'meter_name' => 'required|string|in:meter1,meter2',
            'reading_value' => 'required|numeric',
        ]);

        LastBilledReading::updateOrCreate(
            ['meter_name' => $validated['meter_name']],
            ['reading_value' => $validated['reading_value']]
        );

        return redirect()->route('last-billed-reading-form')->with('success', 'Last billed reading updated successfully.');
    }

    public function showMeterReadingForm()
    {
        return view('meter_readings.index');
    }

    public function storeMeterReading(Request $request)
    {
        $validated = $request->validate([
        'meter_name' => 'required|string|in:meter1,meter2',
        'reading_value' => 'required|numeric',
        ]);

        $today = Carbon::now()->startOfDay();

        // Find the existing record for today, if it exists
        $existingReading = MeterReading::where('meter_name', $validated['meter_name'])
            ->whereDate('created_at', $today)
            ->first();

        if ($existingReading) {
            // Update the existing record
            $existingReading->reading_value = $validated['reading_value'];
            $existingReading->save();
        } else {
            // Create a new record
            MeterReading::create([
                'meter_name' => $validated['meter_name'],
                'reading_value' => $validated['reading_value'],
                'created_at' => $today,
            ]);
        }

        return redirect()->route('electricity-graph')->with('success', 'Meter reading updated successfully.');
    }

    public function showElectricityGraph()
{
    $currentMonth = Carbon::now()->startOfMonth();

    $meter1Readings = MeterReading::where('meter_name', 'meter1')
        ->whereDate('created_at', '>=', $currentMonth)
        ->orderBy('created_at')
        ->get();

    $meter2Readings = MeterReading::where('meter_name', 'meter2')
        ->whereDate('created_at', '>=', $currentMonth)
        ->orderBy('created_at')
        ->get();

    $lastBilledReadingMeter1 = LastBilledReading::where('meter_name', 'meter1')->value('reading_value');
    $lastBilledReadingMeter2 = LastBilledReading::where('meter_name', 'meter2')->value('reading_value');

    $meter1Data = $this->calculateDailyUsage($meter1Readings, $lastBilledReadingMeter1);
    $meter2Data = $this->calculateDailyUsage($meter2Readings, $lastBilledReadingMeter2);

    $totalMeter1 = array_sum(array_column($meter1Data, 'usage'));
    $totalMeter2 = array_sum(array_column($meter2Data, 'usage'));

    return view('electricity_graph', compact(
        'meter1Data', 'meter2Data', 'totalMeter1', 'totalMeter2'
    ));
}

    private function calculateDailyUsage($readings, $lastBilledReading)
{
    $data = [];
    $previousReading = $lastBilledReading;

    foreach ($readings as $reading) {
        $date = Carbon::parse($reading->created_at)->format('Y-m-d');
        $dailyUsage = $reading->reading_value - $previousReading;

        $data[] = [
            'date' => $date,
            'usage' => $dailyUsage,
        ];

        $previousReading = $reading->reading_value;
    }

    return $data;
}
}
