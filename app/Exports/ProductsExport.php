<?php

// app/Exports/ProductsExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function collection()
    {
        return collect($this->products);
    }

    public function headings(): array
    {
        return [
            'Title',
            'Image',
            'Price',
            'Quantity',
        ];
    }
}