<?php


// app/Http/Controllers/CustomerController.php

namespace App\Http\Controllers;

use App\Models\ProductUpload;
use App\Http\Requests\CustomerSubmitRequest;
use App\Jobs\SendProductSelectionEmail;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function showProducts($unique_id)
    {
        $productUpload = ProductUpload::where('unique_id', $unique_id)->firstOrFail();
        $filePath = storage_path('app/' . $productUpload->file_path);
        $file = fopen($filePath, 'r');

        $header = fgetcsv($file);
        $products = [];
        $counter = 0;
        while ($row = fgetcsv($file)) {

            $data = array_combine($header, $row);

            $handle = $data['Handle'];
            $variantImage = $data['Variant Image'];
            $sku = $data['Variant SKU'];

            $counter++;
            // if($counter < 60) continue;
            // if($counter > 80) break;
            // Check if the product handle already exists

            $products[$handle][] = [
                'handle' => $data['Handle'] ?? '',
                'title' => $data['Title'] ?? '',
                'description' => $data['Body (HTML)'] ?? '',
                'type' => $data['Type'] ?? '',
                'sku' => $sku,
                'price' => $data['Variant Price'] ?? '',
                'wholesale_price' => $data['Wholesale Price'] ?? '-',
                'status' => $data['Status'] ?? '-',
                'brand' => $data['Vendor'] ?? 'No brand',
                'variant_img' => $variantImage,
                'image' => $variantImage== "" ? $data['Image Src'] : $variantImage,
                'variant' => $this->get_variants($data)
            ];

            if (!empty($variant)) {
            $products[$handle]['variants'][] = $variant;
        }


        }

        fclose($file);

// dd($products);
        return view('pages.customer.products', ['products' => $products, 'unique_id' => $unique_id, 'domain' => $productUpload->domain]);
    }

    public function submitProducts(CustomerSubmitRequest $request)
    {
        // Extract customer information
        $customerInfo = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
        ];

        // Extract and filter selected products
        $selectedProducts = array_filter($request->input('products'), function ($product) {
            return isset($product['selected']);
        });


        // Generate CSV file
        $fileName = 'selected_products_' . now()->timestamp . '.csv';
        $filePath = 'attachments/' . $fileName;

        // Add CSV headers and data
        $csvData = [];
        $csvData[] = ['Title', 'Price', 'Quantity', 'SKU'];
        foreach ($selectedProducts as $product) {
            $csvData[] = [
                $product['title'],
                $product['price'],
                $product['quantity'],
                $product['sku'],
            ];
        }

        // Store the CSV file in the public disk
        Storage::disk('public')->put($filePath, $this->arrayToCsv($csvData));

        // Generate the download link
        $downloadLink = Storage::disk('public')->url($filePath);


        // Ensure the file exists at the generated link
        if (!Storage::disk('public')->exists($filePath)) {
            return back()->with('error', 'Failed to create CSV file.');
        }


        // Prepare email data
        $emailData = [
            'customerInfo' => $customerInfo,
            'unique_id' => $request->input('unique_id'),
            'downloadLink' => $downloadLink,
        ];

        // Dispatch the job to send the email
        SendProductSelectionEmail::dispatch($emailData);


        // Redirect back with a success message
        return back()->with('success', 'Your selection has been submitted successfully!');
    }


    private function arrayToCsv(array $data)
    {
        $csv = '';
        foreach ($data as $row) {
            $escapedRow = array_map(function ($field) {
                if (strpos($field, ',') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false) {
                    $field = '"' . str_replace('"', '""', $field) . '"';
                }
                return $field;
            }, $row);
            $csv .= implode(',', $escapedRow) . "\n";
        }
        return $csv;
    }

    private function get_variants($data)
    {
        // Collect variant options
        $variant = '';
        if (!empty($data['Option1 Value'])) {
            $variant .= $data['Option1 Value'];
        }
        if (!empty($data['Option2 Value'])) {
            $variant .= ", " . $data['Option2 Value'];
        }
        if (!empty($data['Option3 Value'])) {
            $variant .= ", " .$data['Option3 Value'];
        }

        return $variant;
    }
}