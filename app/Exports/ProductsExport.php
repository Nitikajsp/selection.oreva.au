<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $products = Product::where('delete_status', 1)->get();

        logger('Exported products:', $products->toArray()); // Write to laravel.log

        return $products;
    }
    public function headings(): array
    {
        return [
            'Product Name',
            'Category ID',
            'Description',
            'Code',
            'Stock',
            'Stock Status',
            'Image File Name'
        ];
    }

    public function map($product): array
    {
        return [
            $product->product_name,
            $product->product_category,
            $product->product_description,
            $product->product_code,
            $product->product_stock,
            $product->in_stock ? 'yes' : 'no',
            $product->product_image
        ];
    }
}
