<?php

namespace App\Console\Commands;

use App\Models\LastBilledReading;
use App\Models\MeterReading;
use App\Notifications\MeterReadingNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Telegram\TelegramChannel;

class SendMeterReading extends Command
{

    protected $signature = 'send-meter-reading';

    protected $description = 'Command description';

    public function handle()
    {
        $last_1_reading = LastBilledReading::where('meter_name', 'meter1')->first();
        $last_2_reading = LastBilledReading::where('meter_name', 'meter2')->first();

        // Fetch total readings for both meters
        $meter1Total = MeterReading::where('meter_name', 'meter1')->max('reading_value');
        $meter2Total = MeterReading::where('meter_name', 'meter2')->max('reading_value');

        $meter1_current_month = $meter1Total - $last_1_reading->reading_value;
        $meter2_current_month = $meter2Total - $last_2_reading->reading_value;
        // Format the message
        $message = "Current Reading\n";
        $message .= "Meter 1 Total: $meter1_current_month units\n";
        $message .= "Meter 2 Total: $meter2_current_month units\n";

        Notification::route(TelegramChannel::class, '')->notify(new MeterReadingNotification($message));
    }
}
