<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Goutte\Client;
use App\Models\Bill;
use App\Notifications\MeterReadingNotification;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use Carbon\Carbon;

class CheckBills extends Command
{
    protected $signature = 'check:bills';
    protected $description = 'Check bills and send new ones to Telegram';


    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
         $currentDate = Carbon::now();
        // Check if the current date is after the 27th of the month
        if ($currentDate->day < 27) {
            return;
        }

        $client = new Client();

        $url = "https://bill.pitc.com.pk/iescobill/general?refno=13142121718200";
        // $url = "http://localhost:8523/IESCO%20ONLINE%20BILL.html";
        $crawler = $client->request('GET', $url);

        // Get the bill month from the HTML
        $billMonth = $crawler->filter('.maintable .content td')->eq(3)->text();

        // Check if the bill month already exists in the database
        if (Bill::where('bill_month', $billMonth)->exists()) {
           return;
        }

        Notification::route(TelegramChannel::class, '')->notify(new MeterReadingNotification($url));

        // Store the bill month in the database
        Bill::create(['bill_month' => $billMonth]);

        return 0;

    }

}