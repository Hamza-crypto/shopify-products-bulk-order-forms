<?php

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

if (!function_exists('downloadImage')) {
    function downloadImage($url) {

        try {
            // Append width parameter to the URL
            $urlWithWidth = $url . "&width=100";

            // Generate a slug for the filename
            $slug = Str::slug(parse_url($urlWithWidth, PHP_URL_PATH));

            // Check if the product already exists in the database
            $product = Product::where('url', $urlWithWidth)->first();

            if ($product) {
                return [0, $product->path];
            }

            // Fetch the image from the URL
            $response = Http::get($urlWithWidth);
            if ($response->successful()) {
                // Get the image extension
                $extension = pathinfo(parse_url($urlWithWidth, PHP_URL_PATH), PATHINFO_EXTENSION);
                $filename = $slug . '.' . $extension;
                $imagePath = storage_path('app/public/' . $filename);

                // Save the image to storage
                file_put_contents($imagePath, $response->body());

                // Store the URL and path in the database
                Product::create([
                    'url' => $urlWithWidth,
                    'path' => $imagePath
                ]);

                return [1, $imagePath];
            }
        } catch (\Exception $e) {
            \Log::error('Failed to download image', ['url' => $url, 'error' => $e->getMessage()]);
        }
        return [0, null];
    }
}

if (!function_exists('get_active_products')) {
    function get_active_products($file_handle) {
        // Open the file for reading
        $fileHandle = fopen($file_handle, 'r');
        $header = fgetcsv($fileHandle);

        $products = [];
        $active_products = [];

        // Process the CSV file
        while (($row = fgetcsv($fileHandle)) !== false) {
            $product = array_combine($header, $row);
            $handle = $product['Handle'];

            // Group products by handle
            $products[$handle][] = $product;
        }

        // Process grouped products to filter active ones
        foreach ($products as $variants) {
            $mainProduct = $variants[0];
            $status = strtolower($mainProduct['Status']);

            // if ($status === 'active') {
                foreach ($variants as $index => $variant) {
                    if ($index === 0 || !empty($variant['Variant Image'])) {

                    // Remove HTML tags from "Body (HTML)" field
                    if (isset($variant['Body (HTML)'])) {
                        $variant['Body (HTML)'] = strip_tags($variant['Body (HTML)']);
                    }

                        $active_products[] = $variant;
                    }
                }
            // }
        }

        // Close the file handle
        fclose($fileHandle);

        return $active_products;
    }
}
