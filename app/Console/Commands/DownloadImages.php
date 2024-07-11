<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FileEntry;

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
        $batchSize = 50;

        $totalDownloads = 0;
        $total_products = $fileEntry->total_products;

        $products = get_active_products($filePath);

        $count = 0;

        foreach ($products as $product) {
            $count++;

            if ($count <= $fileEntry->processed_products) {
                continue;
            }

            $img_url = empty($product['Variant Image']) ? $product['Image Src'] : $product['Variant Image'];

            $totalDownloads += downloadImage( $img_url )[0]; //it returns [1, $imagePath] or  [0, $imagePath]
            if ($totalDownloads >= $batchSize) {
                $fileEntry->processed_products = $count;
                $fileEntry->save();
                $this->info("Downloaded $totalDownloads images.");
                return; // Exit the function after downloading 40 images
            }
        }

        $fileEntry->processed_products = $count;
        // $fileEntry->save();


        if($count >= $total_products) {
            $fileEntry->active = false;
            $fileEntry->save();
            $this->info('All entries processed. File entry marked as inactive.');
        }
    }

}