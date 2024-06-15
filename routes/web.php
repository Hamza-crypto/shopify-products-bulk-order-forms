<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('pages.dashboard.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::controller(WebhookController::class)->group(function () {
    Route::get('webhook', 'webhook');
    Route::post('webhook', 'webhook');
});

Route::get('migrate_fresh', function () {
    $res = Artisan::call('migrate:fresh');
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

Route::get('file', function () {


    $url = Storage::disk('public')->url("selected_products_1718441984" . ".csv");
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


Route::get('/admin/upload', [AdminController::class, 'showUploadForm'])->name('admin.upload.form');
Route::post('/admin/upload', [AdminController::class, 'uploadProducts'])->name('admin.upload');
Route::get('/products/{unique_id}', [CustomerController::class, 'showProducts'])->name('customer.products');
Route::post('/products/submit', [CustomerController::class, 'submitProducts'])->name('customer.submit');

//