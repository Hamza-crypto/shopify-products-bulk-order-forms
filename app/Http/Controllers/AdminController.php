<?php


// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use Illuminate\Http\Request;
use App\Models\ProductUpload;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function showUploadForm()
    {
        return view('pages.admin.upload');
    }

    public function uploadProducts(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv',
        ]);

        $file = $request->file('file');
        $domain = $request->input('domain');
        $filePath = $file->store('uploads');

        $uniqueId = Str::uuid();

        ProductUpload::create([
            'unique_id' => $uniqueId,
            'file_path' => $filePath,
            'domain' => $domain,
        ]);

        $url = sprintf("<strong>%s</strong>", route('customer.products', ['unique_id' => $uniqueId]));


        return back()->with('success', 'File uploaded successfully! Share this URL with your customer: ' . $url);
    }


    public function generate_csv_with_images(Request $request)
    {
        // Read the CSV file
        $csvFile = $request->file('csv_file');
        $products = $this->readCSV($csvFile);

        // Export to Excel
        return Excel::download(new ProductsExport($products), 'products.xlsx');
    }


    private function readCSV($csvFile)
    {
        $fileHandle = fopen($csvFile, 'r');
        $header = fgetcsv($fileHandle);
        $products = [];

        while ($row = fgetcsv($fileHandle)) {
            $products[] = array_combine($header, $row);
        }

        fclose($fileHandle);

        return $products;
    }
}