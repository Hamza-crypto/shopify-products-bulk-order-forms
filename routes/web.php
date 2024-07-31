<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\MeterReadingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/dashboard', function () {
    return view('pages.dashboard.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::get('migrate_fresh', function () {
    Artisan::call('migrate:fresh');
    dump('Database Reset Successfully');
});


Route::get('migrate', function () {

    Artisan::call('migrate');
    dump('Migration Done');
});


Route::get('optimize', function () {
    Artisan::call('optimize:clear');
    dump('Optimization Done');
});

Route::get('storage-link', function () {
    $target = storage_path('app/public');
    $linkfolder = $_SERVER['DOCUMENT_ROOT'] . '/storage';
    symlink($target, $linkfolder);

    dump($target, $linkfolder);

    dump('Link Created');
});



Route::get('file', function () {

    // Generate CSV file
    $fileName = 'selected_products_1718463081'  . '.csv';
    $filePath = 'attachments/' . $fileName;


    $url = Storage::disk('public')->url($filePath);
    dd($url);
    // Generate CSV file
    $fileName = 'selected_products_' . now()->timestamp . '.csv';
    $filePath = 'attachments/' . $fileName;

    // Add CSV headers and data
    $csvData = [];
    $csvData[] = ['Title', 'Price', 'Quantity', 'SKU'];


    $csv = '';
    foreach ($csvData as $row) {
        $csv .= implode(',', $row) . "\n";
    }


    // Store the CSV file in the public disk
    Storage::disk('public')->put($filePath, $csv);

    // Generate the download link
    $downloadLink = Storage::disk('public')->url($filePath);
    dd($downloadLink);
});



require __DIR__.'/auth.php';


Route::get('/', [AdminController::class, 'showUploadForm'])->name('admin.upload.form');
Route::post('/admin/upload', [AdminController::class, 'uploadProducts'])->name('admin.upload');
Route::get('/products/{unique_id}', [CustomerController::class, 'showProducts'])->name('customer.products');
Route::post('/products/submit', [CustomerController::class, 'submitProducts'])->name('customer.submit');

//generate_csv_with_images
Route::post('/upload', [AdminController::class, 'generate_csv_with_images'])->name('admin.generate_directly');

//store csv file for downloading images
Route::post('/admin/download_images', [AdminController::class, 'download_images'])->name('admin.download');
Route::get('/progress', [DashboardController::class, 'showProgress'])->name('admin.show.progress');

Route::get('/send-test-email', function () {
    $details = [
        'title' => 'Test Email from Laravel',
        'body' => 'This is a test email sent using Hostinger SMTP settings in Laravel.'
    ];

    Mail::to('noorareesha162@gmail.com')->send(new \App\Mail\TestMail($details));

    return 'Email Sent!';
});

Route::get('/download-images', function () {

    Artisan::call('images:download');

});


/**
 * Meter Reading
 */

 Route::get('/graph', [MeterReadingController::class, 'showElectricityGraph'])->name('electricity-graph');

Route::get('/last-billed-reading', [MeterReadingController::class, 'showLastBilledReadingForm'])->name('last-billed-reading-form');
Route::post('/last-billed-reading', [MeterReadingController::class, 'storeLastBilledReading'])->name('store-last-billed-reading');

Route::get('/meter-reading', [MeterReadingController::class, 'showMeterReadingForm'])->name('meter-reading-form');
Route::post('/meter-reading', [MeterReadingController::class, 'storeMeterReading'])->name('store-meter-reading');

Route::get('/meter-readings/{meterName}', [MeterReadingController::class, 'getMeterReadings']);
Route::put('/meter-readings/{id}', [MeterReadingController::class, 'update'])->name('update-meter-reading');
Route::delete('/meter-readings/{id}', [MeterReadingController::class, 'destroy']);

Route::get('/send_reading', function () {
    Artisan::call('send-meter-reading');

});


/**
 * Asana
 */
Route::post('/meter-reading', [MeterReadingController::class, 'storeMeterReading'])->name('store-meter-reading');


Route::get('/get_bill', function () {
    Artisan::call('check:bills');

});

//test