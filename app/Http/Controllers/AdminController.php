<?php


// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Models\FileEntry;
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
        $active_products = [];

        while (($row = fgetcsv($fileHandle)) !== false) {
            $product = array_combine($header, $row);
            $handle = $product['Handle'];

            if (!isset($products[$handle])) {
                $products[$handle] = [];
            }

            // Remove HTML tags from "Body (HTML)" field
            if (isset($product['Body (HTML)'])) {
                $product['Body (HTML)'] = strip_tags($product['Body (HTML)']);
            }

            $products[$handle][] = $product;
        }


        foreach ($products as $handle => $variants) {
            $mainProduct = $variants[0];

            $status = strtolower($mainProduct['Status']);

            if ($status === 'active') {
                foreach ($variants as $variant) {
                    $active_products[] = $variant;
                }
            }

        }

        fclose($fileHandle);
        return $active_products;
    }

    public function download_images(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv',
        ]);

        $file = $request->file('file');
        $filePath = $file->store('download_images');

        FileEntry::create([
            'filename' => $filePath,
            'total_products' => $this->countCSVRows($filePath),
        ]);

        return back()->with('success', 'File uploaded successfully! We will let you know when all images are downloaded.');
    }

    private function countCSVRows($filePath)
    {
        $filePath = storage_path('app/' . $filePath);
        $fileHandle = fopen($filePath, 'r');
        $header = fgetcsv($fileHandle);

        $products = [];

        while (($row = fgetcsv($fileHandle)) !== false) {
            $product = array_combine($header, $row);
            $handle = $product['Handle'];

            if (!isset($products[$handle])) {
                $products[$handle] = [];
            }

            $products[$handle][] = $product;
        }

        fclose($fileHandle);

        return count($products);
    }
}