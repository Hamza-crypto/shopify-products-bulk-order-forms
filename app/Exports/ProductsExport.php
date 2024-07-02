<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class ProductsExport implements FromArray, WithHeadings, WithMultipleSheets, WithDrawings, WithColumnWidths, WithStyles
{
    private $products;
    private $drawings = [];

    public function __construct(array $products)
    {
        $this->products = $products;
        $this->prepareDrawings();
    }

    public function array(): array
    {
        // Add an empty column at the start of each product's data array
        return array_map(function($product) {
            return array_merge([''], $product);
        }, $this->products);
    }


    public function headings(): array
    {
        return [
            'Image Preview',
            'Product Name',
            'Description',
            'Price'
        ];
    }

     private function prepareDrawings()
    {
        foreach ($this->products as $index => $product) {
            if (!empty($product['Variant Image'])) {
                $imagePath = $this->getImagePath($product['Variant Image'], $index);

                if ($imagePath) {
                    $drawing = new Drawing();
                    $drawing->setName($product['Handle']);
                    $drawing->setPath($imagePath);
                    $drawing->setWidth(100);
                    // $drawing->setheight(150);

                    // Center the image in the cell
                // $drawing->setOffsetX(5);  // Adjust horizontal offset
                // $drawing->setOffsetY(5);  // Adjust vertical offset


                    $drawing->setCoordinates('A' . ($index + 2));
                    $this->drawings[] = $drawing;
                }
            }
        }
    }

    private function getImagePath($url, $index)
    {
        $url .=  "&width=70";
        $slug = Str::slug($url);
        $filename = $slug;

        $product = Product::toBase()->where('url', $url)->select('path')->first();

        if($product) return $product->path;

        // Otherwise, download the image
        return $this->downloadImage($url, $filename);
    }


    private function downloadImage($url, $filename)
    {
        \Log::info('Downloaded func called ', ['url' => $url]);
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

                return $imagePath;
            }
        } catch (\Exception $e) {
            \Log::error('Failed to download image', ['url' => $url, 'error' => $e->getMessage()]);
        }
        return null;
    }

    public function drawings()
    {
        return $this->drawings;
    }

    public function sheets(): array
    {
        return [
            'Products' => $this,
        ];
    }

    // public function __destruct()
    // {
    //     // Clean up temporary images
    //     foreach ($this->drawings as $drawing) {
    //         if (file_exists($drawing->getPath())) {
    //             unlink($drawing->getPath());
    //         }
    //     }
    // }


    // Set default column widths
    public function columnWidths(): array
    {
        return [
            'A' => 21,  // Set the width of the 'Image Preview' column
        ];
    }

    // Set default row height and other styles
    public function styles(Worksheet $sheet)
    {
        foreach ($this->products as $index => $product) {
            $sheet->getRowDimension($index + 2)->setRowHeight(75);  // Set row height for each row with images
        }
    }
}