<?php


// app/Http/Controllers/CustomerController.php

namespace App\Http\Controllers;

use App\Models\ProductUpload;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\CustomerSubmitRequest;
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
        $productHandles = [];

        while ($row = fgetcsv($file)) {
            $data = array_combine($header, $row);
            $handle = $data['Handle'];

            if (!isset($productHandles[$handle])) {
                $productHandles[$handle] = [
                    'handle' => $data['Handle'] ?? '',
                    'title' => $data['Title'] ?? '',


                    'type' => $data['Type'] ?? '',
                    'sku' => $data['Variant SKU'] ?? '',



                    'price' => $data['Variant Price'] ?? '',
                    'image_src' => $data['Image Src'],
                ];
            } elseif (isset($data['Image Src']) && $data['Image Src']) {
                $productHandles[$handle]['image_src'] = $data['Image Src'];
            }
        }

        fclose($file);

        return view('pages.customer.products', ['products' => $productHandles, 'unique_id' => $unique_id]);
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


        // Ensure the directory exists
        $directory = storage_path('app/attachments');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }


        // Generate CSV file
        $fileName = 'selected_products_' . now()->timestamp . '.csv';
        $filePath = storage_path('app/attachments/' . $fileName);
        $file = fopen($filePath, 'w');


        // Add CSV headers
        fputcsv($file, ['Title', 'Price', 'Quantity', 'SKU']);

        // Add CSV data
        foreach ($selectedProducts as $product) {
            fputcsv($file, [
                $product['title'],
                $product['price'],
                $product['quantity'],
                $product['sku'],
            ]);
        }

        fclose($file);


        // Generate the download link
        $downloadLink = Storage::url($filePath);

        // Prepare email data
        $emailData = [
            'customerInfo' => $customerInfo,
            'unique_id' => $request->input('unique_id'),
             'downloadLink' => $downloadLink,
        ];


        // Send email to admin with attachment
        Mail::send('emails.admin', $emailData, function ($message) use ($customerInfo, $filePath) {
            $message->to('admin@example.com')
                    ->subject('New Product Selection from ' . $customerInfo['name'])
                    ->attach($filePath);
        });

        // Redirect back with a success message
        return back()->with('success', 'Your selection has been submitted successfully!');
    }
}
