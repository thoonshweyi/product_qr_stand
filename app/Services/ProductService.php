<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductSpecificationValue;
use App\Models\ProductWorkflow;
use App\Models\Specification;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProductService
{
    private const DEFAULT_DESCRIPTION_MM = 'PRO 1 Global Home Center မှ အရည်အသွေးကောင်းမွန် သော ပစ္စည်းများကိုသာ ပစ္စည်းမှန်စျေးနှုန်းမှန်ကန်စွာ ရောင်းချသဖြင့် ယုံကြည်စိတ်ချစွာ ၀ယ်ယူနိုင်ပါသည်။';

    private const DEFAULT_DESCRIPTION_EN = 'Shop for variety of high quality products with reasonable price at PRO 1 Global, leading provider for construction and home improvement products.';

    public function create(array $data, $user = null): Product
    {
        return DB::transaction(function () use ($data, $user,) {
            // Start Workflow Config
            $workflowConfig = $this->getWorkflowConfig($data['workflow_id'] ?? null);

            $requiresOnlineDate = $workflowConfig['requiresOnlineDate'];
            // End Workflow Config



            $defaultDescriptionMm = self::DEFAULT_DESCRIPTION_MM;
            $defaultDescriptionEn = self::DEFAULT_DESCRIPTION_EN;

            $user = Auth::user();
            $user_id = $user->id;
            $firstWorkflowStep = WorkflowStep::where('workflow_id', $data['workflow_id'])
                ->orderBy('step_no')
                ->orderBy('id')
                ->firstOrFail();
            $description = trim((string) $data['description']).$defaultDescriptionMm;
            $descriptionEn = trim((string) $data['description_en']).$defaultDescriptionEn;

            $specificationRows = collect($data['specifications'])
                ->map(function ($row) {
                    return [
                        'name' => trim($row['name'] ?? ''),
                        'value' => trim($row['value'] ?? ''),
                    ];
                })
                ->filter(fn ($row) => $row['name'] !== '')
                ->values();


            $product = Product::create([
                'product_code' => $data['product_code'],
                'brand' => $data['brand'],
                'name' => $data['name'],
                'model' => $data['model'] ?? '',
                'country_of_origin' => $data['country_of_origin'] ?? '',
                'website_url' => $data['website_url'] ?? '',
                'description' => filled($description) ? $description : self::DEFAULT_DESCRIPTION_MM,
                'description_en' => filled($descriptionEn) ? $descriptionEn : self::DEFAULT_DESCRIPTION_EN,
                'status_id' => $data['status_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'user_id' => $user?->id,
                'product_name' => $data['product_name'],
                'online_date' => $requiresOnlineDate
                    ? Carbon::createFromFormat('Y-m-d', $data['online_date'])->startOfMonth()->toDateString()
                    : null,
            ]);


            $productWorkflow = new ProductWorkflow;
            $productWorkflow->product_id = $product->id;
            $productWorkflow->workflow_id = $data['workflow_id'];
            $productWorkflow->current_step_id = $firstWorkflowStep->id;
            $productWorkflow->status = 'ongoing';
            $productWorkflow->save();

            foreach ($specificationRows as $row) {
                $specificationName = Str::of($row['name'])->squish()->toString();
                $specificationSlug = Str::slug($specificationName);

                $specification = Specification::firstOrCreate(
                    ['slug' => $specificationSlug],
                    [
                        'name' => $specificationName,
                        'status_id' => 3,
                        'user_id' => $user?->id,
                        'category_id' => $data['category_id'] ?? '',
                    ]
                );

                ProductSpecificationValue::create([
                    'product_id' => $product->id,
                    'specification_id' => $specification->id,
                    'value' => $row['value'],
                ]);
            }

            // Start Single Image Upload
            
            // End Single Image Upload

            // Start Generate QR
            $destinationUrl = route('products.show', $product->id);
            $qrData = $this->generateQR($destinationUrl, $product->product_code, 'svg');

            $product->qr = $qrData['path'];
            $product->qr_destination = $destinationUrl;
            $product->save();

            return $product;
        });
    }

    public function generateQR(string $text, string $fileName, string $format = 'png'): array
    {
        if (! in_array($format, ['png', 'svg'], true)) {
            throw new \InvalidArgumentException('Unsupported QR code format.');
        }

        $qrCode = QrCode::format($format)->size(100)->generate($text);
        $safeFileName = basename($fileName).'.'.$format;
        $relativePath = 'assets/img/products/qrs/'.$safeFileName;
        $absolutePath = public_path($relativePath);

        if (! file_exists(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0755, true);
        }
        // Delete existing file if it exists
        if (file_exists($absolutePath)) {
            unlink($absolutePath);
        }

        file_put_contents($absolutePath, $qrCode);

        return [
            'format' => $format,
            'path' => $relativePath,
            'url' => asset($relativePath),
        ];
    }

    public function parseProductDescription(string $rawDescription): array
    {
        $lines = collect(preg_split('/\R/u', str_replace(["\r\n", "\r"], "\n", $rawDescription)))
            ->map(fn ($line) => $this->cleanDescriptionLine($line))
            ->filter(fn ($line) => filled($line))
            ->values();

        $attributes = [];
        $descriptionLines = [];
        $isReadingDescription = false;

        foreach ($lines as $line) {
            $matchesAttribute = preg_match('/^([^:：]{1,80})[:：]\s*(.*)$/u', $line, $matches);

            if ($matchesAttribute && ! $isReadingDescription) {
                $label = Str::squish($matches[1]);
                $value = Str::squish($matches[2]);

                if (filled($label) && filled($value)) {
                    $attributes[Str::snake(Str::lower($label))] = [
                        'label' => $label,
                        'value' => $value,
                    ];

                    continue;
                }
            }

            $isReadingDescription = true;
            $descriptionLines[] = $line;
        }

        return [
            'attributes' => $attributes,
            'description' => collect($descriptionLines)
                ->map(fn ($line) => Str::squish($line))
                ->filter(fn ($line) => filled($line))
                ->implode("\n"),
            'description_lines' => $descriptionLines,
        ];
    }
    private function cleanDescriptionLine(string $line): string
    {
        $line = html_entity_decode($line, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $line = str_replace("\xc2\xa0", ' ', $line);
        $line = preg_replace('/^[\s\p{So}\p{Pd}•●▪▫■□◆◇♦◊]+/u', '', $line) ?? $line;

        return Str::squish($line);
    }

    public function getSpecifications($specificationsArr) {
        $result = [];
        foreach ($specificationsArr as $name => $spec) {
            if (is_array($spec) && isset($spec['value'])) {
                $result[] = [
                    'name' => $name,
                    'value' => $spec['value'],
                ];
            }
        }
        return $result;
    }

    public function getWorkflowConfig(?int $workflowId): array
    {
        $selectedWorkflowSlug = Workflow::whereKey($workflowId)->value('slug');
        $requiresMainImage = Str::contains(strtolower((string) $selectedWorkflowSlug), 'stand');
        $requiresOnlineDate = Str::contains(strtolower((string) $selectedWorkflowSlug), 'online');
        $minimumOnlineDate = now()->startOfMonth()->toDateString();

        return [
            'selectedWorkflowSlug' => $selectedWorkflowSlug,
            'requiresMainImage' => $requiresMainImage,
            'requiresOnlineDate' => $requiresOnlineDate,
            'minimumOnlineDate' => $minimumOnlineDate,
        ];
    }

    public function search_products($productCodes){
        $productCodesString = implode("','", $productCodes);

        $conn = DB::connection('master_product');
        // $branch = Branch::whereId($branch_code)->first();

        $products = $conn->select("
            select product_grade_name as ProductType
                ,cat.remark as MainCategory
                --,coalesce(cat.remark,'N/A') as Main_Category
                ,coalesce(cat.product_category_code,'N/A') as Category,coalesce(cat.product_category_name,'-') as Category_Name
                ,coalesce(subcat.product_group_code,'N/A') as Group,coalesce(subcat.product_group_name,'-') as Group_Name
                ,coalesce(class.product_pattern_code,'N/A')as Pattern,coalesce(class.product_pattern_name,'-') as Pattern_Name
                ,coalesce(subclass.product_design_code,'N/A') as Design,coalesce(subclass.product_design_name,'-') as Design_Name
                ,barcode_code
                ,coalesce(regexp_replace(prod.product_name1, E'[\\n\\r]+',' ', 'g' ),'')as product_name
                ,product_unit_name as Unit
                ,product_brand_name as Brand
            from master_data.master_product prod 
                left join master_data.master_product_category cat on prod.product_category_id = cat.product_category_id
                left join master_data.master_product_group subcat on prod.product_group_id = subcat.product_group_id
                left join master_data.master_product_pattern class on prod.product_pattern_id = class.product_pattern_id -- class
                left join master_data.master_product_design subclass on prod.product_design_id = subclass.product_design_id -- sub-class
                left join master_data.master_product_multiunit mulunit on prod.product_id= mulunit.product_id and prod.product_code= mulunit.product_code
                left join master_data.master_product_unit unit on mulunit.product_unit_id= unit.product_unit_id
                left join master_data.master_product_brand bd on prod.product_brand_id= bd.product_brand_id
                left join  master_data.master_product_grade gd on prod.product_grade_id= gd.product_grade_id
                inner join master_data.master_product_barcode bar on prod.product_id= bar.product_id
                and mulunit.product_unit_id= bar.product_unit_id
            where prod.inactive = 'A'
            and prod.product_code in ('$productCodesString')
        ");
        // dd($products);

        return $products;
    }

}
