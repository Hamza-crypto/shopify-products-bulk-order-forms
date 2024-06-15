<?php


// app/Http/Controllers/ProductController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\ProductSelectionMail;
use Illuminate\Support\Facades\Mail;
use App\Exports\ProductsExport;

class ProductController extends Controller
{
    public function showUploadForm()
    {
        return view('upload');
    }

    public function uploadProducts(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();
        $file = fopen($filePath, 'r');

        $header = fgetcsv($file);
        $products = [];
        $productHandles = [];

        while ($row = fgetcsv($file)) {
            $data = array_combine($header, $row);
            $handle = $data['Handle'];

            if (!isset($productHandles[$handle])) {
                $productHandles[$handle] = [
                    'handle' => $data['Handle'],
                    'title' => $data['Title'],
                    'body_html' => $data['Body (HTML)'],
                    'vendor' => $data['Vendor'],
                    'type' => $data['Type'],
                    'tags' => $data['Tags'],
                    'published' => $data['Published'] === 'TRUE',
                    'option1_name' => $data['Option1 Name'],
                    'option1_value' => $data['Option1 Value'],
                    'price' => $data['Variant Price'],
                    'image_src' => $data['Image Src'],
                ];
            } elseif (isset($data['Image Src']) && $data['Image Src']) {
                $productHandles[$handle]['image_src'] = $data['Image Src'];
            }
        }

        fclose($file);

        return view('products', ['products' => $productHandles]);
    }

    public function submitProducts(Request $request)
    {
        $selectedProducts = $request->input('products');
        $quantities = $request->input('quantities');

        $products = [];
        foreach ($selectedProducts as $productId) {
            $products[] = [
                'title' => $request->input("title_$productId"),
                'image' => $request->input("image_$productId"),
                'price' => $request->input("price_$productId"),
                'quantity' => $quantities[$productId],
            ];
        }

        $filePath = storage_path('app/public/products.xlsx');
        Excel::store(new ProductsExport($products), 'public/products.xlsx');

        Mail::to('admin@example.com')->send(new ProductSelectionMail($filePath));

        return back()->with('success', 'Products submitted successfully!');
    }
}