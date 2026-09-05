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

}
