<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FileEntry;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadImages extends Command
{
    protected $signature = 'images:download';
    protected $description = 'Download images from URLs listed in an Excel file and store them locally';

    public function handle()
    {
        // Get the first active file entry from the database
        $fileEntry = FileEntry::where('active', true)->first();

        if (!$fileEntry) {
            $this->info('No active file entries found.');
            return;
        }

        $filePath = storage_path('app/' . $fileEntry->filename);

        // Process images in chunks
        $this->processCSVInChunks($filePath, $fileEntry);
    }

    private function processCSVInChunks($filePath, $fileEntry)
    {
        $batchSize = 70;
        $currentRow = 0;
        $totalDownloads = 0;
        $totalRows = $fileEntry->total_rows;
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

        foreach ($products as $handle => $variants) {
            $mainProduct = $variants[0];
            $default_column_for_variant_img = "Variant Image";
            $status = strtolower($mainProduct['Status']);

            if ($status === 'active') {

                foreach ($variants as $index => $variant) {

                    $currentRow++;

                    if ($currentRow <= $fileEntry->processed_rows) {
                        continue;
                    }

                     // Skip if 'Variant Image' is empty
                    if (empty($variant[$default_column_for_variant_img]) ) {
                        if($index != 0){
                            continue; //if it is 3rd or 4th variant and variant img is not present, then skip this row
                        }
                        else{
                            $default_column_for_variant_img = "Image Src";
                        }
                    }
                    dump($totalDownloads);
                    $this->getImagePath($variant[$default_column_for_variant_img], $totalDownloads);

                    if ($totalDownloads >= $batchSize) {
                        $fileEntry->processed_rows = $currentRow;
                        $fileEntry->save();
                        $this->info("Downloaded $totalDownloads images.");
                        return; // Exit the function after downloading 40 images
                    }
                }
            }
        }

        if ($currentRow >= $totalRows) {
            $fileEntry->processed_rows = $currentRow;
            $fileEntry->active = false;
            $fileEntry->save();
            $this->info('All entries processed. File entry marked as inactive.');
        }
    }

    private function getImagePath($url, &$totalDownloads)
    {
        $url .=  "&width=100";
        $slug = Str::slug($url);
        $filename = $slug;

        $product = Product::toBase()->where('url', $url)->select('path')->first();

        if($product) return $product->path;

        // Otherwise, download the image
        return $this->downloadImage($url, $filename, $totalDownloads);
    }

    private function downloadImage($url, $filename, &$totalDownloads)
    {
        dump($url);
        try {
            $response = Http::get($url);
            if ($response->successful()) {
                $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
                $imagePath = storage_path('app/public/' . $filename . '.' . $extension);

                file_put_contents($imagePath, $response->body());

                Product::create([
                    'url' => $url,
                    'path' => $imagePath
                ]);

                $totalDownloads++; // Increment the total downloads count
                return $imagePath;
            }
        } catch (\Exception $e) {
            \Log::error('Failed to download image', ['url' => $url, 'error' => $e->getMessage()]);
        }
        return null;
    }
}