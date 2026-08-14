<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Product::all();
    }

    public function headings(): array
    {
        return [
            'product_code',
            'product_name',
            'product_name_mm',
            'brand_name',
            'category_name',
            'sub_category_name',
            'product_group_name',
            'product_pattern_name',
            'product_type_name',
            'product_tags',
            'product_description',
            'product_description_mm',
            'product_unit',
            'show_pro1_logo',
            'item_code',
            'item_name',
            'item_color',
            'item_sell_price',
            'item_member_price',
            'image_name',
            'branch_code_list',
            'product_feature_status',
            'product_low_stock_qty',
            'product_custom_order',
            'product_custom_order_note',
            'upsell_list',
            'product_model',
            'product_country_of_origin',
            'product_custom_product_type',
            'product_weight',
            'product_length',
            'product_width',
            'product_height',
            'product_size',
            'additional_info_1',
            'additional_info_2',
            'additional_info_3',
            'additional_info_4',
            'additional_info_5',
            'product_shipping_class',
            'product_tax',
            'layer_1',
            'layer_2',
            'layer_3',
            'layer_4',
            'layer_5',
            'layer_6',
            'layer_7',
            'layer_8',
            'foc_status',
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->product_code,
        ];
    }

}