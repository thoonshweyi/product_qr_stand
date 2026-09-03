<?php

namespace App\Imports;

use App\Exceptions\ExcelImportValidationException;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSpecificationValue;
use App\Models\ProductWorkflow;
use App\Models\Specification;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProductImport implements ToCollection, WithHeadingRow
{
    private const SPECIFICATION_COLUMNS = [
        'weight' => 'Weight',
        'length' => 'Length',
        'width' => 'Width',
        'height' => 'Height',
        'size' => 'Size',
    ];

    private array $importedProductCodes = [];

    private int $importedCount = 0;

    public function __construct(private readonly int $userId) {}

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $row = $this->normalizeRow($row->toArray());

            if ($this->isEmptyRow($row)) {
                continue;
            }

            $this->validateRow($row, $rowNumber);
            $this->createProduct($row);
            $this->importedProductCodes[] = $row['product_code'];
            $this->importedCount++;
        }
    }

    public function importedCount(): int
    {
        return $this->importedCount;
    }

    private function validateRow(array $row, int $rowNumber): void
    {
        $validator = Validator::make($row, [
            'product_code' => ['required', 'string', 'max:255', 'unique:products,product_code'],
            'product_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'country_of_origin' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'exists:categories,name'],
            'workflow' => ['required', 'string', 'exists:workflows,slug'],
            'unit' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'description_en' => ['nullable', 'string', 'max:2000'],
            'online_date' => ['nullable', 'date'],
            'weight' => ['nullable', 'string', 'max:255'],
            'length' => ['nullable', 'string', 'max:255'],
            'width' => ['nullable', 'string', 'max:255'],
            'height' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:255'],
        ]);

        $validator->after(function ($validator) use ($row) {
            if (in_array($row['product_code'] ?? null, $this->importedProductCodes, true)) {
                $validator->errors()->add('product_code', 'The product code is duplicated in this Excel file.');
            }

            $workflow = Workflow::where('slug', $row['workflow'] ?? null)->first();
            if ($workflow && ! WorkflowStep::where('workflow_id', $workflow->id)->exists()) {
                $validator->errors()->add('workflow', 'The selected workflow does not have any steps.');
            }
        });

        if ($validator->fails()) {
            throw new ExcelImportValidationException($validator->errors()->toArray(), $rowNumber);
        }
    }

    private function createProduct(array $row): void
    {
        $workflow = Workflow::where('slug', $row['workflow'])->firstOrFail();
        $firstWorkflowStep = WorkflowStep::where('workflow_id', $workflow->id)
            ->orderBy('step_no')
            ->orderBy('id')
            ->firstOrFail();
        $category = Category::where('name', $row['category'])->firstOrFail();

        $product = Product::create([
            'product_code' => $row['product_code'],
            'product_name' => $row['product_name'],
            'name' => $row['name'],
            'brand' => $row['brand'],
            'model' => $row['model'],
            'country_of_origin' => $row['country_of_origin'],
            'category_id' => $category->id,
            'unit' => $row['unit'] ?? null,
            'description' => $row['description'] ?? null,
            'description_en' => $row['description_en'] ?? null,
            'status_id' => 1,
            'workflow_id' => $workflow->id,
            'stage' => 'ongoing',
            'online_date' => filled($row['online_date'] ?? null)
                ? Carbon::parse($row['online_date'])->startOfMonth()->toDateString()
                : null,
            'user_id' => $this->userId,
        ]);

        ProductWorkflow::create([
            'product_id' => $product->id,
            'workflow_id' => $workflow->id,
            'current_step_id' => $firstWorkflowStep->id,
            'status' => 'ongoing',
        ]);

        $destinationUrl = route('products.show', $product->id);
        $product->update([
            'qr_destination' => $destinationUrl,
            'qr' => $this->generateQr($destinationUrl, $product->product_code),
        ]);

        foreach (self::SPECIFICATION_COLUMNS as $column => $name) {
            if (! filled($row[$column] ?? null)) {
                continue;
            }

            $specification = Specification::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'status_id' => 3,
                    'user_id' => $this->userId,
                    'category_id' => $category->id,
                ]
            );

            ProductSpecificationValue::create([
                'product_id' => $product->id,
                'specification_id' => $specification->id,
                'value' => $row[$column],
            ]);
        }
    }

    private function normalizeRow(array $row): array
    {
        return collect($row)
            ->mapWithKeys(function ($value, $key) {
                $key = Str::snake(Str::lower(Str::squish((string) $key)));

                if ($key === 'online_date' && is_numeric($value)) {
                    $value = Carbon::instance(Date::excelToDateTimeObject($value))->toDateString();
                }

                return [$key => is_string($value) ? Str::squish($value) : $value];
            })
            ->all();
    }

    private function isEmptyRow(array $row): bool
    {
        return collect($row)
            ->filter(fn ($value) => filled($value))
            ->isEmpty();
    }

    private function generateQr(string $destinationUrl, string $productCode): string
    {
        $relativePath = 'assets/img/products/qrs/'.basename($productCode).'.svg';
        $absolutePath = public_path($relativePath);

        if (! file_exists(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0755, true);
        }

        file_put_contents($absolutePath, QrCode::format('svg')->size(100)->generate($destinationUrl));

        return $relativePath;
    }
}
