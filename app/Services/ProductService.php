<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductSpecificationValue;
use App\Models\ProductWorkflow;
use App\Models\Specification;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProductService
{
    private const DEFAULT_DESCRIPTION_MM = 'PRO 1 Global Home Center မှ အရည်အသွေးကောင်းမွန် သော ပစ္စည်းများကိုသာ ပစ္စည်းမှန်စျေးနှုန်းမှန်ကန်စွာ ရောင်းချသဖြင့် ယုံကြည်စိတ်ချစွာ ၀ယ်ယူနိုင်ပါသည်။';

    private const DEFAULT_DESCRIPTION_EN = 'Shop for variety of high quality products with reasonable price at PRO 1 Global, leading provider for construction and home improvement products.';

    public function create(array $data, $user = null): Product
    {
        return DB::transaction(function () use ($data, $user) {
            $workflow = Workflow::findOrFail($data['workflow_id']);
            $requiresOnlineDate = Str::contains(strtolower($workflow->slug), 'online');
            $description = trim((string) ($data['description'] ?? '')).self::DEFAULT_DESCRIPTION_MM;
            $descriptionEn = trim((string) ($data['description_en'] ?? '')).self::DEFAULT_DESCRIPTION_EN;

            $firstWorkflowStep = WorkflowStep::where(
                'workflow_id',
                $data['workflow_id']
            )
                ->orderBy('step_no')
                ->orderBy('id')
                ->firstOrFail();

            $product = Product::create([
                'product_code' => $data['product_code'],
                'brand' => $data['brand'],
                'name' => $data['name'],
                'model' => $data['model'] ?? '',
                'country_of_origin' => $data['country_of_origin'] ?? '',
                'website_url' => $data['website_url'] ?? '',
                'description' => filled($description) ? $description : self::DEFAULT_DESCRIPTION_MM,
                'description_en' => filled($descriptionEn) ? $descriptionEn : self::DEFAULT_DESCRIPTION_EN,
                'status_id' => $data['status_id'],
                'category_id' => $data['category_id'],
                'user_id' => $user?->id,
                'product_name' => $data['product_name'],
                'unit' => $data['unit'] ?? null,

                'online_date' => $requiresOnlineDate
                    ? Carbon::parse($data['online_date'])
                        ->startOfMonth()
                        ->toDateString()
                    : null,
            ]);

            ProductWorkflow::create([
                'product_id' => $product->id,
                'workflow_id' => $data['workflow_id'],
                'current_step_id' => $firstWorkflowStep->id,
                'status' => 'ongoing',
            ]);

            foreach ($data['specifications'] ?? [] as $row) {

                $specificationName = Str::of($row['name'])
                    ->squish()
                    ->toString();

                $specificationSlug = Str::slug(
                    $specificationName
                );

                $specification = Specification::firstOrCreate(
                    [
                        'slug' => $specificationSlug,
                    ],
                    [
                        'name' => $specificationName,
                        'status_id' => 3,
                        'user_id' => $user?->id,
                        'category_id' => $data['category_id'],
                    ]
                );

                ProductSpecificationValue::create([
                    'product_id' => $product->id,
                    'specification_id' => $specification->id,
                    'value' => $row['value'],
                ]);
            }

            $destinationUrl = route(
                'products.show',
                $product->id
            );

            $qrData = $this->generateQR(
                $destinationUrl,
                $product->product_code,
                'svg'
            );

            $product->update([
                'qr' => $qrData['path'],
                'qr_destination' => $destinationUrl,
            ]);

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
}
