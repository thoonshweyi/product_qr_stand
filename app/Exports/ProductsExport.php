<?php

namespace App\Exports;

use App\Models\Branch;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProductsExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithMapping, WithColumnWidths
{
    private const COMPANY_MESSAGE = 'PRO 1 Global Home Center မှ အရည်အသွေးကောင်းမွန် သော ပစ္စည်းများကိုသာ ပစ္စည်းမှန်စျေးနှုန်းမှန်ကန်စွာ ရောင်းချသဖြင့် ယုံကြည်စိတ်ချစွာ ၀ယ်ယူနိုင်ပါသည်။';
    private const DEFAULT_DESCRIPTION_MM = 'PRO 1 Global Home Center မှ အရည်အသွေးကောင်းမွန် သော ပစ္စည်းများကိုသာ ပစ္စည်းမှန်စျေးနှုန်းမှန်ကန်စွာ ရောင်းချသဖြင့် ယုံကြည်စိတ်ချစွာ ၀ယ်ယူနိုင်ပါသည်။';
    private const DEFAULT_DESCRIPTION_EN = 'Shop for variety of high quality products with reasonable price at PRO 1 Global, leading provider for construction and home improvement products.';

    public function __construct(private readonly Collection $preproducts) {}

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->preproducts;
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

    public function map($preproducts): array
    {
        $product = $preproducts['product'];
        $onlinedata = $preproducts['onlinedata'];
        $specifications = $product->specificationValues->mapWithKeys(
            fn ($value) => [
                Str::lower(Str::squish((string) $value->specification?->name)) => $value->value,
            ],
        );

        $descriptionLines = collect([
            'Brand: '.$product->brand,
            'Name: '.$product->name,
            'Model: '.$product->model,
            'Country of origin: '.($product->country?->name ?? ''),
        ])->merge(
            $product->specificationValues->map(
                fn ($value) => ($value->specification?->name ?? 'Specification').': '.$value->value,
            ),
        );

        $descriptionMm = collect($descriptionLines->all())
            ->when(
                filled($product->description),
                fn ($lines) => $lines->push(trim($product->description)),
                fn ($lines) => $lines->push(self::DEFAULT_DESCRIPTION_MM)
            )
            ->filter(fn ($line) => filled(trim((string) $line)))->implode("\n");

        $descriptionEn = collect($descriptionLines->all())
            ->when(
                filled($product->description_en),
                fn ($lines) => $lines->push(trim($product->description_en)),
                fn ($lines) => $lines->push(self::DEFAULT_DESCRIPTION_EN)
            )
            ->filter(fn ($line) => filled(trim((string) $line)))->implode("\n");

        $branchCodes = Branch::query()
            ->orderBy('branch_code', 'asc')
            ->where('status_id', '3')
            ->pluck('branch_code')
            ->filter((fn ($code) => filled($code) && ! in_array($code, ['MM-001', 'MM-112', 'MM-201', 'MM-205'])))
            ->values();
        $branchCodesString = $branchCodes->implode(',');
        // dd($branchCodesString);
        // MM-101,MM-102,MM-103,MM-104,MM-105,MM-106,MM-107,MM-108,MM-109,MM-110,MM-113,MM-114,MM-115

        return [
            $onlinedata->product_code,
            $onlinedata->product_name,
            $onlinedata->product_name,
            $onlinedata->brand_name,
            $onlinedata->category_name,
            $onlinedata->sub_category_name, // sub_category_name: online data
            $onlinedata->product_group_name, // product_group_name: online data
            $onlinedata->product_pattern_name, // product_pattern_name: online data
            $onlinedata->product_type_name, // product_type_name: online data
            '-', // product_tags: fill by online
            $descriptionEn,
            $descriptionMm,
            $onlinedata->unit,
            '', // show_pro1_logo: online data
            $onlinedata->item_code, // item_code: online data
            $onlinedata->item_name, // item_name: online data
            '', // item_color: online data
            $onlinedata->item_sell_price, // item_sell_price: online data
            $onlinedata->item_member_price, // item_member_price: online data
            $onlinedata->item_code.'.jpg', // image_name: online data
            $branchCodesString, // branch_code_list: online data
            'Yes', // product_feature:
            'Active', // status:
            '0', // product_low_stock_qty:
            '', // product_custom_order:
            '', // product_custom_order_note:
            '-', // upsell_list:
            $product->model,
            $product->country?->name ?? '',
            '', // product_custom_product_type:
            '', //$specifications->get('weight', ''), // product_weight:
            '', //$specifications->get('length', ''), // product_length:
            '', //$specifications->get('width', ''), // product_width:
            '', //$specifications->get('height', ''), // product_height:
            '', //$specifications->get('size', ''), // product_size:
            '', // additional_info_1:
            '', // additional_info_2:
            '', // additional_info_3:
            '', // additional_info_4:
            '', // additional_info_5:
            '', // product_shipping_class:
            '', // product_tax:
            '', // layer_1:
            '', // layer_2:
            '', // layer_3:
            '', // layer_4:
            '', // layer_5:
            '', // layer_6:
            '', // layer_7:
            '', // layer_8:
            '', // foc_status:
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set the row height for the first row (header row)
                // dd(count($this->preproducts));
                foreach (range(1, count($this->preproducts)+1) as $row) {
                    $rowHeightPx = 80;
                    $rowHeight = $rowHeightPx * 0.75; // Convert pixels to Excel row height scale
                    $sheet->getRowDimension($row)->setRowHeight($rowHeight);
                }


                $sheet->getStyle('A1:AY' . count($this->preproducts) + 1)
                ->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // **Style Heading Row (Row 1)**
                $sheet->getStyle('A1:AY1')->applyFromArray([
                    'font' => [
                        'bold' => true,        // Bold text
                        // 'size' => 18,          // Font size
                        'color' => ['rgb' => '000000'], // White font color
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '92d050'], // Blue background color
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER, // Center align text
                        'vertical' => Alignment::VERTICAL_CENTER, // Center vertically
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, // Thin border
                            'color' => ['rgb' => '000000'], // Light gray border
                        ],
                    ],
                ]);

            

            },
        ];
    }

    public function columnWidths(): array
    {
        $columnWidthPx = 300;
        $columnWidth = $columnWidthPx / 7;
        return [
            'K' => $columnWidth,
            'L' => $columnWidth,
        ];
    }
}
