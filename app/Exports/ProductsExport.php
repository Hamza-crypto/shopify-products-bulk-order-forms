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
         // Filter and map the products array to include only specific columns
        return array_map(function($product) {
            return [
                '', // Empty column for 'Image Preview'
                $product['Title'] ?? '',
                $product['Body (HTML)'] ?? '',
                $this->get_variants($product),
                $this->get_price($product),
                $product['Variant SKU'] ?? '',
                $product['Variant Inventory Qty'] ?? '',
                $product['Variant Barcode'] ?? '',
                $product['Vendor'] ?? '',
                $product['Product Category'] ?? '',
                $product['Status'] ?? '',
            ];
        }, $this->products);
    }


    public function headings(): array
    {
        return [
            'Image Preview',
            'Product Name',
            'Description',
            'Variant Name',
            'Price',
            'Variant SKU',
            'Qty',
            'Variant Barcode',
            'Vendor',
            'Product Category',
            'Status',
        ];
    }

    private function prepareDrawings()
    {
        foreach ($this->products as $index => $product) {

            if (!empty($product['Variant Image'])) {
                $img_url = $product['Variant Image'];
            }
            else{
                 $img_url = $product['Image Src'];
            }

            if($img_url == "") continue;

            $imagePath = downloadImage( $img_url )[1];

            if ($imagePath) {
                $drawing = new Drawing();
                $drawing->setName($product['Handle']);
                $drawing->setPath($imagePath);
                $drawing->setWidth(150);
                // $drawing->setheight(150);

                // Center the image in the cell
                $drawing->setOffsetX(10);  // Adjust horizontal offset
                $drawing->setOffsetY(10);  // Adjust vertical offset

                $drawing->setCoordinates('A' . ($index + 2));
                $this->drawings[] = $drawing;
            }

        }
    }

    private function getImagePath($url)
    {
        if($url == "") return;

        $url .=  "&width=100";
        $slug = Str::slug($url);
        $filename = $slug;

        $product = Product::toBase()->where('url', $url)->select('path')->first();

        if($product) return $product->path;

        // Otherwise, download the image
        return $this->downloadImage($url, $filename);
    }


    private function downloadImage($url, $filename)
    {
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
            'A' => 40,  // Set the width of the 'Image Preview' column
        ];
    }

    // Set default row height and other styles
    public function styles(Worksheet $sheet)
    {
        foreach ($this->products as $index => $product) {
            $sheet->getRowDimension($index + 2)->setRowHeight(150);  // Set row height for each row with images
        }
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

    private function get_price($data)
    {

        if (isset($data['Wholesale Price']) && $data['Wholesale Price'] != "") {

            return $data['Wholesale Price'];
        } else {
            foreach ($data as $key => $value) {
                if (stripos($key, 'Price') === 0) {
                    if($value != "") return $value;
                }
            }

            // Default to 'Variant Price' if no matching column found
            return $data['Variant Price'] ?? null;
        }
    }


}