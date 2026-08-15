<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProductsExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithMapping
{
    private const COMPANY_MESSAGE = 'PRO 1 Global Home Center မှ အရည်အသွေးကောင်းမွန် သော ပစ္စည်းများကိုသာ ပစ္စည်းမှန်စျေးနှုန်းမှန်ကန်စွာ ရောင်းချသဖြင့် ယုံကြည်စိတ်ချစွာ ၀ယ်ယူနိုင်ပါသည်။';

    public function __construct(private readonly Collection $products) {}

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->products;
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
            'product_feature',
            'status',
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
        $descriptionLines = collect([
            'Brand: '.$product->brand,
            'Name: '.$product->name,
            'Model: '.$product->model,
            'Country of origin: '.($product->country?->name ?? ''),
        ])->merge(
            $product->specificationValues->map(
                fn ($value) => ($value->specification?->name ?? 'Specification').': '.$value->value,
            ),
        )->when(
            filled($product->description),
            fn ($lines) => $lines->push(trim($product->description)),
        )->push(self::COMPANY_MESSAGE);

        return [
            $product->product_code,
            $product->name,
            '', // product_name_mm: external source
            $product->brand,
            $product->category?->name ?? '',
            '', // sub_category_name: external source
            '', // product_group_name: external source
            '', // product_pattern_name: external source
            '', // product_type_name: external source
            '', // product_tags: external source
            $descriptionLines->filter(fn ($line) => filled(trim((string) $line)))->implode("\n"),
            '', // product_description_mm: external source
            $product->unit ?? '',
            '', // show_pro1_logo: external source
            '', // item_code: external source
            '', // item_name: external source
            '', // item_color: external source
            '', // item_sell_price: external source
            '', // item_member_price: external source
            $product->image ? basename($product->image) : '',
            '', // branch_code_list: external source
            '', // product_feature: external source
            $product->status?->name ?? '',
            '', // product_low_stock_qty: external source
            '', // product_custom_order: external source
            '', // product_custom_order_note: external source
            '', // upsell_list: external source
            $product->model,
            $product->country?->name ?? '',
            '', // product_custom_product_type: external source
            '', // product_weight: external source
            '', // product_length: external source
            '', // product_width: external source
            '', // product_height: external source
            '', // product_size: external source
            '', // additional_info_1: external source
            '', // additional_info_2: external source
            '', // additional_info_3: external source
            '', // additional_info_4: external source
            '', // additional_info_5: external source
            '', // product_shipping_class: external source
            '', // product_tax: external source
            '', // layer_1: external source
            '', // layer_2: external source
            '', // layer_3: external source
            '', // layer_4: external source
            '', // layer_5: external source
            '', // layer_6: external source
            '', // layer_7: external source
            '', // layer_8: external source
            '', // foc_status: external source
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set the row height for the first row (header row)
                foreach (range(1, 1) as $row) {
                    $rowHeightPx = 80;
                    $rowHeight = $rowHeightPx * 0.75; // Convert pixels to Excel row height scale
                    $sheet->getRowDimension($row)->setRowHeight($rowHeight);
                }

                // **Style Heading Row (Row 1)**
                $sheet->getStyle('A1:AY1')->applyFromArray([
                    'font' => [
                        'bold' => true,        // Bold text
                        // 'size' => 18,          // Font size
                        'color' => ['rgb' => 'FFFFFF'], // White font color
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F81BD'], // Blue background color
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER, // Center align text
                        'vertical' => Alignment::VERTICAL_CENTER, // Center vertically
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, // Thin border
                            'color' => ['rgb' => 'BFBFBF'], // Light gray border
                        ],
                    ],
                ]);

                $lastRow = $sheet->getHighestRow();
                if ($lastRow >= 2) {
                    $sheet->getStyle("K2:K{$lastRow}")->getAlignment()->setWrapText(true);
                    $sheet->getColumnDimension('K')->setAutoSize(false);
                    $sheet->getColumnDimension('K')->setWidth(80);

                    for ($row = 2; $row <= $lastRow; $row++) {
                        $sheet->getRowDimension($row)->setRowHeight(-1);
                    }
                }

            },
        ];
    }
}
